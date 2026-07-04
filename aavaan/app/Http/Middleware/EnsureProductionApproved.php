<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * تیم‌های تولید بدون تأیید ادمین نباید به داشبورد/جستجو دسترسی داشته باشند.
 * کاربران production با وضعیت غیر approved به صفحهٔ «در انتظار بررسی» هدایت می‌شوند.
 *
 * توجه: پیش‌فرض ستون approval_status روی approved است، پس کاربران قدیمی سرور قفل نمی‌شوند.
 */
class EnsureProductionApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->role === 'production' && $user->approval_status !== 'approved') {
            return redirect()->route('production.pending-approval');
        }

        return $next($request);
    }
}
