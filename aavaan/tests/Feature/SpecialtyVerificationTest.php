<?php

namespace Tests\Feature;

use App\Models\ArtistProfile;
use App\Models\ArtistSpecialty;
use App\Models\SpecialtyCategory;
use App\Models\User;
use App\Models\Verification;
use App\Services\SpecialtyAttributeIndexer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * خط تولید تأیید تخصص (Specialty Verification).
 */
class SpecialtyVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Mail::fake();
        $this->seed(\Database\Seeders\SpecialtyCategoriesSeeder::class);
        $this->seed(\Database\Seeders\SpecialtyAttributeDefinitionsSeeder::class);
        $this->seed(\Database\Seeders\SpecialtySubcategoriesSeeder::class);
    }

    private function leaf(): SpecialtyCategory
    {
        return SpecialtyCategory::where('parent_id', 1)->orderBy('id')->firstOrFail();
    }

    private function artistWithSpecialty(array $attrs = [], ?int $catId = null): array
    {
        static $n = 0; $n++;
        $user = User::create([
            'name' => "هنرمند {$n}", 'email' => "art{$n}@x.com",
            'password' => 'password', 'role' => 'artist',
        ]);
        ArtistProfile::create(['user_id' => $user->id, 'field' => 'بازیگری', 'is_active' => true, 'username' => "art-{$n}"]);
        $spec = ArtistSpecialty::create([
            'user_id' => $user->id, 'category_id' => $catId ?? $this->leaf()->id,
            'is_primary' => true, 'attributes' => $attrs,
        ]);
        app(SpecialtyAttributeIndexer::class)->sync($spec);
        return [$user, $spec];
    }

    private function admin(): User
    {
        return User::create(['name' => 'ادمین', 'email' => 'adm'.rand(1,99999).'@x.com', 'password' => 'password', 'role' => 'admin']);
    }

    public function test_artist_requests_verification(): void
    {
        [$user, $spec] = $this->artistWithSpecialty();

        $this->actingAs($user)->post(route('artist.specialties.verify', $spec->id), [
            'artist_note'    => 'ده سال سابقه دارم.',
            'evidence_links' => ['https://example.com/a', ''],
        ])->assertRedirect();

        $v = Verification::first();
        $this->assertNotNull($v);
        $this->assertEquals('specialty', $v->type);
        $this->assertEquals('pending', $v->status);
        $this->assertEquals($spec->id, $v->artist_specialty_id);
        $this->assertEquals(['https://example.com/a'], $v->evidence_links);
    }

    public function test_two_pending_requests_blocked(): void
    {
        [$user, $spec] = $this->artistWithSpecialty();
        Verification::create([
            'user_id' => $user->id, 'artist_specialty_id' => $spec->id,
            'type' => 'specialty', 'status' => 'pending', 'artist_note' => 'x',
        ]);

        $this->actingAs($user)->post(route('artist.specialties.verify', $spec->id), [
            'artist_note' => 'دوباره',
        ])->assertSessionHas('error');

        $this->assertEquals(1, Verification::count());
    }

    public function test_resend_allowed_after_reject(): void
    {
        [$user, $spec] = $this->artistWithSpecialty();
        Verification::create([
            'user_id' => $user->id, 'artist_specialty_id' => $spec->id,
            'type' => 'specialty', 'status' => 'rejected', 'artist_note' => 'x', 'notes' => 'ناقص',
        ]);

        $this->actingAs($user)->post(route('artist.specialties.verify', $spec->id), [
            'artist_note' => 'اصلاح شد',
        ])->assertRedirect()->assertSessionHas('success');

        $this->assertEquals(2, Verification::count());
        $this->assertEquals(1, Verification::where('status', 'pending')->count());
    }

    public function test_daily_throttle(): void
    {
        [$user, $spec] = $this->artistWithSpecialty();
        // ۵ درخواست امروز (artist_specialty_id=null تا با قانون pendingِ همین تخصص تداخل نکند)
        for ($i = 0; $i < 5; $i++) {
            Verification::create([
                'user_id' => $user->id, 'artist_specialty_id' => null,
                'type' => 'specialty', 'status' => 'rejected', 'artist_note' => 'x',
            ]);
        }
        // درخواست ششم روی تخصص موجودِ همین کاربر → باید با throttle مسدود شود.
        $this->actingAs($user)->post(route('artist.specialties.verify', $spec->id), ['artist_note' => 'y'])
            ->assertSessionHas('error');

        $this->assertEquals(0, Verification::where('artist_specialty_id', $spec->id)->count());
    }

    public function test_admin_approve_marks_verified_and_emails(): void
    {
        [$user, $spec] = $this->artistWithSpecialty();
        $v = Verification::create([
            'user_id' => $user->id, 'artist_specialty_id' => $spec->id,
            'type' => 'specialty', 'status' => 'pending', 'artist_note' => 'x',
        ]);

        $this->actingAs($this->admin())->post(route('admin.verifications.approve', $v->id), ['notes' => 'خوب بود'])
            ->assertRedirect();

        $this->assertEquals('approved', $v->fresh()->status);
        $this->assertTrue($spec->fresh()->isVerified());
        Mail::assertSent(\App\Mail\SpecialtyVerificationApprovedMail::class);
    }

    public function test_admin_reject_requires_notes(): void
    {
        [$user, $spec] = $this->artistWithSpecialty();
        $v = Verification::create([
            'user_id' => $user->id, 'artist_specialty_id' => $spec->id,
            'type' => 'specialty', 'status' => 'pending', 'artist_note' => 'x',
        ]);

        $admin = $this->admin();
        $this->actingAs($admin)->post(route('admin.verifications.reject', $v->id), [])
            ->assertSessionHasErrors('notes');

        $this->actingAs($admin)->post(route('admin.verifications.reject', $v->id), ['notes' => 'مدرک ناکافی'])
            ->assertRedirect();
        $this->assertEquals('rejected', $v->fresh()->status);
        Mail::assertSent(\App\Mail\SpecialtyVerificationRejectedMail::class);
    }

    public function test_verified_badge_on_public_profile(): void
    {
        [$user, $spec] = $this->artistWithSpecialty();
        Verification::create([
            'user_id' => $user->id, 'artist_specialty_id' => $spec->id,
            'type' => 'specialty', 'status' => 'approved', 'artist_note' => 'x',
        ]);

        $html = $this->get(route('profile.show', $user->artistProfile->username))->assertOk()->getContent();
        $this->assertStringContainsString('تخصص تأییدشده', $html);
    }

    public function test_cast_finder_verified_only_filter_with_number(): void
    {
        $leaf = $this->leaf();
        // مطابق: قد ۱۷۰ + تأییدشده
        [$u1, $s1] = $this->artistWithSpecialty(['height_cm' => 170], $leaf->id);
        Verification::create(['user_id' => $u1->id, 'artist_specialty_id' => $s1->id, 'type' => 'specialty', 'status' => 'approved', 'artist_note' => 'x']);
        // قد مطابق ولی تأییدنشده → باید حذف شود
        $this->artistWithSpecialty(['height_cm' => 172], $leaf->id);

        $prod = User::create(['name' => 'تیم', 'email' => 'p@x.com', 'password' => 'password', 'role' => 'production', 'approval_status' => 'approved']);

        $res = $this->actingAs($prod)->get(route('production.search', [
            'category_id'   => $leaf->id,
            'verified_only' => '1',
            'attr'          => ['height_cm' => ['min' => 160, 'max' => 175]],
        ]))->assertOk();

        $ids = $res->viewData('artists')->pluck('id')->all();
        $this->assertEquals([$u1->artistProfile->id], $ids);
    }

    public function test_delete_specialty_cascades_verifications(): void
    {
        [$user, $spec] = $this->artistWithSpecialty();
        Verification::create(['user_id' => $user->id, 'artist_specialty_id' => $spec->id, 'type' => 'specialty', 'status' => 'approved', 'artist_note' => 'x']);
        $this->assertEquals(1, Verification::count());

        $this->actingAs($user)->delete(route('artist.specialties.destroy', $spec->id))->assertRedirect();

        $this->assertEquals(0, Verification::count());
    }

    public function test_admin_dashboard_loads_with_pending_counts(): void
    {
        [$user, $spec] = $this->artistWithSpecialty();
        Verification::create(['user_id' => $user->id, 'artist_specialty_id' => $spec->id, 'type' => 'specialty', 'status' => 'pending', 'artist_note' => 'x']);
        User::create(['name' => 'تیم', 'email' => 'pend@x.com', 'password' => 'password', 'role' => 'production', 'approval_status' => 'pending']);

        $res = $this->actingAs($this->admin())->get(route('admin.dashboard'))->assertOk();
        $stats = $res->viewData('stats');
        $this->assertEquals(1, $stats['pending_verifications']);
        $this->assertEquals(1, $stats['pending_production']);
        $this->assertNotEmpty($res->viewData('actionQueue'));
    }
}
