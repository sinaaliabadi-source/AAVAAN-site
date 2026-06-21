<?php

namespace App\Http\Controllers;

use App\Models\ArtistProfile;
use App\Models\ArtistSpecialty;
use App\Models\ProductionAccessLog;
use App\Models\SpecialtyAttributeDefinition;
use App\Models\SpecialtyCategory;
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

        // Leaf categories for the specialty filter dropdown
        $categories = SpecialtyCategory::whereNotNull('parent_id')
            ->where('is_active', true)
            ->with('parent')
            ->orderBy('parent_id')
            ->orderBy('sort_order')
            ->get();

        // Build definitionsByCategory: each category ID → effective definitions
        $parents = SpecialtyCategory::whereNull('parent_id')
            ->with([
                'attributeDefinitions' => fn($q) => $q->orderBy('sort_order'),
                'children',
            ])
            ->get();

        $definitionsByCategory = [];
        foreach ($parents as $parent) {
            $defsArray = $parent->attributeDefinitions->map(fn($d) => [
                'key'        => $d->key,
                'label_fa'   => $d->label_fa,
                'field_type' => $d->field_type,
                'options'    => $d->options,
            ])->values()->all();
            $definitionsByCategory[$parent->id] = $defsArray;
            foreach ($parent->children as $child) {
                $definitionsByCategory[$child->id] = $defsArray;
            }
        }

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

        // Specialty category + attribute filter
        if ($request->filled('category_id')) {
            $catId      = (int) $request->category_id;
            $attrInput  = $request->input('attr', []);
            $defsForCat = collect($definitionsByCategory[$catId] ?? [])->keyBy('key');

            $specialties = ArtistSpecialty::where('category_id', $catId)->get();

            $attrFilters = collect($attrInput)->filter(function ($v) {
                return $v !== null && $v !== '' && $v !== [];
            });

            if ($attrFilters->isNotEmpty()) {
                $specialties = $specialties->filter(function ($spec) use ($attrFilters, $defsForCat) {
                    foreach ($attrFilters as $key => $filterVal) {
                        $def     = $defsForCat[$key] ?? null;
                        if (!$def) continue;
                        $attrVal = $spec->attributes[$key] ?? null;

                        switch ($def['field_type']) {
                            case 'number':
                                $min = isset($filterVal['min']) && $filterVal['min'] !== '' ? (float)$filterVal['min'] : null;
                                $max = isset($filterVal['max']) && $filterVal['max'] !== '' ? (float)$filterVal['max'] : null;
                                if ($attrVal === null) return false;
                                if ($min !== null && (float)$attrVal < $min) return false;
                                if ($max !== null && (float)$attrVal > $max) return false;
                                break;
                            case 'select':
                                if ($filterVal !== '' && $attrVal !== $filterVal) return false;
                                break;
                            case 'multiselect':
                                $filterArr = array_filter((array)$filterVal);
                                if (!empty($filterArr) && empty(array_intersect((array)($attrVal ?? []), $filterArr))) {
                                    return false;
                                }
                                break;
                            case 'boolean':
                                if ($filterVal !== '' && (bool)$attrVal !== ($filterVal === '1')) return false;
                                break;
                        }
                    }
                    return true;
                });
            }

            $matchingUserIds = $specialties->pluck('user_id');
            $query->whereHas('user', fn($q) => $q->whereIn('id', $matchingUserIds));
        }

        $artists     = $query->paginate(20)->withQueryString();
        $unlockedIds = ProductionAccessLog::where('production_user_id', $user->id)
            ->pluck('artist_profile_id')
            ->toArray();

        return view('dashboard.production.search', compact(
            'artists', 'unlockedIds', 'fields', 'access', 'hasPaidAccess',
            'categories', 'definitionsByCategory'
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
