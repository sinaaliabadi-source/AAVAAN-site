<?php

namespace Tests\Feature;

use App\Models\ArtistProfile;
use App\Models\ProductionAccessLog;
use App\Models\Subscription;
use App\Models\SystemSetting;
use App\Models\User;
use App\Support\Festival;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * جشنوارهٔ افتتاح آوان — عضویت و استفادهٔ رایگان تا پایان تابستان.
 */
class FestivalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Mail::fake();
        // اطمینان از فعال بودن جشنواره در این تست‌ها (کلید از migration درج شده است).
        SystemSetting::set('festival_active', '1');
        SystemSetting::set('festival_ends_at', '2026-09-22');
        Festival::forgetCache();
    }

    private function makeArtist(bool $blueTick = false, ?string $name = null): ArtistProfile
    {
        static $n = 0;
        $n++;
        $user = User::create([
            'name'              => $name ?? "هنرمند {$n}",
            'email'             => "fartist{$n}@example.com",
            'password'          => 'password',
            'role'              => 'artist',
            'email_verified_at' => now(), // هنرمند تأییدشده
        ]);

        return ArtistProfile::create([
            'user_id'              => $user->id,
            'username'             => "fartist-{$n}",
            'field'                => 'بازیگری و اجرا',
            'city'                 => 'تهران',
            'is_active'            => true,
            'has_blue_tick'        => $blueTick,
            'blue_tick_granted_at' => $blueTick ? now() : null,
        ]);
    }

    private function approvedTeam(): User
    {
        static $m = 0;
        $m++;
        return User::create([
            'name'            => 'تیم تولید تأییدشده',
            'email'           => "fteam{$m}@example.com",
            'password'        => 'password',
            'role'            => 'production',
            'approval_status' => 'approved',
        ]);
    }

    /**
     * ثبت‌نام هنرمند در جشنواره → هنوز اشتراکی ساخته نمی‌شود (تا تأیید ایمیل)؛
     * پس از کلیک لینک تأیید → حساب فعال + اشتراک festival خودکار ساخته می‌شود.
     */
    public function test_festival_subscription_created_after_email_verification(): void
    {
        $this->post(route('auth.register'), [
            'name'                  => 'هنرمند تازه',
            'email'                 => 'newartist@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'artist',
            'field'                 => 'بازیگری و اجرا',
        ])->assertRedirect(route('verification.notice'));

        $user = User::where('email', 'newartist@example.com')->first();
        $this->assertNotNull($user);
        $this->assertFalse($user->hasVerifiedEmail(), 'هنرمند باید تأییدنشده باشد.');

        // پیش از تأیید هیچ اشتراکی ساخته نشده است.
        $this->assertSame(0, Subscription::where('user_id', $user->id)->count());

        // شبیه‌سازی کلیک لینک تأیید (URL امضاشدهٔ استاندارد Laravel).
        $verifyUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );
        $this->actingAs($user)->get($verifyUrl)->assertRedirect(route('artist.dashboard'));

        $this->assertTrue($user->fresh()->hasVerifiedEmail());

        $sub = Subscription::where('user_id', $user->id)->first();
        $this->assertNotNull($sub, 'اشتراک جشنواره باید پس از تأیید ساخته می‌شد.');
        $this->assertSame('festival', $sub->plan);
        $this->assertSame('active', $sub->status);
        $this->assertTrue($sub->isActive());

        // پرداختِ رایگانِ ثبتی (مبلغ صفر، منبع دستی).
        $this->assertNotNull($sub->payment);
        $this->assertSame(0, $sub->payment->amount);
        $this->assertTrue($sub->payment->isManual());
    }

    /** اجرای دوبارهٔ festival:grant اشتراک تکراری نمی‌سازد. */
    public function test_festival_grant_is_idempotent(): void
    {
        // سه هنرمند بدون اشتراک (کاربر مستقیم می‌سازیم تا هوک ثبت‌نام دخالت نکند).
        foreach (range(1, 3) as $i) {
            $u = User::create([
                'name' => "هنرمند گرنت {$i}", 'email' => "grant{$i}@example.com",
                'password' => 'password', 'role' => 'artist',
            ]);
            ArtistProfile::create(['user_id' => $u->id, 'field' => 'بازیگری', 'username' => "grant-{$i}"]);
        }

        Artisan::call('festival:grant');
        $afterFirst = Subscription::where('plan', 'festival')->count();
        $this->assertSame(3, $afterFirst);

        Artisan::call('festival:grant');
        $afterSecond = Subscription::where('plan', 'festival')->count();
        $this->assertSame(3, $afterSecond, 'اجرای دوباره نباید اشتراک تکراری بسازد.');
    }

    /** unlock تیم approved در جشنواره → بدون کسر اعتبار + ثبت لاگ دسترسی. */
    public function test_approved_team_unlock_in_festival_is_free_and_logged(): void
    {
        $team   = $this->approvedTeam();
        $artist = $this->makeArtist();

        $this->actingAs($team)->post(route('production.access.unlock'), [
            'artist_profile_id' => $artist->id,
        ])->assertRedirect(route('profile.show', $artist->username));

        // لاگ دسترسی ثبت شده (برای آمار) ولی بدون بستهٔ اعتبار.
        $log = ProductionAccessLog::where('production_user_id', $team->id)
            ->where('artist_profile_id', $artist->id)
            ->first();
        $this->assertNotNull($log);
        $this->assertNull($log->production_access_id);

        // هیچ بستهٔ اعتباری لازم نبود و ساخته نشد.
        $this->assertNull($team->availableProductionAccess());
    }

    /** unlock تیم تأییدنشده → همچنان مسدود (توسط middleware تأیید). */
    public function test_unapproved_team_unlock_is_blocked_even_in_festival(): void
    {
        $team = User::create([
            'name' => 'تیم منتظر', 'email' => 'pending@example.com',
            'password' => 'password', 'role' => 'production', 'approval_status' => 'pending',
        ]);
        $artist = $this->makeArtist();

        $this->actingAs($team)->post(route('production.access.unlock'), [
            'artist_profile_id' => $artist->id,
        ])->assertRedirect(route('production.pending-approval'));

        $this->assertSame(0, ProductionAccessLog::where('production_user_id', $team->id)->count());
    }

    /** خاموش کردن festival_active → unlock نیازمند اعتبار می‌شود (رفتار عادی برمی‌گردد). */
    public function test_disabling_festival_restores_credit_requirement(): void
    {
        SystemSetting::set('festival_active', '0');
        Festival::forgetCache();

        $team   = $this->approvedTeam();
        $artist = $this->makeArtist();

        // بدون اعتبار → به صفحهٔ خرید با خطا هدایت می‌شود و لاگی ثبت نمی‌شود.
        $this->actingAs($team)->post(route('production.access.unlock'), [
            'artist_profile_id' => $artist->id,
        ])->assertRedirect(route('production.access'));

        $this->assertSame(0, ProductionAccessLog::where('production_user_id', $team->id)->count());
    }

    /** صفحهٔ اشتراک هنرمند در جشنواره پیام جشن را نشان می‌دهد. */
    public function test_artist_subscription_page_shows_festival_message(): void
    {
        $artist = $this->makeArtist();

        $html = $this->actingAs($artist->user)
            ->get(route('artist.subscription'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('عضویت شما تا پایان تابستان رایگان است', $html);
    }

    /** صفحهٔ خرید اعتبار تیم تولید در جشنواره بنر جشنواره را نشان می‌دهد. */
    public function test_production_access_page_shows_festival_banner(): void
    {
        $html = $this->actingAs($this->approvedTeam())
            ->get(route('production.access'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('جشنوارهٔ آغاز', $html);
    }
}
