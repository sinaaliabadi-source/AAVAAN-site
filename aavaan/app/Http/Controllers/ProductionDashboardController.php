<?php

namespace App\Http\Controllers;

use App\Models\ArtistProfile;
use App\Models\ProductionAccessLog;
use Illuminate\Http\Request;

class ProductionDashboardController extends Controller
{
    public function index()
    {
        $user          = auth()->user();
        $access        = $user->availableProductionAccess();
        $hasPaidAccess = $user->productionAccesses()
            ->whereHas('payment', fn($q) => $q->where('status', 'paid'))
            ->exists();
        $totalUnlocked = ProductionAccessLog::where('production_user_id', $user->id)->count();
        $recentLogs    = ProductionAccessLog::where('production_user_id', $user->id)
            ->with('artistProfile.user')
            ->latest('accessed_at')
            ->limit(5)
            ->get();

        return view('dashboard.production.index', compact(
            'user', 'access', 'hasPaidAccess', 'totalUnlocked', 'recentLogs'
        ));
    }

    public function search(Request $request)
    {
        $user   = auth()->user();
        $fields = config('aavaan.artistic_fields');
        $access = $user->availableProductionAccess();
        $hasPaidAccess = $user->productionAccesses()
            ->whereHas('payment', fn($q) => $q->where('status', 'paid'))
            ->exists();

        $query = ArtistProfile::with('user')->where('is_active', true);

        if ($request->filled('field'))          $query->where('field', $request->field);
        if ($request->filled('city'))           $query->where('city', 'like', '%' . $request->city . '%');
        if ($request->filled('experience_min')) $query->where('years_experience', '>=', (int) $request->experience_min);
        if ($request->filled('age_min'))        $query->where('birth_year', '<=', now()->year - (int) $request->age_min);
        if ($request->filled('age_max'))        $query->where('birth_year', '>=', now()->year - (int) $request->age_max);
        if ($request->filled('keyword')) {
            $kw = $request->keyword;
            $query->where(function ($q) use ($kw) {
                $q->where('bio', 'like', "%{$kw}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$kw}%"));
            });
        }

        $artists     = $query->paginate(20)->withQueryString();
        $unlockedIds = ProductionAccessLog::where('production_user_id', $user->id)
            ->pluck('artist_profile_id')
            ->toArray();

        return view('dashboard.production.search', compact(
            'artists', 'unlockedIds', 'fields', 'access', 'hasPaidAccess'
        ));
    }

    public function saved()
    {
        $user         = auth()->user();
        $savedArtists = ProductionAccessLog::where('production_user_id', $user->id)
            ->with('artistProfile.user')
            ->latest('accessed_at')
            ->paginate(20);

        return view('dashboard.production.saved', compact('savedArtists'));
    }
}
