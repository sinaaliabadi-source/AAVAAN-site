<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // @artistField(...) — نمایش یک فیلد حساس فقط در صورت دسترسیِ پرداخت‌شده.
        // در قالب باید متغیر $artistUserId در دسترس باشد.
        Blade::directive('artistField', function ($expression) {
            return "<?php echo \\App\\Helpers\\ArtistPrivacy::hasAccess(auth()->user(), \$artistUserId)"
                . " ? $expression"
                . " : '<span class=\"locked-field\">🔒 بعد از پرداخت نمایش داده می‌شود</span>'; ?>";
        });

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->input('email') . '|' . $request->ip());
        });

        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        RateLimiter::for('forgot-password', function (Request $request) {
            return Limit::perMinute(5)->by($request->input('email') . '|' . $request->ip());
        });

        RateLimiter::for('contact', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // هنرباز — حداکثر ۳ ثبت‌نام از هر IP در ساعت
        RateLimiter::for('honarbaz-register', function (Request $request) {
            return Limit::perHour(3)->by($request->ip());
        });

        // هنرباز — حداکثر ۵ درخواست رأی از هر IP در روز
        RateLimiter::for('honarbaz-vote', function (Request $request) {
            return Limit::perDay(5)->by($request->ip());
        });
    }
}
