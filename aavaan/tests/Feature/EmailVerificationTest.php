<?php

namespace Tests\Feature;

use App\Models\ArtistProfile;
use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

/**
 * فعال‌سازی حساب هنرمند با تأیید ایمیل — فقط نقش artist مشمول اجبار است.
 */
class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function newArtist(bool $verified): User
    {
        static $n = 0;
        $n++;
        $user = User::create([
            'name'              => "هنرمند {$n}",
            'email'             => "ev-artist{$n}@example.com",
            'password'          => 'password',
            'role'              => 'artist',
            'email_verified_at' => $verified ? now() : null,
        ]);
        ArtistProfile::create(['user_id' => $user->id, 'field' => 'بازیگری', 'username' => "ev-artist-{$n}"]);
        return $user;
    }

    /** هنرمند تأییدنشده → داشبورد مسدود و به صفحهٔ بررسی ایمیل هدایت می‌شود. */
    public function test_unverified_artist_is_blocked_from_dashboard(): void
    {
        $artist = $this->newArtist(verified: false);

        $this->actingAs($artist)
            ->get(route('artist.dashboard'))
            ->assertRedirect(route('verification.notice'));
    }

    /** کاربر قدیمی (تأییدشده) بدون هیچ اقدامی وارد داشبورد می‌شود (backward compatibility). */
    public function test_verified_legacy_artist_reaches_dashboard(): void
    {
        $artist = $this->newArtist(verified: true);

        $this->actingAs($artist)
            ->get(route('artist.dashboard'))
            ->assertOk();
    }

    /** کلیک لینک امضاشدهٔ تأیید → حساب فعال می‌شود و به داشبورد می‌رود. */
    public function test_clicking_verification_link_verifies_account(): void
    {
        $artist = $this->newArtist(verified: false);

        $verifyUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $artist->id, 'hash' => sha1($artist->getEmailForVerification())]
        );

        $this->actingAs($artist)->get($verifyUrl)->assertRedirect(route('artist.dashboard'));
        $this->assertTrue($artist->fresh()->hasVerifiedEmail());
    }

    /** ثبت‌نام هنرمند یک ایمیل تأیید می‌فرستد و به صفحهٔ بررسی ایمیل هدایت می‌کند. */
    public function test_artist_registration_sends_verification_and_redirects_to_notice(): void
    {
        Notification::fake();

        $this->post(route('auth.register'), [
            'name'                  => 'هنرمند ثبتی',
            'email'                 => 'reg-artist@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'artist',
            'field'                 => 'بازیگری و اجرا',
        ])->assertRedirect(route('verification.notice'));

        $user = User::where('email', 'reg-artist@example.com')->first();
        $this->assertNotNull($user);
        $this->assertFalse($user->hasVerifiedEmail());
        Notification::assertSentTo($user, VerifyEmailNotification::class);
    }

    /** تیم تولید مشمول تأیید ایمیل نیست؛ جریان تأیید ادمین دست‌نخورده می‌ماند. */
    public function test_production_registration_untouched_by_email_verification(): void
    {
        $this->post(route('auth.register'), [
            'name'                  => 'تیم تولید تازه',
            'email'                 => 'reg-prod@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'production',
        ])->assertRedirect(route('production.pending-approval'));

        $user = User::where('email', 'reg-prod@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame('pending', $user->approval_status);
    }

    /** ارسال مجدد ایمیل تأیید کار می‌کند و پیام موفقیت می‌دهد. */
    public function test_resend_verification_email(): void
    {
        Notification::fake();
        $artist = $this->newArtist(verified: false);

        $this->actingAs($artist)
            ->post(route('verification.send'))
            ->assertRedirect()
            ->assertSessionHas('success');

        Notification::assertSentTo($artist, VerifyEmailNotification::class);
    }
}
