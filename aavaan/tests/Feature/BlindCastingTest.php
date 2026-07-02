<?php

namespace Tests\Feature;

use App\Models\ArtistProfile;
use App\Models\Payment;
use App\Models\ProductionAccess;
use App\Models\ProductionAccessLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * کستینگ ناشناس (Blind Casting).
 *
 * این تست‌ها تضمین می‌کنند که مخفی‌سازی نام/تماس واقعی هنرمند سمت سرور انجام می‌شود
 * (اطلاعات اصلاً وارد HTML پاسخ نمی‌شود) نه صرفاً پنهان‌سازی بصری با CSS.
 */
class BlindCastingTest extends TestCase
{
    use RefreshDatabase;

    private const REAL_NAME = 'علی رضایی واقعی';

    private function makeArtist(): ArtistProfile
    {
        $user = User::create([
            'name'     => self::REAL_NAME,
            'email'    => 'artist@example.com',
            'password' => 'password',
            'role'     => 'artist',
        ]);

        return ArtistProfile::create([
            'user_id'       => $user->id,
            'username'      => 'ali-real',
            'field'         => 'بازیگری و اجرا',
            'city'          => 'تهران',
            'bio'           => 'بیوگرافی نمونه بدون اطلاعات تماس.',
            'phone_contact' => '09120000000',
            'email_contact' => 'contact@example.com',
            'is_active'     => true,
        ]);
    }

    private function makeProductionUser(): User
    {
        return User::create([
            'name'     => 'تیم تولید',
            'email'    => 'prod@example.com',
            'password' => 'password',
            'role'     => 'production',
        ]);
    }

    public function test_production_user_without_access_does_not_receive_real_name_or_contact(): void
    {
        $profile = $this->makeArtist();
        $prod    = $this->makeProductionUser();

        $html = $this->actingAs($prod)
            ->get(route('profile.show', $profile->username))
            ->assertOk()
            ->getContent();

        // نام و اطلاعات تماس واقعی نباید در HTML پاسخ باشند
        $this->assertStringNotContainsString(self::REAL_NAME, $html);
        $this->assertStringNotContainsString('09120000000', $html);
        $this->assertStringNotContainsString('contact@example.com', $html);

        // به‌جای نام باید شناسهٔ مستعار نمایش داده شود
        $this->assertStringContainsString('هنرمند #' . $profile->id, $html);
    }

    public function test_guest_does_not_receive_real_name(): void
    {
        $profile = $this->makeArtist();

        $html = $this->get(route('profile.show', $profile->username))
            ->assertOk()
            ->getContent();

        $this->assertStringNotContainsString(self::REAL_NAME, $html);
        $this->assertStringContainsString('هنرمند #' . $profile->id, $html);
    }

    public function test_production_user_with_access_log_sees_real_name_and_contact(): void
    {
        $profile = $this->makeArtist();
        $prod    = $this->makeProductionUser();

        $access = ProductionAccess::create([
            'user_id'     => $prod->id,
            'access_type' => 'single',
            'bundle_size' => 1,
            'used_count'  => 1,
        ]);
        Payment::create([
            'user_id'      => $prod->id,
            'payable_type' => ProductionAccess::class,
            'payable_id'   => $access->id,
            'amount'       => 200000,
            'status'       => 'paid',
        ]);
        ProductionAccessLog::create([
            'production_access_id' => $access->id,
            'production_user_id'   => $prod->id,
            'artist_profile_id'    => $profile->id,
            'accessed_at'          => now(),
        ]);

        $html = $this->actingAs($prod)
            ->get(route('profile.show', $profile->username))
            ->assertOk()
            ->getContent();

        // پس از ثبت رکورد دسترسی، نام و اطلاعات تماس واقعی نمایش داده می‌شوند
        $this->assertStringContainsString(self::REAL_NAME, $html);
        $this->assertStringContainsString('09120000000', $html);
        $this->assertStringContainsString('contact@example.com', $html);
    }

    public function test_artist_viewing_own_profile_sees_real_name(): void
    {
        $profile = $this->makeArtist();

        $html = $this->actingAs($profile->user)
            ->get(route('profile.show', $profile->username))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString(self::REAL_NAME, $html);
    }
}
