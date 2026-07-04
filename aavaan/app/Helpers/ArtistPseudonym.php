<?php

namespace App\Helpers;

use App\Models\ArtistProfile;
use Illuminate\Support\Facades\Cache;

/**
 * کد مستعار پایدار برای هنرمند در «کست‌یاب» (کستینگ ناشناس).
 *
 * پیش از باز کردن (unlock)، نه نام و نه شناسهٔ داخلی دیتابیس هنرمند نباید لو برود.
 * به جای «هنرمند #ID» یک کد مستعار پایدار از روی هش کلید برنامه ساخته می‌شود؛
 * چون به app.key وابسته است، از بیرون قابل حدس/بازگردانی نیست.
 */
class ArtistPseudonym
{
    /**
     * کد مستعار پایدار برای یک شناسهٔ پروفایل هنرمند: «آوان-XXXXXX».
     */
    public static function code(int $artistProfileId): string
    {
        return 'آوان-' . self::hash($artistProfileId);
    }

    /**
     * فقط بخش هش (۶ کاراکتر) — برای پارامتر route تصویر ناشناس.
     */
    public static function hash(int $artistProfileId): string
    {
        return substr(sha1($artistProfileId . config('app.key')), 0, 6);
    }

    /**
     * بازگرداندن شناسهٔ پروفایل هنرمندِ فعال از روی هشِ مستعار.
     * چون هش یک‌طرفه است، نگاشت هش→شناسه برای هنرمندان فعال ساخته و کش می‌شود.
     */
    public static function resolveActiveId(string $hash): ?int
    {
        $map = Cache::remember('artist_pseudonym_map', 300, function () {
            return ArtistProfile::where('is_active', true)
                ->pluck('id')
                ->mapWithKeys(fn ($id) => [self::hash($id) => $id])
                ->all();
        });

        return $map[$hash] ?? null;
    }
}
