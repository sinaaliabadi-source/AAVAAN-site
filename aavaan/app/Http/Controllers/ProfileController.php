<?php

namespace App\Http\Controllers;

use App\Helpers\ArtistPrivacy;
use App\Models\ArtistProfile;
use App\Models\ArtistReview;
use App\Models\ProductionAccessLog;

class ProfileController extends Controller
{
    public function show(string $username)
    {
        $profile = ArtistProfile::with([
            'user',
            'workHistories',
            'portfolioImages',
            'portfolioVideos',
        ])
            ->where('username', $username)
            ->firstOrFail();

        $isSelf = auth()->check() && auth()->id() === $profile->user_id;

        if (!$profile->is_active && !$isSelf) {
            abort(404);
        }

        if (!$isSelf) {
            $profile->increment('profile_views');
        }

        // سطح دسترسی از طریق helper مرکزیِ کستینگ ناشناس تعیین می‌شود.
        $hasAccess = ArtistPrivacy::hasAccess(auth()->user(), $profile->user_id);
        $canUnlock = false;

        if (!$hasAccess && !$isSelf && auth()->check() && auth()->user()->isProduction()) {
            $canUnlock = auth()->user()->availableProductionAccess() !== null;
        }

        // نظرات قابل‌نمایش + نظر خودِ تیم تولید (برای فرم ویرایش)
        $reviews = ArtistReview::where('artist_user_id', $profile->user_id)
            ->where('is_visible', true)
            ->latest()
            ->get();

        $myReview = auth()->check()
            ? ArtistReview::where('artist_user_id', $profile->user_id)
                ->where('reviewer_user_id', auth()->id())
                ->first()
            : null;

        $canReview = auth()->check()
            && auth()->user()->role === 'production'
            && ProductionAccessLog::where('production_user_id', auth()->id())
                ->where('artist_profile_id', $profile->id)
                ->exists();

        $specialties = $profile->user->artistSpecialties()
            ->with([
                'category.attributeDefinitions'        => fn($q) => $q->orderBy('sort_order'),
                'category.parent.attributeDefinitions' => fn($q) => $q->orderBy('sort_order'),
                'media'                                 => fn($q) => $q->orderBy('sort_order'),
                'latestVerification',
            ])
            ->orderByDesc('is_primary')
            ->orderBy('id')
            ->get();

        return view('profile.show', compact(
            'profile', 'hasAccess', 'canUnlock', 'isSelf', 'specialties',
            'reviews', 'myReview', 'canReview'
        ));
    }
}
