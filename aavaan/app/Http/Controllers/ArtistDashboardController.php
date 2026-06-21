<?php

namespace App\Http\Controllers;

use App\Models\ArtistProfile;
use App\Models\ArtistSpecialtyMedia;
use App\Models\PortfolioImage;
use App\Models\PortfolioVideo;
use App\Models\SpecialtyAttributeDefinition;
use App\Models\SpecialtyCategory;
use App\Models\WorkHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ArtistDashboardController extends Controller
{
    private function safeUnlink(string $relativePath): void
    {
        $uploadsRoot = realpath(public_path('uploads'));
        if (!$uploadsRoot) {
            return;
        }
        $absolute = realpath(public_path('uploads/' . $relativePath));
        if ($absolute && str_starts_with($absolute, $uploadsRoot . DIRECTORY_SEPARATOR) && is_file($absolute)) {
            if (!unlink($absolute)) {
                Log::warning("Failed to delete upload file: {$absolute}");
            }
        }
    }


    public function index()
    {
        $user = auth()->user();
        $profile = $user->artistProfile()
            ->withCount(['portfolioImages', 'workHistories'])
            ->first();
        $subscription = $user->activeSubscription();
        return view('dashboard.artist.index', compact('user', 'profile', 'subscription'));
    }

    public function profile()
    {
        $user = auth()->user();

        $profile = $user->artistProfile()
            ->with(['workHistories', 'portfolioImages', 'portfolioVideos'])
            ->first();

        $fields = config('aavaan.artistic_fields');

        // Specialties with their category (and attribute definitions) and media
        $specialties = $user->artistSpecialties()
            ->with(['category.attributeDefinitions' => fn($q) => $q->orderBy('sort_order'), 'media' => fn($q) => $q->orderBy('sort_order')])
            ->get();

        $usedCategoryIds = $specialties->pluck('category_id');

        // All root categories for the "add new specialty" dropdown
        $categories = SpecialtyCategory::where('is_active', true)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        // All attribute definitions grouped by category_id for Alpine.js (lightweight projection)
        $definitionsByCategory = SpecialtyAttributeDefinition::orderBy('sort_order')
            ->get()
            ->groupBy('category_id')
            ->map(fn($defs) => $defs->map(fn($d) => [
                'key'         => $d->key,
                'label_fa'    => $d->label_fa,
                'field_type'  => $d->field_type,
                'options'     => $d->options,
                'is_required' => (bool) $d->is_required,
                'is_premium'  => (bool) $d->is_premium,
                'visibility'  => $d->visibility,
            ])->values()->all())
            ->all();

        $premiumProfile = $user->artistProfilePremium;

        $totalPhotos = ArtistSpecialtyMedia::whereHas(
            'artistSpecialty',
            fn($q) => $q->where('user_id', $user->id)
        )->where('type', 'photo')->count();

        return view('dashboard.artist.profile', compact(
            'user', 'profile', 'fields',
            'specialties', 'categories', 'usedCategoryIds', 'definitionsByCategory',
            'premiumProfile', 'totalPhotos'
        ));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $profile = $user->artistProfile;

        $validated = $request->validate([
            'username'         => "nullable|string|max:50|alpha_dash|unique:artist_profiles,username,{$profile?->id}",
            'field'            => 'required|string|max:100',
            'city'             => 'nullable|string|max:100',
            'birth_year'       => 'nullable|integer|min:1300|max:1410',
            'years_experience' => 'nullable|integer|min:0|max:60',
            'bio'              => 'nullable|string|max:1000',
            'phone_contact'    => 'nullable|string|max:15',
            'email_contact'    => 'nullable|email|max:200',
            'avatar'           => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ], [
            'field.required'       => 'رشته هنری الزامی است.',
            'username.alpha_dash'  => 'نام کاربری فقط می‌تواند شامل حروف، اعداد، خط‌تیره و زیرخط باشد.',
            'username.unique'      => 'این نام کاربری قبلاً انتخاب شده است.',
            'bio.max'              => 'بیوگرافی نباید بیشتر از ۱۰۰۰ کاراکتر باشد.',
            'avatar.mimes'         => 'فرمت آواتار باید jpg یا png باشد.',
            'avatar.max'           => 'حجم آواتار نباید بیشتر از ۵ مگابایت باشد.',
            'email_contact.email'  => 'فرمت ایمیل تماس صحیح نیست.',
        ]);

        $data = collect($validated)->except(['avatar'])->filter(fn($v) => $v !== null)->toArray();

        if ($request->hasFile('avatar')) {
            $dir = public_path("uploads/{$user->id}");
            if (!is_dir($dir)) mkdir($dir, 0750, true);
            if ($profile?->avatar) {
                $this->safeUnlink($profile->avatar);
            }
            $filename = Str::uuid() . '.' . $request->file('avatar')->extension();
            $request->file('avatar')->move($dir, $filename);
            $data['avatar'] = "{$user->id}/{$filename}";
        }

        if ($profile) {
            $profile->update($data);
        } else {
            $data['user_id'] = $user->id;
            ArtistProfile::create($data);
        }

        return back()->with('success', 'پروفایل با موفقیت ذخیره شد.');
    }

    public function uploadPortfolio(Request $request)
    {
        $user = auth()->user();
        $profile = $user->artistProfile;

        if (!$profile) {
            return back()->with('error', 'ابتدا اطلاعات پایه پروفایل را ذخیره کنید.');
        }

        $currentCount = $profile->portfolioImages()->count();
        $maxItems = config('aavaan.upload.max_portfolio_items', 10);
        $remaining = $maxItems - $currentCount;

        if ($remaining <= 0) {
            return back()->with('error', "حداکثر {$maxItems} تصویر مجاز است.");
        }

        $request->validate([
            'images'   => 'required|array',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:5120',
        ], [
            'images.required'  => 'حداقل یک تصویر انتخاب کنید.',
            'images.*.image'   => 'فایل انتخاب‌شده باید تصویر باشد.',
            'images.*.mimes'   => 'فرمت تصویر باید jpg یا png باشد.',
            'images.*.max'     => 'حجم هر تصویر نباید بیشتر از ۵ مگابایت باشد.',
        ]);

        $dir = public_path("uploads/{$user->id}/portfolios");
        if (!is_dir($dir)) mkdir($dir, 0750, true);

        $nextOrder = ($profile->portfolioImages()->max('order') ?? 0) + 1;
        $caption = $request->input('caption');
        $added = 0;

        foreach ($request->file('images') as $file) {
            if ($added >= $remaining) break;
            $filename = Str::uuid() . '.' . $file->extension();
            $file->move($dir, $filename);
            PortfolioImage::create([
                'artist_profile_id' => $profile->id,
                'file_path'         => "{$user->id}/portfolios/{$filename}",
                'caption'           => $caption ?: null,
                'order'             => $nextOrder++,
            ]);
            $added++;
        }

        return back()->with('success', "{$added} تصویر به گالری اضافه شد.");
    }

    public function deletePortfolio(int $id)
    {
        $item = PortfolioImage::findOrFail($id);
        if ($item->artistProfile->user_id !== auth()->id()) abort(403);
        $this->safeUnlink($item->file_path);
        $item->delete();
        return back()->with('success', 'تصویر حذف شد.');
    }

    public function uploadReel(Request $request)
    {
        $user = auth()->user();
        $profile = $user->artistProfile;

        if (!$profile) {
            return back()->with('error', 'ابتدا اطلاعات پایه پروفایل را ذخیره کنید.');
        }

        $request->validate([
            'reel' => 'required|file|mimes:mp4,mov,webm|max:102400',
        ], [
            'reel.required' => 'فایل ویدیو الزامی است.',
            'reel.mimes'    => 'فرمت ویدیو باید mp4، mov یا webm باشد.',
            'reel.max'      => 'حجم ویدیو نباید بیشتر از ۱۰۰ مگابایت باشد.',
        ]);

        $dir = public_path("uploads/{$user->id}/reels");
        if (!is_dir($dir)) mkdir($dir, 0750, true);

        $existingReel = $profile->portfolioVideos()->where('is_reel', true)->first();
        if ($existingReel) {
            $this->safeUnlink($existingReel->file_path);
            $existingReel->delete();
        }

        $file = $request->file('reel');
        $filename = Str::uuid() . '.' . $file->extension();
        $file->move($dir, $filename);

        PortfolioVideo::create([
            'artist_profile_id' => $profile->id,
            'file_path'         => "{$user->id}/reels/{$filename}",
            'is_reel'           => true,
            'order'             => 0,
        ]);

        return back()->with('success', 'ویدیوی ریل با موفقیت آپلود شد.');
    }

    public function deleteReel(int $id)
    {
        $reel = PortfolioVideo::findOrFail($id);
        if ($reel->artistProfile->user_id !== auth()->id()) abort(403);
        $this->safeUnlink($reel->file_path);
        $reel->delete();
        return back()->with('success', 'ویدیوی ریل حذف شد.');
    }

    public function addWorkHistory(Request $request)
    {
        $profile = auth()->user()->artistProfile;
        if (!$profile) {
            return back()->with('error', 'ابتدا اطلاعات پایه پروفایل را ذخیره کنید.');
        }

        $validated = $request->validate([
            'title'       => 'required|string|max:200',
            'role'        => 'required|string|max:100',
            'year'        => 'nullable|integer|min:1300|max:1410',
            'director'    => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
        ], [
            'title.required' => 'عنوان اثر الزامی است.',
            'role.required'  => 'نقش شما الزامی است.',
            'year.integer'   => 'سال باید عدد باشد.',
        ]);

        $validated['artist_profile_id'] = $profile->id;
        WorkHistory::create($validated);
        return back()->with('success', 'سابقه کاری اضافه شد.');
    }

    public function deleteWorkHistory(int $id)
    {
        $item = WorkHistory::findOrFail($id);
        if ($item->artistProfile->user_id !== auth()->id()) abort(403);
        $item->delete();
        return back()->with('success', 'سابقه کاری حذف شد.');
    }
}
