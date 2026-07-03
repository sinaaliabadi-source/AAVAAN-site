<?php

namespace App\Http\Controllers;

use App\Models\ArtistProfile;
use App\Models\ArtistProfilePremium;
use App\Models\ArtistReview;
use App\Models\ProductionAccessLog;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * ثبت نظر و امتیاز برای هنرمند — فقط توسط تیم تولیدی که این هنرمند را باز کرده است.
     */
    public function store(Request $request, string $username)
    {
        $request->validate([
            'rating'  => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:500',
        ]);

        $profile = ArtistProfile::where('username', $username)->firstOrFail();
        $artistUserId = $profile->user_id;
        $reviewer = $request->user();

        // فقط تیم تولید مجاز است
        if ($reviewer->role !== 'production') {
            return back()->with('error', 'فقط تیم‌های تولید می‌توانند نظر ثبت کنند.');
        }

        // فقط تیم‌هایی که قبلاً دسترسیِ این هنرمند را خریده‌اند
        if (! $this->hasPaidAccess($reviewer->id, $profile->id)) {
            return back()->with('error', 'فقط تیم‌هایی که این هنرمند را انتخاب کرده‌اند می‌توانند نظر بدهند.');
        }

        // نظر نباید حاوی نام هنرمند باشد (حفظ کستینگ ناشناس)
        if ($request->filled('comment') && $this->containsArtistName($request->comment, $artistUserId)) {
            return back()->with('error', 'نظر نباید حاوی نام هنرمند باشد.')->withInput();
        }

        ArtistReview::updateOrCreate(
            ['reviewer_user_id' => $reviewer->id, 'artist_user_id' => $artistUserId],
            ['rating' => $request->rating, 'comment' => $request->comment],
        );

        $this->recalculateRating($artistUserId);

        return back()->with('success', 'نظر شما با موفقیت ثبت شد.');
    }

    /**
     * ویرایش نظر — فقط صاحب همان نظر.
     */
    public function update(Request $request, string $username)
    {
        $request->validate([
            'rating'  => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:500',
        ]);

        $profile = ArtistProfile::where('username', $username)->firstOrFail();
        $artistUserId = $profile->user_id;
        $reviewer = $request->user();

        $review = ArtistReview::where('reviewer_user_id', $reviewer->id)
            ->where('artist_user_id', $artistUserId)
            ->first();

        if (! $review) {
            return back()->with('error', 'نظری برای ویرایش یافت نشد.');
        }

        if ($request->filled('comment') && $this->containsArtistName($request->comment, $artistUserId)) {
            return back()->with('error', 'نظر نباید حاوی نام هنرمند باشد.')->withInput();
        }

        $review->update([
            'rating'  => $request->rating,
            'comment' => $request->comment,
        ]);

        $this->recalculateRating($artistUserId);

        return back()->with('success', 'نظر شما به‌روزرسانی شد.');
    }

    /**
     * حذف نظر — صاحب نظر یا ادمین.
     */
    public function destroy(Request $request, int $id)
    {
        $review = ArtistReview::findOrFail($id);
        $user = $request->user();

        if ($user->role !== 'admin' && $user->id !== $review->reviewer_user_id) {
            abort(403);
        }

        $artistUserId = $review->artist_user_id;
        $review->delete();
        $this->recalculateRating($artistUserId);

        return back()->with('success', 'نظر حذف شد.');
    }

    /**
     * آیا این تیم تولید رکورد دسترسیِ پرداخت‌شده برای این پروفایل دارد؟
     */
    private function hasPaidAccess(int $productionUserId, int $artistProfileId): bool
    {
        return ProductionAccessLog::where('production_user_id', $productionUserId)
            ->where('artist_profile_id', $artistProfileId)
            ->exists();
    }

    /**
     * بررسی اینکه نام واقعی یا نام هنریِ هنرمند در متن نظر نیامده باشد.
     */
    private function containsArtistName(string $comment, int $artistUserId): bool
    {
        $profile = ArtistProfile::where('user_id', $artistUserId)->with('user')->first();
        if (! $profile) {
            return false;
        }

        $commentLower = mb_strtolower($comment);

        $nameParts = preg_split('/\s+/', mb_strtolower($profile->user->name ?? ''));
        foreach ($nameParts as $part) {
            if (mb_strlen($part) > 2 && mb_strpos($commentLower, $part) !== false) {
                return true;
            }
        }

        $stageName = mb_strtolower(
            ArtistProfilePremium::where('user_id', $artistUserId)->value('stage_name') ?? ''
        );

        return $stageName !== '' && mb_strpos($commentLower, $stageName) !== false;
    }

    /**
     * محاسبهٔ مجدد میانگین و تعداد نظرات و ذخیره در artist_profiles.
     * فقط نظرات قابل‌نمایش در میانگین لحاظ می‌شوند.
     */
    private function recalculateRating(int $artistUserId): void
    {
        $query = ArtistReview::where('artist_user_id', $artistUserId)->where('is_visible', true);

        $count = $query->count();
        $avg = $count > 0 ? round((float) $query->avg('rating'), 1) : 0;

        ArtistProfile::where('user_id', $artistUserId)->update([
            'rating_avg'   => $avg,
            'rating_count' => $count,
        ]);
    }
}
