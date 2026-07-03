<?php

namespace App\Helpers;

use App\Models\ProductionAccessLog;
use App\Models\User;

/**
 * پنهان‌سازی اطلاعات هنرمند در بخش کستینگ تا قبل از پرداخت (کستینگ ناشناس).
 *
 * قانون: اطلاعات تماس و هویتیِ هنرمند فقط زمانی نمایش داده می‌شود که بازدیدکننده
 * یکی از این‌ها باشد: ادمین، خودِ هنرمند، یا تیم تولیدی که برای این هنرمند
 * رکورد دسترسیِ پرداخت‌شده (ProductionAccessLog) دارد.
 *
 * توجه: در این پروژه سطح دسترسیِ پرداخت‌شده با جدول production_access_logs
 * (production_user_id + artist_profile_id) نگهداری می‌شود، نه با status/expires_at.
 * این استثنا شامل بخش «هنرباز» نمی‌شود؛ آنجا این فیلتر اصلاً فراخوانی نمی‌شود.
 */
class ArtistPrivacy
{
    /**
     * آیا بازدیدکنندهٔ فعلی به اطلاعات کاملِ این هنرمند دسترسیِ پرداخت‌شده دارد؟
     *
     * @param  User|null  $viewer         کاربر بازدیدکننده (یا null برای مهمان)
     * @param  int        $artistUserId   شناسهٔ کاربریِ هنرمند
     */
    public static function hasAccess(?User $viewer, int $artistUserId): bool
    {
        if (! $viewer) {
            return false;
        }
        if ($viewer->role === 'admin') {
            return true;
        }
        if ($viewer->id === $artistUserId) {
            return true;
        }
        if ($viewer->role !== 'production') {
            return false;
        }

        return ProductionAccessLog::where('production_user_id', $viewer->id)
            ->whereHas('artistProfile', fn ($q) => $q->where('user_id', $artistUserId))
            ->exists();
    }

    /**
     * فیلتر اطلاعات هنرمند بر اساس سطح دسترسی.
     * در نبود دسترسی، اطلاعات حساس حذف/مخفی می‌شوند.
     *
     * @param  array  $profile     آرایهٔ اطلاعات هنرمند
     * @param  bool   $hasAccess   نتیجهٔ hasAccess()
     */
    public static function filterProfile(array $profile, bool $hasAccess): array
    {
        if ($hasAccess) {
            return $profile;
        }

        // نام کامل → نام مخفی‌شده
        if (array_key_exists('full_name', $profile)) {
            $profile['full_name'] = self::maskName($profile['full_name'] ?? '');
        }

        // اطلاعات تماس و شبکه‌های اجتماعی حذف می‌شوند
        foreach ([
            'phone_contact', 'email_contact',
            'instagram', 'instagram_url',
            'telegram', 'telegram_url',
            'linkedin_url', 'imdb_url',
            'website_url', 'youtube_url', 'vimeo_url',
        ] as $key) {
            if (array_key_exists($key, $profile)) {
                $profile[$key] = null;
            }
        }

        return $profile;
    }

    /**
     * نمایش نام مخفی‌شده — فقط نام کوچک و حرف اولِ نام خانوادگی. مثال: «علی ر.»
     */
    public static function maskName(string $name): string
    {
        $name = trim($name);
        if ($name === '') {
            return '***** *****';
        }

        $parts = preg_split('/\s+/', $name);
        if (count($parts) === 1) {
            return mb_substr($parts[0], 0, 1) . '.';
        }

        return $parts[0] . ' ' . mb_substr($parts[1], 0, 1) . '.';
    }
}
