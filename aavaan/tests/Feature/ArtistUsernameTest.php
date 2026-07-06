<?php

namespace Tests\Feature;

use App\Models\ArtistProfile;
use App\Models\ProductionAccess;
use App\Models\ProductionAccessLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * تولید و یکتایی username هنرمندان و ایمن‌سازی unlock در برابر پروفایل بدون username.
 */
class ArtistUsernameTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Mail::fake();
    }

    /** ثبت‌نام هنرمند باید پروفایلی با username غیرتهی و یکتا بسازد. */
    public function test_registration_creates_artist_with_nonempty_unique_username(): void
    {
        $this->post(route('auth.register'), [
            'name'                  => 'علی رضایی',
            'email'                 => 'ali@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'artist',
            'field'                 => 'بازیگری و اجرا',
        ]);

        $profile = ArtistProfile::whereHas('user', fn ($q) => $q->where('email', 'ali@example.com'))->first();

        $this->assertNotNull($profile, 'پروفایل هنرمند باید ساخته شود.');
        $this->assertNotEmpty($profile->username, 'username نباید تهی باشد.');
    }

    /** دو هنرمند با نام یکسان باید usernameهای متفاوت (یکتا) بگیرند. */
    public function test_two_artists_same_name_get_unique_usernames(): void
    {
        foreach (['a@example.com', 'b@example.com'] as $email) {
            $this->post(route('auth.register'), [
                'name'                  => 'Sara Ahmadi',
                'email'                 => $email,
                'password'              => 'password123',
                'password_confirmation' => 'password123',
                'role'                  => 'artist',
                'field'                 => 'بازیگری و اجرا',
            ]);
            // ثبت‌نام کاربر را وارد حساب می‌کند؛ برای ثبت‌نام دوم (middleware guest) باید خارج شویم.
            $this->post(route('auth.logout'));
        }

        $usernames = ArtistProfile::pluck('username');

        $this->assertCount(2, $usernames);
        $this->assertNotEmpty($usernames[0]);
        $this->assertNotEmpty($usernames[1]);
        $this->assertNotSame($usernames[0], $usernames[1], 'usernameها باید یکتا باشند.');
    }

    /** unlock پروفایلِ بدون username نباید اعتبار مصرف کند یا لاگ بسازد. */
    public function test_unlock_of_profile_without_username_does_not_consume_credit(): void
    {
        // تیم تولید تأییدشده با یک بستهٔ اعتبار استفاده‌نشده.
        $team = User::create([
            'name'            => 'تیم تولید',
            'email'           => 'team@example.com',
            'password'        => 'password',
            'role'            => 'production',
            'approval_status' => 'approved',
        ]);
        $access = ProductionAccess::create([
            'user_id'     => $team->id,
            'access_type' => 'single',
            'bundle_size' => 1,
            'used_count'  => 0,
        ]);

        // هنرمند فعال ولی بدون username (دادهٔ قدیمی).
        $artistUser = User::create([
            'name'     => 'هنرمند بی‌یوزرنیم',
            'email'    => 'nouser@example.com',
            'password' => 'password',
            'role'     => 'artist',
        ]);
        $profile = ArtistProfile::create([
            'user_id'   => $artistUser->id,
            'username'  => null,
            'field'     => 'بازیگری',
            'is_active' => true,
        ]);

        $this->actingAs($team)->post(route('production.access.unlock'), [
            'artist_profile_id' => $profile->id,
        ])->assertRedirect(); // back با پیام خطا

        // اعتبار مصرف نشده و هیچ لاگی ثبت نشده است.
        $this->assertSame(0, $access->fresh()->used_count, 'اعتبار نباید مصرف شود.');
        $this->assertSame(0, ProductionAccessLog::where('production_user_id', $team->id)->count());
    }
}
