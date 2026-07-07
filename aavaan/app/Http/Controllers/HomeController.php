<?php

namespace App\Http\Controllers;

use App\Models\ArtistProfile;
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
        // فعلاً سایت تک‌زبانه (فارسی) است؛ مسیرهای زبان انگلیسی حذف شده‌اند.
        $locale = 'fa';
        app()->setLocale($locale);

        // هنرمندان برگزیده: نمونه‌ای تصادفی از همهٔ هنرمندانِ فعال (نه فقط تیک‌آبی‌ها)
        // برای نمایش در کروسل صفحهٔ اصلی. هویت این هنرمندان عمومی است، پس نام واقعی و
        // لینک پروفایل نمایش داده می‌شود. شرط is_active حفظ می‌شود تا پروفایل‌های
        // غیرفعال/ناقص نمایش داده نشوند. اگر هیچ هنرمندی نباشد، بخش اصلاً رندر نمی‌شود.
        $featuredArtists = ArtistProfile::with(['user.primarySpecialty.category'])
            ->where('is_active', true)
            ->inRandomOrder()
            ->limit(12)
            ->get();

        $festivalActive = \App\Support\Festival::active();
        $festivalEndsFa = \App\Support\Festival::endsAtJalali();

        $allCategories = SpecialtyCategory::where('is_active', true)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        $categoryGroups = collect(self::CATEGORY_GROUPS)->map(function ($group) use ($allCategories) {
            $group['categories'] = $allCategories->whereIn('slug', $group['slugs'])->values();
            return $group;
        });

        return view('home.index', compact(
            'featuredArtists', 'categoryGroups', 'allCategories', 'locale',
            'festivalActive', 'festivalEndsFa'
        ));
    }
}
