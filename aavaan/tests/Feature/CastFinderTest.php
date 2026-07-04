<?php

namespace Tests\Feature;

use App\Helpers\ArtistPseudonym;
use App\Models\ArtistProfile;
use App\Models\ArtistSpecialty;
use App\Models\Payment;
use App\Models\ProductionAccess;
use App\Models\ProductionAccessLog;
use App\Models\SpecialtyCategory;
use App\Models\User;
use App\Services\SpecialtyAttributeIndexer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * کست‌یاب — فیلتر SQL ویژگی‌ها + کستینگ ناشناس.
 */
class CastFinderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed(\Database\Seeders\SpecialtyCategoriesSeeder::class);
        $this->seed(\Database\Seeders\SpecialtyAttributeDefinitionsSeeder::class);
        $this->seed(\Database\Seeders\SpecialtySubcategoriesSeeder::class);
    }

    private function actingLeafCategory(): SpecialtyCategory
    {
        // یک زیرشاخهٔ «بازیگری و اجرا» (والد id=1)
        return SpecialtyCategory::where('parent_id', 1)->orderBy('id')->firstOrFail();
    }

    private function makeArtist(array $profile, array $attributes, ?int $categoryId = null): ArtistProfile
    {
        static $n = 0;
        $n++;
        $user = User::create([
            'name'     => $profile['name'] ?? "هنرمند واقعی {$n}",
            'email'    => "artist{$n}@example.com",
            'password' => 'password',
            'role'     => 'artist',
        ]);

        $p = ArtistProfile::create(array_merge([
            'user_id'   => $user->id,
            'username'  => "real-artist-{$n}",
            'field'     => 'بازیگری و اجرا',
            'is_active' => true,
        ], $profile));

        $spec = ArtistSpecialty::create([
            'user_id'     => $user->id,
            'category_id' => $categoryId ?? $this->actingLeafCategory()->id,
            'is_primary'  => true,
            'attributes'  => $attributes,
        ]);
        app(SpecialtyAttributeIndexer::class)->sync($spec);

        return $p->fresh();
    }

    private function makeProductionUser(bool $withCredits = false): User
    {
        static $m = 0;
        $m++;
        $user = User::create([
            'name'     => 'تیم تولید',
            'email'    => "prod{$m}@example.com",
            'password' => 'password',
            'role'     => 'production',
        ]);

        if ($withCredits) {
            $access = ProductionAccess::create([
                'user_id' => $user->id, 'access_type' => 'bundle_5',
                'bundle_size' => 5, 'used_count' => 0,
            ]);
            Payment::create([
                'user_id' => $user->id, 'payable_type' => ProductionAccess::class,
                'payable_id' => $access->id, 'amount' => 500000, 'status' => 'paid',
            ]);
        }

        return $user;
    }

    public function test_no_filters_returns_all_active_artists(): void
    {
        $this->makeArtist(['city' => 'تهران'], ['height_cm' => 180]);
        $this->makeArtist(['city' => 'شیراز'], ['height_cm' => 160]);
        // غیرفعال — نباید بیاید
        $inactive = $this->makeArtist(['city' => 'کرج'], ['height_cm' => 170]);
        $inactive->update(['is_active' => false]);

        $prod = $this->makeProductionUser();
        $res  = $this->actingAs($prod)->get(route('production.search'))->assertOk();

        $this->assertEquals(2, $res->viewData('artists')->total());
    }

    public function test_number_range_and_multiselect_filter_together(): void
    {
        $leaf = $this->actingLeafCategory();
        // مطابق: قد ۱۷۰، لهجهٔ آذری
        $match = $this->makeArtist(
            ['city' => 'تهران', 'gender' => 'female'],
            ['height_cm' => 170, 'accents' => ['azeri', 'tehrani']],
            $leaf->id
        );
        // نامطابق قد
        $this->makeArtist(['city' => 'تهران'], ['height_cm' => 150, 'accents' => ['azeri']], $leaf->id);
        // نامطابق لهجه
        $this->makeArtist(['city' => 'تهران'], ['height_cm' => 172, 'accents' => ['kurdish']], $leaf->id);

        $prod = $this->makeProductionUser();
        $res  = $this->actingAs($prod)->get(route('production.search', [
            'category_id' => $leaf->id,
            'attr' => [
                'height_cm' => ['min' => 160, 'max' => 175],
                'accents'   => ['azeri'],
            ],
        ]))->assertOk();

        $ids = $res->viewData('artists')->pluck('id')->all();
        $this->assertEquals([$match->id], $ids);
    }

    public function test_non_filterable_attr_key_is_ignored(): void
    {
        $leaf = $this->actingLeafCategory();
        $a = $this->makeArtist(['city' => 'تهران'], ['height_cm' => 180], $leaf->id);

        $prod = $this->makeProductionUser();
        // attr[bio] و یک ویژگی ناموجود نباید کوئری را بشکنند یا فیلتر کنند.
        $res = $this->actingAs($prod)->get(route('production.search', [
            'category_id' => $leaf->id,
            'attr' => [
                'bio'        => 'چیزی',
                'nonexistent'=> 'x',
            ],
        ]))->assertOk();

        $this->assertTrue($res->viewData('artists')->pluck('id')->contains($a->id));
    }

    public function test_locked_card_leaks_no_identity(): void
    {
        $artist = $this->makeArtist(
            ['name' => 'علی رضایی واقعی', 'username' => 'ali-secret', 'city' => 'تهران', 'avatar' => '999/secret-file.jpg'],
            ['height_cm' => 175]
        );

        $prod = $this->makeProductionUser(withCredits: true);
        $html = $this->actingAs($prod)->get(route('production.search'))->assertOk()->getContent();

        // هیچ نام/نام‌کاربری/مسیر فایل هویتی نباید در HTML باشد
        $this->assertStringNotContainsString('علی رضایی واقعی', $html);
        $this->assertStringNotContainsString('ali-secret', $html);
        $this->assertStringNotContainsString('999/secret-file.jpg', $html);
        // کد مستعار باید باشد
        $this->assertStringContainsString(ArtistPseudonym::code($artist->id), $html);
    }

    public function test_unlocked_card_shows_real_name_and_profile_link(): void
    {
        $artist = $this->makeArtist(
            ['name' => 'مریم احمدی واقعی', 'username' => 'maryam-real', 'city' => 'تهران'],
            ['height_cm' => 168]
        );
        $prod   = $this->makeProductionUser(withCredits: true);
        $access = $prod->availableProductionAccess();

        ProductionAccessLog::create([
            'production_access_id' => $access->id,
            'production_user_id'   => $prod->id,
            'artist_profile_id'    => $artist->id,
            'accessed_at'          => now(),
        ]);

        $html = $this->actingAs($prod)->get(route('production.search'))->assertOk()->getContent();

        $this->assertStringContainsString('مریم احمدی واقعی', $html);
        $this->assertStringContainsString(route('profile.show', 'maryam-real'), $html);
    }

    public function test_gender_filter_sql(): void
    {
        $female = $this->makeArtist(['gender' => 'female', 'city' => 'تهران'], ['height_cm' => 165]);
        $this->makeArtist(['gender' => 'male', 'city' => 'تهران'], ['height_cm' => 180]);

        $prod = $this->makeProductionUser();
        $res  = $this->actingAs($prod)->get(route('production.search', ['gender' => 'female']))->assertOk();

        $this->assertEquals([$female->id], $res->viewData('artists')->pluck('id')->all());
    }

    public function test_anon_avatar_route_hides_path_and_serves_default_when_missing(): void
    {
        $artist = $this->makeArtist(['city' => 'تهران'], ['height_cm' => 170]);
        $prod   = $this->makeProductionUser();

        $this->actingAs($prod)
            ->get(route('production.anon-avatar', ArtistPseudonym::hash($artist->id)))
            ->assertOk();

        // هش نامعتبر → 404
        $this->actingAs($prod)
            ->get(route('production.anon-avatar', 'zzzzzz'))
            ->assertNotFound();
    }
}
