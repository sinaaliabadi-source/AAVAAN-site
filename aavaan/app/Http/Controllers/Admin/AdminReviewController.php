<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArtistProfile;
use App\Models\ArtistReview;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    use LogsAdminActivity;

    public function index(Request $request)
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $query = ArtistReview::with(['artist', 'reviewer'])->latest();

        if ($request->filled('rating')) {
            $query->where('rating', (int) $request->rating);
        }

        if ($request->filled('status')) {
            $query->where('is_visible', $request->status === 'visible');
        }

        $reviews = $query->paginate(30)->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function toggle(int $id)
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $review = ArtistReview::findOrFail($id);
        $review->update(['is_visible' => ! $review->is_visible]);

        $this->recalculateRating($review->artist_user_id);

        $this->logAdminActivity(
            'artist_review_toggled',
            'وضعیت نمایش نظر #' . $review->id . ' به ' . ($review->is_visible ? 'نمایش' : 'مخفی') . ' تغییر کرد.',
            'artist_review',
            $review->id
        );

        return back()->with('success', 'وضعیت نظر به‌روزرسانی شد.');
    }

    /**
     * محاسبهٔ مجدد میانگین و تعداد نظراتِ قابل‌نمایش برای هنرمند.
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
