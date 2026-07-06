<?php

namespace Tests\Feature;

use App\Models\ArtistProfile;
use App\Models\SystemSetting;
use App\Models\User;
use App\Support\Festival;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * «تیک آبی آوان» — نشان برگزیدگیِ کلِ پروفایل (جدا از تأیید تخصص).
 * هنرمند دارای تیک آبی هویت عمومی دارد و در کست‌یاب/برگزیدگان با نام واقعی می‌آید.
 */
class BlueTickTest extends TestCase
{
    use RefreshDatabase;

    private const BLUE_NAME   = 'ستارهٔ برگزیده';
    private const NORMAL_NAME = 'هنرمند ناشناس واقعی';

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        // جشنواره خاموش تا رفتار تیک آبی مستقل از جشنواره سنجیده شود.
        SystemSetting::set('festival_active', '0');
        Festival::forgetCache();
    }

    private function makeArtist(string $name, string $username, bool $blueTick): ArtistProfile
    {
        $user = User::create([
            'name'     => $name,
            'email'    => $username . '@example.com',
            'password' => 'password',
            'role'     => 'artist',
        ]);

        return ArtistProfile::create([
            'user_id'              => $user->id,
            'username'             => $username,
            'field'                => 'بازیگری و اجرا',
            'city'                 => 'تهران',
            'is_active'            => true,
            'has_blue_tick'        => $blueTick,
            'blue_tick_granted_at' => $blueTick ? now() : null,
        ]);
    }

    private function approvedTeam(): User
    {
        return User::create([
            'name'            => 'تیم تولید',
            'email'           => 'blueteam@example.com',
            'password'        => 'password',
            'role'            => 'production',
            'approval_status' => 'approved',
        ]);
    }

    /** در کست‌یاب: هنرمند تیک‌آبی با نام واقعی، بقیه ناشناس. */
    public function test_blue_tick_artist_shown_by_real_name_in_cast_finder_others_anonymous(): void
    {
        $blue   = $this->makeArtist(self::BLUE_NAME, 'blue-star', true);
        $normal = $this->makeArtist(self::NORMAL_NAME, 'normal-one', false);

        $html = $this->actingAs($this->approvedTeam())
            ->get(route('production.search'))
            ->assertOk()
            ->getContent();

        // هنرمند تیک‌آبی: نام واقعی نمایش داده می‌شود.
        $this->assertStringContainsString(self::BLUE_NAME, $html);

        // هنرمند عادی: نام واقعی نباید لو برود؛ کد مستعار نمایش داده می‌شود.
        $this->assertStringNotContainsString(self::NORMAL_NAME, $html);
        $this->assertStringContainsString(\App\Helpers\ArtistPseudonym::code($normal->id), $html);
    }

    /** پروفایل عمومی هنرمند تیک‌آبی نام واقعی را نشان می‌دهد حتی برای مهمان. */
    public function test_blue_tick_artist_public_profile_shows_real_name_to_guest(): void
    {
        $blue = $this->makeArtist(self::BLUE_NAME, 'blue-public', true);

        $html = $this->get(route('profile.show', $blue->username))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString(self::BLUE_NAME, $html);
    }

    /** بخش «هنرمندان برگزیده» صفحهٔ اصلی فقط دارندگان تیک آبی را نشان می‌دهد. */
    public function test_home_featured_section_only_shows_blue_tick_artists(): void
    {
        $this->makeArtist(self::NORMAL_NAME, 'normal-home', false);
        $this->makeArtist(self::BLUE_NAME, 'blue-home', true);

        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringContainsString(self::BLUE_NAME, $html);
        $this->assertStringNotContainsString(self::NORMAL_NAME, $html);
    }

    /** بخش برگزیدگان در نبودِ هیچ هنرمند تیک‌آبی اصلاً رندر نمی‌شود. */
    public function test_home_featured_section_hidden_when_no_blue_tick_artists(): void
    {
        $this->makeArtist(self::NORMAL_NAME, 'normal-only', false);

        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringNotContainsString(__('home.featured_title'), $html);
    }
}
