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
        return view('admin.artists.edit', compact('artist'));
    }

    public function update(Request $request, int $id) {
        abort_unless(auth()->user()->role === 'admin', 403);
        $artist = ArtistProfile::findOrFail($id);
        $validated = $request->validate([
            'is_active'    => 'boolean',
            'admin_notes'  => 'nullable|string|max:2000',
        ]);
        $isActive = $request->boolean('is_active');
        $artist->update(['is_active' => $isActive]);
        if ($artist->user) $artist->user->update(['admin_notes' => $request->admin_notes]);
        $this->logAdminActivity('artist_updated', "پروفایل هنرمند {$artist->username} ویرایش شد.", 'artist_profile', $artist->id);
        return back()->with('success', 'پروفایل هنرمند به‌روزرسانی شد.');
    }
}
