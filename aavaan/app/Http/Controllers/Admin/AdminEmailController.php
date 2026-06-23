<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdminBroadcastMail;
use App\Models\User;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminEmailController extends Controller
{
    use LogsAdminActivity;

    public function index()
    {
        $artistCount     = User::where('role', 'artist')->count();
        $productionCount = User::where('role', 'production')->count();
        $totalCount      = $artistCount + $productionCount;

        return view('admin.email.index', compact('artistCount', 'productionCount', 'totalCount'));
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'recipient_type' => 'required|in:all,artists,production,specific',
            'specific_email' => 'required_if:recipient_type,specific|nullable|email|max:200',
            'subject'        => 'required|string|max:150',
            'body'           => 'required|string|max:5000',
        ], [
            'recipient_type.required' => 'لطفاً گیرنده را انتخاب کنید.',
            'recipient_type.in'       => 'نوع گیرنده نامعتبر است.',
            'specific_email.required_if' => 'آدرس ایمیل الزامی است.',
            'specific_email.email'    => 'فرمت ایمیل صحیح نیست.',
            'subject.required'        => 'موضوع ایمیل الزامی است.',
            'subject.max'             => 'موضوع نباید بیشتر از ۱۵۰ کاراکتر باشد.',
            'body.required'           => 'متن ایمیل الزامی است.',
            'body.max'                => 'متن ایمیل نباید بیشتر از ۵۰۰۰ کاراکتر باشد.',
        ]);

        $recipients = $this->resolveRecipients($validated);

        if (empty($recipients)) {
            return back()->withErrors(['recipient_type' => 'هیچ گیرنده‌ای یافت نشد.'])->withInput();
        }

        $sent    = 0;
        $failed  = 0;
        $mailable = new AdminBroadcastMail($validated['subject'], $validated['body']);

        foreach ($recipients as $email) {
            try {
                Mail::to($email)->send(clone $mailable);
                $sent++;
            } catch (\Throwable) {
                $failed++;
            }
        }

        $this->logAdminActivity(
            'admin_email_sent',
            "ایمیل «{$validated['subject']}» به {$sent} کاربر ارسال شد." .
            ($failed ? " ({$failed} مورد ناموفق)" : '')
        );

        $message = "ایمیل با موفقیت به {$sent} کاربر ارسال شد.";
        if ($failed) {
            $message .= " ({$failed} مورد به‌دلیل خطای ارسال ناموفق بود.)";
        }

        return back()->with('success', $message);
    }

    // ────────────────────────────────────────────────────────────────
    // Helpers
    // ────────────────────────────────────────────────────────────────

    private function resolveRecipients(array $validated): array
    {
        return match ($validated['recipient_type']) {
            'all' => User::whereNotNull('email')
                ->whereIn('role', ['artist', 'production'])
                ->pluck('email')->toArray(),

            'artists' => User::where('role', 'artist')
                ->whereNotNull('email')
                ->pluck('email')->toArray(),

            'production' => User::where('role', 'production')
                ->whereNotNull('email')
                ->pluck('email')->toArray(),

            'specific' => array_filter([$validated['specific_email'] ?? null]),

            default => [],
        };
    }
}
