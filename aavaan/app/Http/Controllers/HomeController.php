<?php

namespace App\Http\Controllers;

use App\Models\ArtistProfile;
use App\Models\Program;
use App\Models\SpecialtyCategory;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    private const CATEGORY_GROUPS = [
        ['label' => 'اجرا',               'icon' => '🎭', 'slugs' => ['acting','stunt','music','animation','games','coaching']],
        ['label' => 'خلق محتوا',           'icon' => '🎬', 'slugs' => ['directing','writing','production-design','costume','makeup','photography']],
        ['label' => 'فنی و تولید',         'icon' => '🎛️', 'slugs' => ['cinematography','lighting','sound','editing','vfx']],
        ['label' => 'مدیریت و پشتیبانی',  'icon' => '📋', 'slugs' => ['production-management','casting','pr-marketing','crew','translation']],
    ];

    public function index(Request $request)
    {
        // زبان از پیش‌فرض مسیر (/fa، /en) تعیین می‌شود؛ پیش‌فرض فارسی.
        $locale = $request->route()->defaults['locale'] ?? config('app.locale', 'fa');
        if (! in_array($locale, ['fa', 'en'], true)) {
            $locale = 'fa';
        }
        app()->setLocale($locale);

        // هنرمندان برگزیده: فعال‌ترین/پربازدیدترین پروفایل‌ها (اولویت نمایش واقعی)
        $featuredArtists = ArtistProfile::with('user')
            ->where('is_active', true)
            ->orderByDesc('profile_views')
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        $allCategories = SpecialtyCategory::where('is_active', true)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        $categoryGroups = collect(self::CATEGORY_GROUPS)->map(function ($group) use ($allCategories) {
            $group['categories'] = $allCategories->whereIn('slug', $group['slugs'])->values();
            return $group;
        });

        // برنامه فعال هنرباز برای بنر اعلان صفحه اصلی (در صورت وجود و فعال بودن).
        $honarbaz = Program::where('slug', config('honarbaz.program_slug'))
            ->where('status', 'active')
            ->first();

        return view('home.index', compact('featuredArtists', 'categoryGroups', 'allCategories', 'locale', 'honarbaz'));
    }
}
