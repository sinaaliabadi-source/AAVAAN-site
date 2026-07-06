<?php

namespace App\Http\Controllers;

use App\Support\Festival;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

/**
 * فعال‌سازی حساب هنرمند با تأیید ایمیل (مکانیزم استاندارد Laravel).
 * فقط نقش artist مشمول اجبار تأیید است (از طریق middleware روی گروه داشبورد هنرمند).
 */
class EmailVerificationController extends Controller
{
    /** صفحهٔ «ایمیل خود را بررسی کنید». */
    public function notice(Request $request)
    {
        // اگر قبلاً تأیید شده، به داشبورد برگردد.
        if ($request->user()?->hasVerifiedEmail()) {
            return redirect()->route('artist.dashboard');
        }

        return view('auth.verify-email');
    }

    /** تأیید ایمیل از روی لینک امضاشده. */
    public function verify(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('artist.dashboard')->with('info', 'حساب شما از قبل فعال شده است.');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));

            // اشتراک جشنواره فقط پس از تأیید ایمیل ساخته می‌شود (نه هنگام ثبت‌نام).
            if (Festival::active()) {
                Festival::grantSubscription($request->user()->fresh());
            }
        }

        return redirect()->route('artist.dashboard')->with('success', 'حساب شما با موفقیت فعال شد. خوش آمدید!');
    }

    /** ارسال مجدد ایمیل تأیید (با throttle استاندارد ۶ در دقیقه روی route). */
    public function send(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('artist.dashboard');
        }

        // الگوی امن: شکست SMTP نباید صفحهٔ ۵۰۰ بدهد.
        try {
            $request->user()->sendEmailVerificationNotification();
        } catch (\Throwable) {
            return back()->with('error', 'ایمیل ارسال نشد. لطفاً چند لحظه بعد دوباره تلاش کنید.');
        }

        return back()->with('success', 'ایمیل فعال‌سازی دوباره ارسال شد. صندوق ورودی خود را بررسی کنید.');
    }
}
