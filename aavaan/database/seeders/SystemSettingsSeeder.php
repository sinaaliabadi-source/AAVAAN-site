<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemSettingsSeeder extends Seeder {
    public function run(): void {
        if (DB::getDriverName() !== 'sqlite') DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('system_settings')->truncate();
        if (DB::getDriverName() !== 'sqlite') DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $settings = [
            ['key' => 'subscription_monthly_price', 'value' => '150000', 'label_fa' => 'قیمت اشتراک ماهانه (تومان)', 'group' => 'pricing'],
            ['key' => 'subscription_yearly_price',  'value' => '1500000','label_fa' => 'قیمت اشتراک سالانه (تومان)', 'group' => 'pricing'],
            ['key' => 'production_access_price',    'value' => '200000', 'label_fa' => 'قیمت دسترسی تیم تولید',      'group' => 'pricing'],
            ['key' => 'max_photos_per_artist',      'value' => '30',     'label_fa' => 'حداکثر عکس هر هنرمند',       'group' => 'limits'],
            ['key' => 'site_maintenance_mode',      'value' => '0',      'label_fa' => 'حالت تعمیر و نگهداری',       'group' => 'general'],
            ['key' => 'festival_active',            'value' => '1',      'label_fa' => 'جشنوارهٔ افتتاح فعال باشد (رایگان تا پایان تابستان)', 'group' => 'general'],
            ['key' => 'festival_ends_at',           'value' => '2026-09-22', 'label_fa' => 'تاریخ پایان جشنواره (میلادی، مثل 2026-09-22)',      'group' => 'general'],
        ];

        DB::table('system_settings')->insert($settings);
    }
}
