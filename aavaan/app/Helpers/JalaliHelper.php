<?php

namespace App\Helpers;

class JalaliHelper
{
    private static array $monthNames = [
        'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور',
        'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند',
    ];

    public static function toDate(\DateTimeInterface $date): string
    {
        [$jy, $jm, $jd] = self::convert((int)$date->format('Y'), (int)$date->format('n'), (int)$date->format('j'));
        return sprintf('%04d/%02d/%02d', $jy, $jm, $jd);
    }

    public static function toFull(\DateTimeInterface $date): string
    {
        [$jy, $jm, $jd] = self::convert((int)$date->format('Y'), (int)$date->format('n'), (int)$date->format('j'));
        return "{$jd} " . self::$monthNames[$jm - 1] . " {$jy}";
    }

    public static function toMonthYear(\DateTimeInterface $date): string
    {
        [$jy, $jm] = self::convert((int)$date->format('Y'), (int)$date->format('n'), (int)$date->format('j'));
        return self::$monthNames[$jm - 1] . ' ' . $jy;
    }

    public static function monthName(int $jm): string
    {
        return self::$monthNames[$jm - 1] ?? '';
    }

    /**
     * Converts Gregorian date to Jalali. Returns [year, month, day].
     */
    public static function convert(int $gy, int $gm, int $gd): array
    {
        $g_days = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        $j_days = [31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29];

        $gy -= 1600;
        $gm -= 1;
        $gd -= 1;

        $g_day_no = 365 * $gy
            + (int)(($gy + 3) / 4)
            - (int)(($gy + 99) / 100)
            + (int)(($gy + 399) / 400);

        for ($i = 0; $i < $gm; ++$i) {
            $g_day_no += $g_days[$i];
        }
        $origGy = $gy + 1600;
        if ($gm > 1 && (($origGy % 4 === 0 && $origGy % 100 !== 0) || ($origGy % 400 === 0))) {
            ++$g_day_no;
        }
        $g_day_no += $gd;

        $j_day_no = $g_day_no - 79;
        $j_np     = (int)($j_day_no / 12053);
        $j_day_no %= 12053;

        $jy        = 979 + 33 * $j_np + 4 * (int)($j_day_no / 1461);
        $j_day_no %= 1461;

        if ($j_day_no >= 366) {
            $jy       += (int)(($j_day_no - 1) / 365);
            $j_day_no  = ($j_day_no - 1) % 365;
        }

        $jm = 0;
        for ($i = 0; $i < 11 && $j_day_no >= $j_days[$i]; ++$i) {
            $j_day_no -= $j_days[$i];
            $jm++;
        }

        return [$jy, $jm + 1, $j_day_no + 1];
    }
}
