<?php

namespace Tests\Feature;

use App\Models\ArtistProfile;
use App\Models\Payment;
use App\Models\ProductionAccess;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * فاز ادمین: افزودن کاربر، گردش‌کار تأیید تیم تولید، اشتراک/اعتبار دستی.
 */
class AdminPhaseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Mail::fake();
    }

    private function admin(): User
    {
        return User::create([
            'name' => 'ادمین', 'email' => 'admin'.rand(1, 99999).'@x.com',
            'password' => 'password', 'role' => 'admin', 'approval_status' => 'approved',
        ]);
    }

    private function artist(array $overrides = []): User
    {
        $u = User::create(array_merge([
            'name' => 'هنرمند', 'email' => 'artist'.rand(1, 99999).'@x.com',
            'password' => 'password', 'role' => 'artist',
        ], $overrides));
        ArtistProfile::create(['user_id' => $u->id, 'field' => 'بازیگری', 'is_active' => true, 'username' => 'u'.$u->id]);
        return $u;
    }

    // ─── ب) گردش‌کار تأیید ───────────────────────────────────────────

    public function test_production_registration_is_pending_and_redirects(): void
    {
        $res = $this->post(route('auth.register'), [
            'name' => 'تیم نو', 'email' => 'newprod@x.com',
            'password' => 'password123', 'password_confirmation' => 'password123',
            'role' => 'production',
        ]);
        $res->assertRedirect(route('production.pending-approval'));

        $user = User::where('email', 'newprod@x.com')->first();
        $this->assertEquals('pending', $user->approval_status);
    }

    public function test_pending_production_blocked_from_search(): void
    {
        $prod = User::create([
            'name' => 'تیم', 'email' => 'p@x.com', 'password' => 'password',
            'role' => 'production', 'approval_status' => 'pending',
        ]);

        $this->actingAs($prod)->get(route('production.search'))
            ->assertRedirect(route('production.pending-approval'));
    }

    public function test_admin_approve_grants_access(): void
    {
        $admin = $this->admin();
        $prod  = User::create([
            'name' => 'تیم', 'email' => 'p2@x.com', 'password' => 'password',
            'role' => 'production', 'approval_status' => 'pending',
        ]);

        $this->actingAs($admin)->post(route('admin.production.approve', $prod->id))->assertRedirect();
        $prod->refresh();
        $this->assertEquals('approved', $prod->approval_status);
        $this->assertEquals($admin->id, $prod->approved_by);
        Mail::assertSent(\App\Mail\ProductionApprovedMail::class);

        // اکنون به جستجو دسترسی دارد
        $this->actingAs($prod)->get(route('production.search'))->assertOk();
    }

    public function test_reject_requires_reason_and_shows_it(): void
    {
        $admin = $this->admin();
        $prod  = User::create([
            'name' => 'تیم', 'email' => 'p3@x.com', 'password' => 'password',
            'role' => 'production', 'approval_status' => 'pending',
        ]);

        // بدون دلیل → خطای اعتبارسنجی
        $this->actingAs($admin)->post(route('admin.production.reject', $prod->id), [])
            ->assertSessionHasErrors('rejection_reason');

        // با دلیل
        $this->actingAs($admin)->post(route('admin.production.reject', $prod->id), [
            'rejection_reason' => 'مدارک ناقص است.',
        ])->assertRedirect();
        $prod->refresh();
        $this->assertEquals('rejected', $prod->approval_status);
        Mail::assertSent(\App\Mail\ProductionRejectedMail::class);

        // دلیل در صفحهٔ وضعیت نمایش داده می‌شود
        $html = $this->actingAs($prod)->get(route('production.pending-approval'))->assertOk()->getContent();
        $this->assertStringContainsString('مدارک ناقص است.', $html);
    }

    public function test_legacy_production_user_keeps_access(): void
    {
        // کاربر قدیمی: بدون تعیین approval_status → پیش‌فرض ستون 'approved'
        User::create([
            'name' => 'قدیمی', 'email' => 'legacy@x.com', 'password' => 'password', 'role' => 'production',
        ]);
        // بارگذاری مجدد از دیتابیس تا مقدار پیش‌فرض ستون (approved) روی مدل بنشیند.
        $legacy = User::where('email', 'legacy@x.com')->first();
        $this->assertEquals('approved', $legacy->approval_status);
        $this->actingAs($legacy)->get(route('production.search'))->assertOk();
    }

    // ─── الف) افزودن کاربر توسط ادمین ────────────────────────────────

    public function test_admin_creates_artist_with_generated_username_and_login(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'علی احمدی', 'email' => 'ali@x.com',
            'role' => 'artist', 'field' => 'بازیگری و اجرا', 'city' => 'تهران',
            'password' => 'secret12345', 'password_confirmation' => 'secret12345',
        ])->assertRedirect(route('admin.users.index'));

        $user = User::where('email', 'ali@x.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('artist', $user->role);
        $this->assertNotNull($user->artistProfile);
        $this->assertNotEmpty($user->artistProfile->username);

        // رمز تعیین‌شده درست ذخیره شده و امکان ورود می‌دهد.
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('secret12345', $user->password));

        // ورود واقعی از حالت مهمان (خروج از حساب ادمینِ تست) نیز به داشبورد هنرمند می‌رسد.
        auth()->logout();
        $this->post(route('auth.login'), ['email' => 'ali@x.com', 'password' => 'secret12345'])
            ->assertRedirect(route('artist.dashboard'));
    }

    public function test_admin_created_production_is_approved(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'تیم دستی', 'email' => 'teamm@x.com', 'role' => 'production',
            'password' => 'secret12345', 'password_confirmation' => 'secret12345',
        ])->assertRedirect();

        $user = User::where('email', 'teamm@x.com')->first();
        $this->assertEquals('approved', $user->approval_status);
    }

    public function test_admin_role_requires_confirmation(): void
    {
        $admin = $this->admin();
        // بدون تیک تأیید → خطا
        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'ادمین نو', 'email' => 'adm2@x.com', 'role' => 'admin',
            'password' => 'secret12345', 'password_confirmation' => 'secret12345',
        ])->assertSessionHasErrors('admin_confirm');
    }

    // ─── ج) اشتراک و اعتبار دستی ─────────────────────────────────────

    public function test_manual_subscription_renew_keeps_active_and_starts_at_end(): void
    {
        $admin  = $this->admin();
        $artist = $this->artist();

        $active = Subscription::create([
            'user_id' => $artist->id, 'plan' => 'monthly', 'status' => 'active',
            'starts_at' => now()->subDays(5), 'expires_at' => now()->addDays(10),
        ]);

        $this->actingAs($admin)->post(route('admin.subscriptions.store'), [
            'user_id' => $artist->id, 'plan' => 'monthly', 'active_action' => 'renew', 'amount' => 0,
        ])->assertRedirect(route('admin.subscriptions.index'));

        // اشتراک فعلی همچنان فعال است
        $this->assertEquals('active', $active->fresh()->status);
        // اشتراک جدید از انتهای اشتراک فعلی شروع می‌شود
        $new = Subscription::where('user_id', $artist->id)->latest('id')->first();
        $this->assertEquals($active->expires_at->toDateString(), $new->starts_at->toDateString());
        // پرداخت دستی متصل ثبت شده
        $this->assertTrue($new->payment->isManual());
    }

    public function test_manual_subscription_replace_expires_old(): void
    {
        $admin  = $this->admin();
        $artist = $this->artist();

        $active = Subscription::create([
            'user_id' => $artist->id, 'plan' => 'monthly', 'status' => 'active',
            'starts_at' => now()->subDays(5), 'expires_at' => now()->addDays(10),
        ]);

        $this->actingAs($admin)->post(route('admin.subscriptions.store'), [
            'user_id' => $artist->id, 'plan' => 'yearly', 'active_action' => 'replace',
        ])->assertRedirect();

        $this->assertEquals('expired', $active->fresh()->status);
    }

    public function test_manual_credit_added_and_usable(): void
    {
        $admin = $this->admin();
        $team  = User::create([
            'name' => 'تیم', 'email' => 'pcred@x.com', 'password' => 'password',
            'role' => 'production', 'approval_status' => 'approved',
        ]);

        $this->actingAs($admin)->post(route('admin.production.add-credit', $team->id), [
            'bundle_size' => 5, 'amount' => 0, 'admin_note' => 'هدیه',
        ])->assertRedirect();

        $access = ProductionAccess::where('user_id', $team->id)->first();
        $this->assertEquals('manual', $access->access_type);
        $this->assertEquals(5, $access->bundle_size);
        $this->assertTrue($access->payment->isManual());

        // اعتبار قابل استفاده است
        $this->assertNotNull($team->availableProductionAccess());
        $this->assertEquals(5, $team->availableProductionAccess()->remainingCredits());

        // مصرف یکی از طریق unlock کست‌یاب
        $artist = $this->artist();
        $this->actingAs($team)->post(route('production.access.unlock'), [
            'artist_profile_id' => $artist->artistProfile->id,
        ]);
        $this->assertEquals(4, $team->fresh()->availableProductionAccess()->remainingCredits());
    }

    public function test_add_credit_blocked_for_unapproved_team(): void
    {
        $admin = $this->admin();
        $team  = User::create([
            'name' => 'تیم', 'email' => 'pnp@x.com', 'password' => 'password',
            'role' => 'production', 'approval_status' => 'pending',
        ]);

        $this->actingAs($admin)->post(route('admin.production.add-credit', $team->id), [
            'bundle_size' => 5,
        ])->assertSessionHas('error');
        $this->assertEquals(0, ProductionAccess::where('user_id', $team->id)->count());
    }
}
