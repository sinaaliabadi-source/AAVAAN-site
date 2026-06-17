<?php

namespace App\Http\Controllers;

use App\Models\ArtistProfile;
use App\Models\ProductionAccessLog;
use Illuminate\Http\Request;

class ProductionDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $access = $user->availableProductionAccess();
        $recentLogs = ProductionAccessLog::where('production_user_id', $user->id)
            ->with('artistProfile.user')
            ->latest('accessed_at')
            ->limit(5)
            ->get();
        return view('dashboard.production.index', compact('user', 'access', 'recentLogs'));
    }

    public function search(Request $request)
    {
        $query = ArtistProfile::with('user')->where('is_active', true);

        if ($request->filled('field'))          $query->where('field', $request->field);
        if ($request->filled('city'))           $query->where('city', 'like', '%' . $request->city . '%');
        if ($request->filled('experience_min')) $query->where('years_experience', '>=', $request->experience_min);
        if ($request->filled('keyword')) {
            $kw = $request->keyword;
            $query->where(function ($q) use ($kw) {
                $q->where('bio', 'like', "%{$kw}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$kw}%"));
            });
        }

        $artists = $query->paginate(20)->withQueryString();

        $unlockedIds = [];
        if (auth()->check()) {
            $unlockedIds = ProductionAccessLog::where('production_user_id', auth()->id())
                ->pluck('artist_profile_id')
                ->toArray();
        }

        $fields = config('aavaan.artistic_fields');
        $access = auth()->user()->availableProductionAccess();

        return view('dashboard.production.search', compact('artists', 'unlockedIds', 'fields', 'access'));
    }

    public function saved()
    {
        $user = auth()->user();
        $savedArtists = ProductionAccessLog::where('production_user_id', $user->id)
            ->with('artistProfile.user')
            ->latest('accessed_at')
            ->paginate(20);
        return view('dashboard.production.saved', compact('savedArtists'));
    }
}
