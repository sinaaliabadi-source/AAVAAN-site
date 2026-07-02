<?php

namespace Database\Seeders;

use App\Models\Program;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class HonarbazSeeder extends Seeder
{
    public function run(): void
    {
        Program::updateOrCreate(
            ['slug' => 'honarbaz'],
            [
                'title'       => 'هنرباز',
                'description' => 'برنامه استعدادیابی و رئالیتی شوی کودکان ایران — کشف استعدادهای پنهان کودکان در مناطق کم‌برخوردار، روستاها و نقاط کمتر دیده‌شده ایران. اولین استعدادیابی روستامحور ایران با سفر خانه سیار به سراسر کشور.',
                'status'      => 'active',
                'starts_at'   => Carbon::now(),
                'ends_at'     => Carbon::now()->addMonths(6),
                'meta'        => [
                    'registration_enabled' => true,
                    'voting_enabled'       => true,
                    'director'             => 'مجید بذرپاچ',
                    'designer'             => 'فرشته صفری',
                    'designer_reg_no'      => '5504005041',
                    'structure'            => '۷۵ قسمت یک ساعته در فصل اول، پخش از پلتفرم',
                    'subtitle'             => 'هر کودک یک استعداد دارد، هر روستا یک داستان',
                ],
            ]
        );
    }
}
