<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * افزودن دو کلید تنظیمات «جشنوارهٔ افتتاح» به جدول system_settings.
     * چون در دیپلوی تولید فقط migrate اجرا می‌شود (نه seeder)، این کلیدها باید از طریق
     * migration درج شوند. درج به‌صورت idempotent است (کلید موجود دوباره ساخته نمی‌شود).
     */
    public function up(): void
    {
        $rows = [
            [
                'key'      => 'festival_active',
                'value'    => '1',
                'label_fa' => 'جشنوارهٔ افتتاح فعال باشد (رایگان تا پایان تابستان)',
                'group'    => 'general',
            ],
            [
                'key'      => 'festival_ends_at',
                'value'    => '2026-09-22',
                'label_fa' => 'تاریخ پایان جشنواره (میلادی، مثل 2026-09-22)',
                'group'    => 'general',
            ],
        ];

        foreach ($rows as $row) {
            $exists = DB::table('system_settings')->where('key', $row['key'])->exists();
            if (! $exists) {
                DB::table('system_settings')->insert($row + ['updated_at' => now()]);
            }
        }
    }

    public function down(): void
    {
        DB::table('system_settings')->whereIn('key', ['festival_active', 'festival_ends_at'])->delete();
    }
};
