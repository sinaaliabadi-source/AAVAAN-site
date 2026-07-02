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
            ->firstOrFail();

        $isSelf = auth()->check() && auth()->id() === $profile->user_id;

        if (!$profile->is_active && !$isSelf) {
            abort(404);
        }

        if (!$isSelf) {
            $profile->increment('profile_views');
        }

        $hasAccess = false;
        $canUnlock = false;

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

        $specialties = $profile->user->artistSpecialties()
            ->with([
                'category.attributeDefinitions'        => fn($q) => $q->orderBy('sort_order'),
                'category.parent.attributeDefinitions' => fn($q) => $q->orderBy('sort_order'),
                'media'                                 => fn($q) => $q->orderBy('sort_order'),
            ])
            ->orderByDesc('is_primary')
            ->orderBy('id')
            ->get();

        return view('profile.show', compact('profile', 'hasAccess', 'canUnlock', 'isSelf', 'specialties'));
    }
}
