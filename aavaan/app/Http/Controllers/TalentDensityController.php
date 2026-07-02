<?php

namespace App\Http\Controllers;

use App\Models\ArtistProfile;
use Illuminate\Http\JsonResponse;

class TalentDensityController extends Controller
{
    /**
     * تراکم هنرمندان فعال به تفکیک شهر.
     *
     * تعداد پروفایل‌های فعال هر شهر را از جدول artist_profiles گروه‌بندی
     * می‌کند و مختصات جغرافیایی هر شهر را از config/city_coordinates.php
     * ضمیمه می‌کند تا نقشه‌ی صفحه‌ی اصلی بتواند پین‌ها را رسم کند.
     *
     * خروجی نمونه:
     * {
     *   "cities": [
     *     { "city": "تهران", "count": 128, "lat": 35.6892, "lng": 51.3890 },
     *     ...
     *   ],
     *   "total": 260,
     *   "max": 128
     * }
     */
    public function index(): JsonResponse
    {
        $coordinates = config('city_coordinates', []);

        $counts = ArtistProfile::query()
            ->where('is_active', true)
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->selectRaw('city, COUNT(*) as count')
            ->groupBy('city')
            ->pluck('count', 'city');

        $cities = [];
        $max = 0;

        foreach ($counts as $city => $count) {
            $name = trim($city);

            if (! isset($coordinates[$name])) {
                // شهری که مختصاتش را نداریم، روی نقشه رسم نمی‌شود.
                continue;
            }

            [$lat, $lng] = $coordinates[$name];

            $cities[] = [
                'city'  => $name,
                'count' => (int) $count,
                'lat'   => (float) $lat,
                'lng'   => (float) $lng,
            ];

            $max = max($max, (int) $count);
        }

        // پرتراکم‌ترین شهرها اول
        usort($cities, fn ($a, $b) => $b['count'] <=> $a['count']);

        return response()->json([
            'cities' => $cities,
            'total'  => array_sum(array_column($cities, 'count')),
            'max'    => $max,
        ]);
    }
}
