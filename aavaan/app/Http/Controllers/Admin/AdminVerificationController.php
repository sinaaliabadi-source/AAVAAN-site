<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Verification;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\Request;

class AdminVerificationController extends Controller {
    use LogsAdminActivity;

    public function index(Request $request) {
        abort_unless(auth()->user()->role === 'admin', 403);
        $query = Verification::with('user');
        if ($request->status) $query->where('status', $request->status);
        else $query->where('status', 'pending');
        if ($request->type) $query->where('type', $request->type);
        $verifications = $query->orderByDesc('created_at')->paginate(30)->withQueryString();
        return view('admin.verifications.index', compact('verifications'));
    }

    public function approve(Request $request, int $id) {
        abort_unless(auth()->user()->role === 'admin', 403);
        $v = Verification::findOrFail($id);
        $v->update(['status' => 'approved', 'reviewed_at' => now(), 'notes' => $request->notes]);
        $this->logAdminActivity('verification_approved', "تخصص کاربر {$v->user?->name} تأیید شد.", 'verification', $v->id);
        return back()->with('success', 'تخصص تأیید شد.');
    }

    public function reject(Request $request, int $id) {
        abort_unless(auth()->user()->role === 'admin', 403);
        $v = Verification::findOrFail($id);
        $v->update(['status' => 'rejected', 'reviewed_at' => now(), 'notes' => $request->notes]);
        $this->logAdminActivity('verification_rejected', "تخصص کاربر {$v->user?->name} رد شد.", 'verification', $v->id);
        return back()->with('success', 'تخصص رد شد.');
    }
}
