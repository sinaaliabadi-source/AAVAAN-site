<?php

namespace App\Http\Controllers;

use App\Models\ArtistProfile;
use App\Models\PortfolioItem;
use App\Models\WorkHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArtistDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $profile = $user->artistProfile;
        $subscription = $user->activeSubscription();
        return view('dashboard.artist.index', compact('user', 'profile', 'subscription'));
    }

    public function profile()
    {
        $user = auth()->user();
        $profile = $user->artistProfile()->with(['workHistories', 'portfolioItems'])->first();
        $fields = config('aavaan.artistic_fields');
        return view('dashboard.artist.profile', compact('user', 'profile', 'fields'));
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
            'avatar'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'reel_video'       => 'nullable|file|mimes:mp4,mov,webm|max:102400',
            'reel_url'         => 'nullable|url|max:500',
        ]);

        $data = collect($validated)->except(['avatar', 'reel_video', 'reel_url'])->filter()->toArray();

        if ($request->hasFile('avatar')) {
            if ($profile?->avatar) {
                @unlink(public_path('uploads/avatars/' . $profile->avatar));
            }
            $filename = Str::uuid() . '.' . $request->file('avatar')->extension();
            $request->file('avatar')->move(public_path('uploads/avatars'), $filename);
            $data['avatar'] = $filename;
        }

        if ($request->hasFile('reel_video')) {
            if ($profile?->reel_video && !$profile->reel_is_external) {
                @unlink(public_path('uploads/reels/' . $profile->reel_video));
            }
            $filename = Str::uuid() . '.' . $request->file('reel_video')->extension();
            $request->file('reel_video')->move(public_path('uploads/reels'), $filename);
            $data['reel_video'] = $filename;
            $data['reel_is_external'] = false;
        } elseif ($request->filled('reel_url')) {
            $data['reel_video'] = $request->input('reel_url');
            $data['reel_is_external'] = true;
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
        $profile = auth()->user()->artistProfile;

        if (!$profile || $profile->portfolioItems()->count() >= config('aavaan.upload.max_portfolio_items')) {
            return back()->with('error', 'حداکثر تعداد آیتم‌های نمونه‌کار رعایت نشده است.');
        }

        $validated = $request->validate([
            'image'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'video_url' => 'nullable|url|max:500',
            'caption'   => 'nullable|string|max:200',
        ]);

        if ($request->hasFile('image')) {
            $filename = Str::uuid() . '.' . $request->file('image')->extension();
            $request->file('image')->move(public_path('uploads/portfolios'), $filename);
            PortfolioItem::create([
                'artist_profile_id' => $profile->id,
                'type'              => 'image',
                'file_path'         => $filename,
                'caption'           => $validated['caption'] ?? null,
                'order'             => $profile->portfolioItems()->max('order') + 1,
            ]);
        } elseif (!empty($validated['video_url'])) {
            PortfolioItem::create([
                'artist_profile_id' => $profile->id,
                'type'              => 'video_link',
                'video_url'         => $validated['video_url'],
                'caption'           => $validated['caption'] ?? null,
                'order'             => $profile->portfolioItems()->max('order') + 1,
            ]);
        }

        return back()->with('success', 'آیتم جدید به نمونه‌کارها اضافه شد.');
    }

    public function deletePortfolio(int $id)
    {
        $item = PortfolioItem::findOrFail($id);
        if ($item->artistProfile->user_id !== auth()->id()) abort(403);
        if ($item->type === 'image' && $item->file_path) {
            @unlink(public_path('uploads/portfolios/' . $item->file_path));
        }
        $item->delete();
        return back()->with('success', 'آیتم حذف شد.');
    }

    public function addWorkHistory(Request $request)
    {
        $profile = auth()->user()->artistProfile;
        $validated = $request->validate([
            'title'       => 'required|string|max:200',
            'role'        => 'required|string|max:100',
            'year'        => 'nullable|integer|min:1300|max:1410',
            'director'    => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
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
