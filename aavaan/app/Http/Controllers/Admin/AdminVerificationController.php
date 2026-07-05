<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\SpecialtyVerificationApprovedMail;
use App\Mail\SpecialtyVerificationRejectedMail;
use App\Models\Verification;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminVerificationController extends Controller {
    use LogsAdminActivity;

    public function index(Request $request) {
        $query = Verification::with(['user', 'artistSpecialty.category']);
        if ($request->status) $query->where('status', $request->status);
        else $query->where('status', 'pending');
        if ($request->type) $query->where('type', $request->type);
        $verifications = $query->orderByDesc('created_at')->paginate(30)->withQueryString();
        return view('admin.verifications.index', compact('verifications'));
    }

    // صفحهٔ جزئیات بررسی درخواست تأیید تخصص.
    public function show(int $id) {
        $verification = Verification::with([
            'user.artistProfile',
            'artistSpecialty.category.attributeDefinitions',
            'artistSpecialty.category.parent.attributeDefinitions',
            'artistSpecialty.media',
        ])->findOrFail($id);

        // تاریخچهٔ درخواست‌های قبلی همین تخصص (به‌جز خودِ این رکورد).
        $history = collect();
        if ($verification->artist_specialty_id) {
            $history = Verification::where('artist_specialty_id', $verification->artist_specialty_id)
                ->where('id', '!=', $verification->id)
                ->orderByDesc('created_at')
                ->get();
        }

        return view('admin.verifications.show', compact('verification', 'history'));
    }

    public function approve(Request $request, int $id) {
        abort_unless(auth()->user()->role === 'admin', 403);
        $v = Verification::with('artistSpecialty.category', 'user')->findOrFail($id);
        $v->update(['status' => 'approved', 'reviewed_at' => now(), 'notes' => $request->notes]);
        $this->logAdminActivity('verification_approved', "تخصص کاربر {$v->user?->name} تأیید شد.", 'verification', $v->id);

        $this->notify($v, new SpecialtyVerificationApprovedMail($v));

        return back()->with('success', 'تخصص تأیید شد.');
    }

    public function reject(Request $request, int $id) {
        abort_unless(auth()->user()->role === 'admin', 403);
        $request->validate(
            ['notes' => ['required', 'string', 'max:1000']],
            ['notes.required' => 'ذکر دلیل رد الزامی است.']
        );
        $v = Verification::with('artistSpecialty.category', 'user')->findOrFail($id);
        $v->update(['status' => 'rejected', 'reviewed_at' => now(), 'notes' => $request->notes]);
        $this->logAdminActivity('verification_rejected', "تخصص کاربر {$v->user?->name} رد شد.", 'verification', $v->id);

        $this->notify($v, new SpecialtyVerificationRejectedMail($v));

        return back()->with('success', 'درخواست رد شد.');
    }

    // ارسال ایمیل اطلاع‌رسانی — فقط برای تأیید تخصص و داخل try/catch.
    private function notify(Verification $v, $mailable): void {
        if ($v->type !== Verification::TYPE_SPECIALTY || !$v->user?->email) {
            return;
        }
        try {
            Mail::to($v->user->email)->send($mailable);
        } catch (\Throwable) {
            // خطای SMTP نباید اقدام ادمین را متوقف کند.
        }
    }
}
