<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArtistProfile;
use App\Models\SpecialtyCategory;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\Request;

class AdminArtistController extends Controller {
    use LogsAdminActivity;

    public function index(Request $request) {
        abort_unless(auth()->user()->role === 'admin', 403);
        $query = ArtistProfile::with(['user']);
        if ($request->status === 'active') $query->where('is_active', true);
        elseif ($request->status === 'inactive') $query->where('is_active', false);
        if ($request->blue_tick === 'yes') $query->where('has_blue_tick', true);
        elseif ($request->blue_tick === 'no') $query->where('has_blue_tick', false);
        if ($request->city) $query->where('city', $request->city);
        if ($request->search) {
            $s = '%' . $request->search . '%';
            $query->where(fn($q) => $q->where('username','like',$s)->orWhereHas('user', fn($u) => $u->where('name','like',$s)->orWhere('email','like',$s)));
        }
        $artists = $query->orderByDesc('created_at')->paginate(30)->withQueryString();
        $cities = ArtistProfile::whereNotNull('city')->distinct()->orderBy('city')->pluck('city');
        return view('admin.artists.index', compact('artists', 'cities'));
    }

    public function edit(int $id) {
        abort_unless(auth()->user()->role === 'admin', 403);
        $artist = ArtistProfile::with('user')->findOrFail($id);

        // تخصص‌ها و وضعیت تأیید (فقط‌خواندنی) برای نمایش در صفحهٔ ادمین.
        $specialties = \App\Models\ArtistSpecialty::where('user_id', $artist->user_id)
            ->with(['category:id,name_fa', 'latestVerification'])
            ->orderByDesc('is_primary')
            ->get();

        return view('admin.artists.edit', compact('artist', 'specialties'));
    }

    public function update(Request $request, int $id) {
        abort_unless(auth()->user()->role === 'admin', 403);
        $artist = ArtistProfile::findOrFail($id);
        $validated = $request->validate([
            'is_active'     => 'boolean',
            'has_blue_tick' => 'boolean',
            'admin_notes'   => 'nullable|string|max:2000',
        ]);
        $isActive = $request->boolean('is_active');
        $artist->update(['is_active' => $isActive]);

        // «تیک آبی آوان» — نشان برگزیدگیِ کلِ پروفایل (جدا از تأیید تخصص).
        $wantsBlueTick = $request->boolean('has_blue_tick');
        if ($wantsBlueTick !== (bool) $artist->has_blue_tick) {
            $artist->update([
                'has_blue_tick'        => $wantsBlueTick,
                'blue_tick_granted_at' => $wantsBlueTick ? now() : null,
            ]);
            $this->logAdminActivity(
                $wantsBlueTick ? 'artist_blue_tick_granted' : 'artist_blue_tick_revoked',
                ($wantsBlueTick ? 'تیک آبی آوان به هنرمند ' : 'تیک آبی آوان از هنرمند ') . "{$artist->username} " . ($wantsBlueTick ? 'اعطا شد.' : 'برداشته شد.'),
                'artist_profile',
                $artist->id
            );
        }

        if ($artist->user) $artist->user->update(['admin_notes' => $request->admin_notes]);
        $this->logAdminActivity('artist_updated', "پروفایل هنرمند {$artist->username} ویرایش شد.", 'artist_profile', $artist->id);
        return back()->with('success', 'پروفایل هنرمند به‌روزرسانی شد.');
    }
}
