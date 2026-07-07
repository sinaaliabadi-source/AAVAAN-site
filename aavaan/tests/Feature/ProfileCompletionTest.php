<?php

namespace Tests\Feature;

use App\Models\ArtistProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * درصد تکمیل پروفایل: منبع واحد ArtistProfile::completionData().
 * پرکردن هر فیلد (از جمله سال تولد که قبلاً شمرده نمی‌شد) باید درصد را بالا ببرد.
 * توجه: ستون field در دیتابیس NOT NULL است، پس هر پروفایلِ موجود همیشه ۱۵٪ رشته هنری دارد.
 */
class ProfileCompletionTest extends TestCase
{
    use RefreshDatabase;

    private function newProfile(array $attrs = []): ArtistProfile
    {
        static $n = 0;
        $n++;
        $user = User::create([
            'name'              => "هنرمند {$n}",
            'email'             => "pc-artist{$n}@example.com",
            'password'          => 'password',
            'role'              => 'artist',
            'email_verified_at' => now(),
        ]);

        return ArtistProfile::create(array_merge([
            'user_id'  => $user->id,
            'username' => "pc-artist-{$n}",
            'field'    => 'بازیگری',
        ], $attrs));
    }

    /** پروفایلِ فقط دارای رشته هنری = ۱۵٪ و بقیه در فهرست موارد ناقص. */
    public function test_field_only_profile(): void
    {
        $profile = $this->newProfile();

        $data = $profile->completionData();

        $this->assertSame(15, $data['percent']);
        $this->assertContains('سال تولد', $data['hints']);
        $this->assertContains('شهر', $data['hints']);
    }

    /** افزودن شهر +۱۰٪ می‌کند و از فهرست ناقص حذف می‌شود. */
    public function test_city_adds_ten(): void
    {
        $profile = $this->newProfile();
        $this->assertSame(15, $profile->completionData()['percent']);

        $profile->update(['city' => 'تهران']);

        $this->assertSame(25, $profile->fresh()->completionData()['percent']);
        $this->assertNotContains('شهر', $profile->fresh()->completionData()['hints']);
    }

    /** افزودن سال تولد +۱۰٪ می‌کند (رفع باگ: قبلاً اصلاً شمرده نمی‌شد). */
    public function test_birth_year_now_counts(): void
    {
        $profile = $this->newProfile();
        $this->assertSame(15, $profile->completionData()['percent']);

        $profile->update(['birth_year' => 1370]);

        $this->assertSame(25, $profile->fresh()->completionData()['percent']);
        $this->assertNotContains('سال تولد', $profile->fresh()->completionData()['hints']);
    }

    /** پرکردن همهٔ فیلدهای متنی + تماس (بدون آپلود) به ۶۵٪ می‌رسد؛ آواتار و ریل بقیه را می‌سازند. */
    public function test_all_text_fields_reach_sixty_five(): void
    {
        $profile = $this->newProfile([
            'city'          => 'تهران',
            'birth_year'    => 1370,
            'bio'           => 'معرفی من',
            'phone_contact' => '09120000000',
        ]);

        // 15(field) + 10(city) + 10(birth) + 15(bio) + 15(contact) = 65
        $this->assertSame(65, $profile->completionData()['percent']);
    }

    /** با آواتار (بدون ریل) به ۸۵٪ می‌رسد؛ یعنی جمع همهٔ وزن‌ها ۱۰۰ است. */
    public function test_with_avatar_reaches_eighty_five(): void
    {
        $profile = $this->newProfile([
            'city'          => 'تهران',
            'birth_year'    => 1370,
            'bio'           => 'معرفی',
            'avatar'        => '1/avatar.jpg',
            'phone_contact' => '09120000000',
        ]);

        // همه به‌جز ریل(15) → 85
        $this->assertSame(85, $profile->completionData()['percent']);
        $this->assertSame(['ویدیوی ریل'], $profile->completionData()['hints']);
    }
}
