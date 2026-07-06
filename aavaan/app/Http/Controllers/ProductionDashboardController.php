<?php

namespace App\Http\Controllers;

use App\Helpers\ArtistPseudonym;
use App\Helpers\JalaliHelper;
use App\Models\ArtistProfile;
use App\Models\ArtistSpecialty;
use App\Models\ProductionAccessLog;
use App\Models\SpecialtyCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    /**
     * کست‌یاب — جستجوی هنرمند بر اساس ویژگی‌ها با کستینگ ناشناس.
     * تمام فیلترها به‌صورت SQL روی دیتابیس اجرا می‌شوند (نه در حافظهٔ PHP).
     */
    public function search(Request $request)
    {
        $user          = auth()->user();
        $access        = $user->availableProductionAccess();
        $hasPaidAccess = $user->productionAccesses()
            ->whereHas('payment', fn($q) => $q->where('status', 'paid'))
            ->exists();

        // زیرشاخه‌ها برای dropdown دستهٔ تخصص
        $categories = SpecialtyCategory::whereNotNull('parent_id')
            ->where('is_active', true)
            ->with('parent')
            ->orderBy('parent_id')
            ->orderBy('sort_order')
            ->get();

        // نگاشت هر دسته (والد + زیرشاخه) → فقط ویژگی‌های قابل فیلتر، برای سایدبار Alpine
        $definitionsByCategory = $this->buildFilterableDefinitions();

        // ── دستهٔ انتخاب‌شده و زیرشاخه‌هایش ─────────────────────────────
        $selectedCategory = null;
        $categoryIds      = [];
        if ($request->filled('category_id')) {
            $selectedCategory = SpecialtyCategory::with(['attributeDefinitions', 'parent.attributeDefinitions'])
                ->find((int) $request->category_id);
            if ($selectedCategory) {
                $categoryIds[] = $selectedCategory->id;
                // انتخاب والد → شامل زیرشاخه‌ها
                if ($selectedCategory->parent_id === null) {
                    $categoryIds = array_merge(
                        $categoryIds,
                        SpecialtyCategory::where('parent_id', $selectedCategory->id)->pluck('id')->all()
                    );
                }
            }
        }

        // ── whitelist ویژگی‌های قابل فیلتر (تطبیق با key + دسته، نه id هاردکد) ──
        // ورودی attr[...] که به تعریفِ is_filterable همین دسته نگاشت نشود، نادیده گرفته می‌شود.
        $filterableDefs = $selectedCategory
            ? $selectedCategory->effectiveAttributeDefinitions()->where('is_filterable', true)->keyBy('key')
            : collect();

        $attrFilters = $this->normalizeAttributeFilters((array) $request->input('attr', []), $filterableDefs);

        // ── کوئری اصلی روی artist_profiles ─────────────────────────────
        $query = ArtistProfile::query()->where('is_active', true);

        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }
        if ($request->filled('gender') && in_array($request->gender, ['male', 'female'], true)) {
            $query->where('gender', $request->gender);
        }
        if ($request->filled('experience_min')) {
            $query->where('years_experience', '>=', (int) $request->experience_min);
        }

        // سن از روی سال تولد شمسی محاسبه می‌شود (birth_year شمسی است، نه میلادی).
        $currentJalaliYear = JalaliHelper::convert(
            (int) now()->format('Y'), (int) now()->format('n'), (int) now()->format('j')
        )[0];
        if ($request->filled('age_min')) {
            $query->where('birth_year', '<=', $currentJalaliYear - (int) $request->age_min);
        }
        if ($request->filled('age_max')) {
            $query->where('birth_year', '>=', $currentJalaliYear - (int) $request->age_max);
        }

        if ($request->filled('keyword')) {
            // کستینگ ناشناس: جستجوی کلیدواژه فقط روی بیوگرافی انجام می‌شود، نه نام واقعی هنرمند،
            // تا تیم تولید نتواند پیش از خرید دسترسی هویت هنرمند را از طریق نام حدس/تأیید کند.
            $kw = $request->keyword;
            $query->where('bio', 'like', "%{$kw}%");
        }

        // ── فیلتر دسته + ویژگی‌ها با whereExists همبسته (کاملاً SQL) ──────
        if (!empty($categoryIds)) {
            $query->whereExists(function ($q) use ($categoryIds, $attrFilters) {
                $q->select(DB::raw(1))
                  ->from('artist_specialties as sp')
                  ->whereColumn('sp.user_id', 'artist_profiles.user_id')
                  ->whereIn('sp.category_id', $categoryIds);

                // AND میان ویژگی‌های مختلف؛ هر ویژگی یک whereExists جدا روی همان تخصص (sp).
                foreach ($attrFilters as $f) {
                    $q->whereExists(function ($sub) use ($f) {
                        $sub->select(DB::raw(1))
                            ->from('artist_specialty_attribute_values as v')
                            ->whereColumn('v.artist_specialty_id', 'sp.id')
                            ->where('v.definition_id', $f['definition_id']);

                        switch ($f['type']) {
                            case 'number':
                                if ($f['min'] !== null) $sub->where('v.value_number', '>=', $f['min']);
                                if ($f['max'] !== null) $sub->where('v.value_number', '<=', $f['max']);
                                break;
                            case 'multiselect':
                                // OR میان گزینه‌های یک ویژگی (چند ردیف در جدول مقادیر).
                                $sub->whereIn('v.value_string', $f['values']);
                                break;
                            case 'select':
                            case 'boolean':
                                $sub->where('v.value_string', $f['value']);
                                break;
                        }
                    });
                }
            });
        }

        // ── فیلتر «فقط تأییدشده‌ها» ─────────────────────────────────────
        // تخصص تأییدشده = وجود verification با type=specialty و status=approved روی تخصص کاربر
        // (در صورت انتخاب دسته، محدود به همان دسته‌ها). سازگار با whereExists جستجوی SQL فعلی.
        if ($request->boolean('verified_only')) {
            $query->whereExists(function ($q) use ($categoryIds) {
                $q->select(DB::raw(1))
                  ->from('verifications as vr')
                  ->join('artist_specialties as vsp', 'vsp.id', '=', 'vr.artist_specialty_id')
                  ->whereColumn('vsp.user_id', 'artist_profiles.user_id')
                  ->where('vr.type', 'specialty')
                  ->where('vr.status', 'approved');
                if (!empty($categoryIds)) {
                    $q->whereIn('vsp.category_id', $categoryIds);
                }
            });
        }

        $artists = $query->with('user')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        // ── دادهٔ کارت‌ها: تخصص‌ها (چیپ) + ویژگی‌های public تخصص match شده ──
        $cardData = $this->buildCardData($artists->getCollection(), $categoryIds);

        $unlockedIds = ProductionAccessLog::where('production_user_id', $user->id)
            ->pluck('artist_profile_id')
            ->all();

        // شمارندهٔ فیلترهای فعال (برای سایدبار و نسخهٔ موبایل)
        $activeFilterCount = collect(['city', 'gender', 'age_min', 'age_max', 'experience_min', 'keyword', 'category_id'])
            ->filter(fn($k) => $request->filled($k))->count()
            + count($attrFilters)
            + ($request->boolean('verified_only') ? 1 : 0);

        $festivalActive = \App\Support\Festival::active();

        return view('dashboard.production.search', compact(
            'artists', 'unlockedIds', 'access', 'hasPaidAccess',
            'categories', 'definitionsByCategory', 'cardData',
            'currentJalaliYear', 'activeFilterCount', 'festivalActive'
        ));
    }

    /**
     * تصویر ناشناس هنرمند برای کارت‌های قفل — بدون افشای مسیر واقعی فایل (که شامل شناسهٔ کاربری است).
     * فقط کد مستعار در آدرس قرار می‌گیرد و تصویر stream می‌شود.
     */
    public function anonAvatar(string $code)
    {
        $id = ArtistPseudonym::resolveActiveId($code);
        abort_if($id === null, 404);

        $profile = ArtistProfile::where('is_active', true)->find($id);
        $path    = $profile && $profile->avatar ? public_path('uploads/' . $profile->avatar) : null;

        // فایل واقعی موجود → stream آن (بدون افشای مسیر). private تا پروکسی/CDN میان‌گذاری هویتی نکند.
        if ($path && is_file($path)) {
            return response()->file($path, ['Cache-Control' => 'private, max-age=3600']);
        }

        // در نبود فایل، جای‌گیرندهٔ SVG برند (هرگز 500 نمی‌دهد و هیچ اطلاعات هویتی ندارد).
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="300" height="300" viewBox="0 0 300 300">'
            . '<rect width="300" height="300" fill="#1F2A44"/>'
            . '<text x="50%" y="52%" text-anchor="middle" dominant-baseline="middle" '
            . 'font-family="Tahoma,sans-serif" font-size="120" fill="#C9A24B">آ</text></svg>';

        return response($svg, 200, [
            'Content-Type'  => 'image/svg+xml',
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }

    /**
     * صفحهٔ وضعیت انتظار/رد تأیید برای تیم‌های تولید تأییدنشده.
     * اگر کاربر approved باشد به داشبورد هدایت می‌شود.
     */
    public function pendingApproval()
    {
        $user = auth()->user();
        if ($user->approval_status === 'approved') {
            return redirect()->route('production.dashboard');
        }

        return view('dashboard.production.pending-approval', compact('user'));
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

    // ─────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────

    /**
     * نگاشت هر دسته (والد + زیرشاخه) → آرایهٔ ویژگی‌های «قابل فیلتر» آن، برای رندر سایدبار.
     */
    private function buildFilterableDefinitions(): array
    {
        $parents = SpecialtyCategory::whereNull('parent_id')
            ->with([
                'attributeDefinitions' => fn($q) => $q->where('is_filterable', true)->orderBy('sort_order'),
                'children',
            ])
            ->get();

        $map = [];
        foreach ($parents as $parent) {
            $defs = $parent->attributeDefinitions->map(fn($d) => [
                'key'        => $d->key,
                'label_fa'   => $d->label_fa,
                'field_type' => $d->field_type,
                'unit'       => $d->unit,
                'options'    => $d->options,
            ])->values()->all();

            $map[$parent->id] = $defs;
            foreach ($parent->children as $child) {
                $map[$child->id] = $defs; // زیرشاخه ویژگی‌های والد را ارث می‌برد
            }
        }

        return $map;
    }

    /**
     * ورودی خام attr[...] را به فیلترهای معتبر تبدیل می‌کند (whitelist بر پایهٔ تعریف‌های filterable).
     *
     * @param  \Illuminate\Support\Collection  $filterableDefs  کلید=key تعریف
     * @return array<int, array>
     */
    private function normalizeAttributeFilters(array $attrInput, $filterableDefs): array
    {
        $filters = [];

        foreach ($attrInput as $key => $raw) {
            $def = $filterableDefs->get($key);
            if (!$def) {
                continue; // نادیده گرفتن کلیدهای غیرمجاز/غیرفیلتر (مثل attr[bio])
            }

            switch ($def->field_type) {
                case 'number':
                    $min = (is_array($raw) && isset($raw['min']) && $raw['min'] !== '') ? (float) $raw['min'] : null;
                    $max = (is_array($raw) && isset($raw['max']) && $raw['max'] !== '') ? (float) $raw['max'] : null;
                    if ($min === null && $max === null) break;
                    $filters[] = ['definition_id' => $def->id, 'type' => 'number', 'min' => $min, 'max' => $max];
                    break;

                case 'multiselect':
                    $values = array_values(array_filter((array) $raw, fn($v) => $v !== '' && $v !== null));
                    if (empty($values)) break;
                    $filters[] = ['definition_id' => $def->id, 'type' => 'multiselect', 'values' => $values];
                    break;

                case 'select':
                    if (!is_string($raw) || $raw === '') break;
                    $filters[] = ['definition_id' => $def->id, 'type' => 'select', 'value' => $raw];
                    break;

                case 'boolean':
                    if ($raw !== '0' && $raw !== '1') break;
                    $filters[] = ['definition_id' => $def->id, 'type' => 'boolean', 'value' => $raw];
                    break;
            }
        }

        return $filters;
    }

    /**
     * برای هر پروفایلِ صفحهٔ نتایج: چیپ‌های تخصص + ویژگی‌های publicِ تخصص match شده.
     *
     * @param  \Illuminate\Support\Collection  $profiles
     * @return array<int, array>  کلید = artist_profile id
     */
    private function buildCardData($profiles, array $categoryIds): array
    {
        $userIds = $profiles->pluck('user_id')->all();
        if (empty($userIds)) {
            return [];
        }

        $specialtiesByUser = ArtistSpecialty::whereIn('user_id', $userIds)
            ->with([
                'category:id,name_fa,slug,parent_id',
                'category.attributeDefinitions' => fn($q) => $q->orderBy('sort_order'),
                'category.parent.attributeDefinitions' => fn($q) => $q->orderBy('sort_order'),
                'latestVerification',
            ])
            ->orderByDesc('is_primary')
            ->get()
            ->groupBy('user_id');

        $data = [];
        foreach ($profiles as $profile) {
            $specs = $specialtiesByUser->get($profile->user_id, collect());

            // چیپ همهٔ تخصص‌ها به‌همراه وضعیت تأیید (برای نشان «تخصص تأییدشده»).
            $chips = $specs->map(fn($s) => [
                'name'     => $s->category?->name_fa,
                'verified' => $s->latestVerification?->status === 'approved',
            ])->filter(fn($c) => $c['name'])->unique('name')->values()->all();

            // تخصص match شده: در صورت فیلتر دسته، اولین تخصصِ درون دسته‌های انتخابی؛ وگرنه تخصص اصلی/اول.
            $matched = !empty($categoryIds)
                ? $specs->first(fn($s) => in_array($s->category_id, $categoryIds, true))
                : ($specs->firstWhere('is_primary', true) ?? $specs->first());

            $data[$profile->id] = [
                'specialty_chips' => $chips,
                'public_attrs'    => $matched ? $this->publicAttributeChips($matched) : [],
            ];
        }

        return $data;
    }

    /**
     * ویژگی‌های با visibility=public و مقداردار یک تخصص، برای نمایش روی کارت قفل.
     *
     * @return array<int, array{label:string, value:string, unit:?string}>
     */
    private function publicAttributeChips(ArtistSpecialty $spec): array
    {
        $defs  = $spec->category?->effectiveAttributeDefinitions() ?? collect();
        $attrs = $spec->attributes ?? [];
        $out   = [];

        foreach ($defs as $def) {
            if ($def->visibility !== 'public') {
                continue; // ویژگی‌های production_team_only روی کارت قفل نمایش داده نمی‌شوند
            }
            $val = $attrs[$def->key] ?? null;
            if ($val === null || $val === '' || $val === []) {
                continue;
            }

            $display = $this->displayAttributeValue($def, $val);
            if ($display === null || $display === '') {
                continue;
            }

            $out[] = ['label' => $def->label_fa, 'value' => $display, 'unit' => $def->unit];
            if (count($out) >= 6) break; // سقف نمایش روی کارت
        }

        return $out;
    }

    /**
     * تبدیل مقدار خام ویژگی به متن خوانا (بر اساس نوع فیلد و گزینه‌ها).
     */
    private function displayAttributeValue($def, $val): ?string
    {
        switch ($def->field_type) {
            case 'boolean':
                return $val ? 'بله' : 'خیر';
            case 'select':
                $opt = collect($def->options ?? [])->firstWhere('value', $val);
                return $opt['label'] ?? (string) $val;
            case 'multiselect':
                return collect((array) $val)->map(function ($v) use ($def) {
                    $o = collect($def->options ?? [])->firstWhere('value', $v);
                    return $o['label'] ?? $v;
                })->join('، ');
            default:
                return (string) $val;
        }
    }
}
