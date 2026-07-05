<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestSpecialtyVerificationRequest;
use App\Models\ArtistSpecialty;
use App\Models\Verification;

/**
 * درخواست تأیید تخصص از سمت هنرمند.
 */
class ArtistVerificationController extends Controller
{
    public function store(RequestSpecialtyVerificationRequest $request, ArtistSpecialty $specialty)
    {
        if ($specialty->user_id !== auth()->id()) {
            abort(403);
        }

        // فقط یک درخواست pending هم‌زمان برای هر تخصص.
        $latest = $specialty->latestVerification()->first();
        if ($latest && $latest->isPending()) {
            return back()->with('error', 'برای این تخصص یک درخواست تأیید در انتظار بررسی دارید.');
        }
        if ($latest && $latest->isApproved()) {
            return back()->with('error', 'این تخصص قبلاً تأیید شده است.');
        }

        // throttle منطقی: حداکثر ۵ درخواست تأیید تخصص در روز برای هر کاربر.
        $todayCount = Verification::where('user_id', auth()->id())
            ->where('type', Verification::TYPE_SPECIALTY)
            ->whereDate('created_at', today())
            ->count();
        if ($todayCount >= 5) {
            return back()->with('error', 'به سقف ۵ درخواست تأیید در روز رسیده‌اید. لطفاً فردا دوباره تلاش کنید.');
        }

        $data = $request->validated();

        Verification::create([
            'user_id'             => auth()->id(),
            'artist_specialty_id' => $specialty->id,
            'type'                => Verification::TYPE_SPECIALTY,
            'artist_note'         => $data['artist_note'],
            'evidence_links'      => $data['evidence_links'] ?? [],
            'status'              => Verification::STATUS_PENDING,
        ]);

        return back()->with('success', 'درخواست تأیید تخصص ثبت شد و در انتظار بررسی است.');
    }
}
