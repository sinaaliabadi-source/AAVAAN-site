<?php

namespace App\Support;

use App\Helpers\JalaliHelper;
use App\Models\SystemSetting;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * «جشنوارهٔ افتتاح آوان» — لایه‌ای روی زیرساخت پرداخت که تا پایان تابستان،
 * عضویت هنرمندان و دسترسی تیم‌های تولید را رایگان می‌کند. این لایه بعداً با
 * خاموش‌کردن کلید تنظیمات، بدون تغییر کد محو می‌شود.
 *
 * منبع حقیقت: دو کلید system_settings —
 *   festival_active   (بولی)   → روشن/خاموش بودن دستیِ جشنواره
 *   festival_ends_at  (تاریخ)  → پایان جشنواره (پیش‌فرض ۳۱ شهریور = 2026-09-22)
 */
class Festival
{
    private const CACHE_KEY = 'festival_state';
    private const CACHE_TTL = 60; // ثانیه — کش کوتاه تا تغییر تنظیمات سریع اثر کند.

    /**
     * آیا جشنواره همین حالا فعال است؟ (کلید روشن باشد + تاریخ پایان نگذشته باشد.)
     */
    public static function active(): bool
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            $flag = SystemSetting::get('festival_active', '1');
            if (! in_array((string) $flag, ['1', 'true', 'on'], true)) {
                return false;
            }

            $endsAt = self::endsAt();
            if ($endsAt !== null && $endsAt->isPast()) {
                return false;
            }

            return true;
        });
    }

    /**
     * تاریخ پایان جشنواره (پایانِ همان روز) یا null در نبود مقدار معتبر.
     */
    public static function endsAt(): ?Carbon
    {
        $raw = SystemSetting::get('festival_ends_at');
        if (! $raw) {
            return null;
        }

        try {
            return Carbon::parse($raw)->endOfDay();
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * نمایش فارسی تاریخ پایان جشنواره برای رابط کاربری.
     * اگر تاریخ معتبر نبود، عبارت ثابت «تا پایان تابستان» برگردانده می‌شود.
     */
    public static function endsAtJalali(): string
    {
        $endsAt = self::endsAt();
        if ($endsAt === null) {
            return 'تا پایان تابستان';
        }

        return JalaliHelper::toFull($endsAt);
    }

    /**
     * پاک‌کردن کش وضعیت جشنواره (پس از تغییر تنظیمات توسط ادمین).
     */
    public static function forgetCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * ساخت اشتراک جشنواره برای یک هنرمند به‌صورت idempotent.
     * اگر هنرمند از قبل اشتراک فعال دارد (جشنواره یا پولی)، چیزی ساخته نمی‌شود.
     *
     * @return Subscription|null  اشتراک تازه‌ساخته‌شده یا null اگر لازم نبود.
     */
    public static function grantSubscription(User $user): ?Subscription
    {
        if (! $user->isArtist()) {
            return null;
        }
        if (! self::active()) {
            return null;
        }
        // اشتراک فعال موجود → دوباره نساز (idempotent).
        if ($user->activeSubscription() !== null) {
            return null;
        }

        $endsAt = self::endsAt() ?? Carbon::parse('2026-09-22')->endOfDay();

        $subscription = Subscription::create([
            'user_id'    => $user->id,
            'plan'       => 'festival',
            'status'     => 'active',
            'starts_at'  => now(),
            'expires_at' => $endsAt,
            'admin_note' => 'جشنواره افتتاح',
        ]);

        // پرداخت ثبتیِ رایگان (منبع دستی، مبلغ صفر) برای رهگیری در گزارش‌ها.
        $subscription->payment()->create([
            'user_id'        => $user->id,
            'amount'         => 0,
            'gateway'        => 'festival',
            'payment_source' => 'manual',
            'status'         => 'paid',
            'paid_at'        => now(),
        ]);

        // پروفایل هنرمند در دورهٔ جشنواره فعال می‌شود تا در نتایج دیده شود.
        $user->artistProfile?->update(['is_active' => true]);

        return $subscription;
    }
}
