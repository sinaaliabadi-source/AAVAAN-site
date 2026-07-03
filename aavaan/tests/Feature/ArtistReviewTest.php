<?php

namespace Tests\Feature;

use App\Models\ArtistProfile;
use App\Models\ArtistReview;
use App\Models\Payment;
use App\Models\ProductionAccess;
use App\Models\ProductionAccessLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * سیستم امتیازدهی و نظرات هنرمند.
 */
class ArtistReviewTest extends TestCase
{
    use RefreshDatabase;

    private const REAL_NAME = 'علی رضایی واقعی';

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function makeArtist(): ArtistProfile
    {
        $user = User::create([
            'name'     => self::REAL_NAME,
            'email'    => 'artist@example.com',
            'password' => 'password',
            'role'     => 'artist',
        ]);

        return ArtistProfile::create([
            'user_id'   => $user->id,
            'username'  => 'ali-real',
            'field'     => 'بازیگری و اجرا',
            'city'      => 'تهران',
            'bio'       => 'بیوگرافی نمونه.',
            'is_active' => true,
        ]);
    }

    private function makeProductionUser(string $email = 'prod@example.com'): User
    {
        return User::create([
            'name'     => 'تیم تولید',
            'email'    => $email,
            'password' => 'password',
            'role'     => 'production',
        ]);
    }

    private function grantAccess(User $prod, ArtistProfile $profile): void
    {
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
    }

    public function test_production_without_access_cannot_review(): void
    {
        $profile = $this->makeArtist();
        $prod    = $this->makeProductionUser();

        $this->actingAs($prod)
            ->post(route('review.store', $profile->username), ['rating' => 5])
            ->assertSessionHas('error');

        $this->assertDatabaseCount('artist_reviews', 0);
    }

    public function test_production_with_access_can_review_and_rating_is_aggregated(): void
    {
        $profile = $this->makeArtist();
        $prod    = $this->makeProductionUser();
        $this->grantAccess($prod, $profile);

        $this->actingAs($prod)
            ->post(route('review.store', $profile->username), ['rating' => 4, 'comment' => 'همکاری خوبی بود.'])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('artist_reviews', [
            'reviewer_user_id' => $prod->id,
            'artist_user_id'   => $profile->user_id,
            'rating'           => 4,
        ]);

        $profile->refresh();
        $this->assertSame(4.0, (float) $profile->rating_avg);
        $this->assertSame(1, $profile->rating_count);
    }

    public function test_comment_containing_artist_name_is_rejected(): void
    {
        $profile = $this->makeArtist();
        $prod    = $this->makeProductionUser();
        $this->grantAccess($prod, $profile);

        $this->actingAs($prod)
            ->post(route('review.store', $profile->username), [
                'rating'  => 5,
                'comment' => 'کار با علی عالی بود.',
            ])
            ->assertSessionHas('error');

        $this->assertDatabaseCount('artist_reviews', 0);
    }

    public function test_unique_review_per_team_updates_existing(): void
    {
        $profile = $this->makeArtist();
        $prod    = $this->makeProductionUser();
        $this->grantAccess($prod, $profile);

        $this->actingAs($prod)->post(route('review.store', $profile->username), ['rating' => 3]);
        $this->actingAs($prod)->put(route('review.update', $profile->username), ['rating' => 5]);

        $this->assertDatabaseCount('artist_reviews', 1);
        $this->assertDatabaseHas('artist_reviews', [
            'reviewer_user_id' => $prod->id,
            'rating'           => 5,
        ]);
    }

    public function test_admin_toggle_hides_review_from_aggregate(): void
    {
        $profile = $this->makeArtist();
        $prod    = $this->makeProductionUser();
        $this->grantAccess($prod, $profile);
        $this->actingAs($prod)->post(route('review.store', $profile->username), ['rating' => 4]);

        $admin = User::create([
            'name'     => 'ادمین',
            'email'    => 'admin@example.com',
            'password' => 'password',
            'role'     => 'admin',
        ]);

        $review = ArtistReview::first();
        $this->actingAs($admin)
            ->post(route('admin.reviews.toggle', $review->id))
            ->assertSessionHas('success');

        $profile->refresh();
        $this->assertSame(0, $profile->rating_count);
        $this->assertSame(0.0, (float) $profile->rating_avg);
    }
}
