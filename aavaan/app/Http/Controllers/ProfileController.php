<?php

namespace App\Http\Controllers;

use App\Models\ArtistProfile;
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
            ->where('is_active', true)
            ->firstOrFail();

        $profile->increment('profile_views');

        $hasAccess  = false;
        $canUnlock  = false;
        $isSelf     = auth()->check() && auth()->id() === $profile->user_id;

        if ($isSelf) {
            $hasAccess = true;
        } elseif (auth()->check() && auth()->user()->isProduction()) {
            $hasAccess = ProductionAccessLog::where('production_user_id', auth()->id())
                ->where('artist_profile_id', $profile->id)
                ->exists();

            if (!$hasAccess) {
                $canUnlock = auth()->user()->availableProductionAccess() !== null;
            }
        }

        return view('profile.show', compact('profile', 'hasAccess', 'canUnlock', 'isSelf'));
    }
}
