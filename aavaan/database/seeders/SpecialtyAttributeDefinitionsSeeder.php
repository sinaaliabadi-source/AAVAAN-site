<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpecialtyAttributeDefinitionsSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::getDriverName() === 'mysql') DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('specialty_attribute_definitions')->truncate();
        if (DB::getDriverName() === 'mysql') DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $rows = [];
        $sortOrder = 0;

        $addField = function (
            int    $categoryId,
            string $key,
            string $labelFa,
            string $fieldType,
            bool   $isRequired = false,
            ?array $options = null,
            bool   $isPreium = false,
            string $visibility = 'public',
        ) use (&$rows, &$sortOrder): void {
            $rows[] = [
                'category_id' => $categoryId,
                'key'         => $key,
                'label_fa'    => $labelFa,
                'field_type'  => $fieldType,
                'options'     => $options !== null ? json_encode($options) : null,
                'is_required' => $isRequired ? 1 : 0,
                'is_premium'  => $isPreium ? 1 : 0,
                'visibility'  => $visibility,
                'sort_order'  => ++$sortOrder,
                'created_at'  => now(),
                'updated_at'  => now(),
            ];
        };

        // Helper for select option arrays
        $opt = fn(string $value, string $label = '') => [
            'value' => $value,
            'label' => $label ?: $value,
        ];

        // ─── 1. بازیگری و اجرا ────────────────────────────────────────────
        $addField(1, 'sub_specialty', 'زیرتخصص', 'select', true, [
            $opt('actor_cinema_tv', 'بازیگر سینما/تلویزیون'),
            $opt('actor_theater', 'بازیگر تئاتر'),
            $opt('actor_child', 'بازیگر کودک'),
            $opt('supporting', 'نقش مکمل/سیاهی‌لشکر'),
            $opt('voice_dubbing', 'گوینده/دوبلور'),
            $opt('host', 'مجری'),
        ]);
        $addField(1, 'height_cm', 'قد (سانتی‌متر)', 'number');
        $addField(1, 'weight_kg', 'وزن (کیلوگرم)', 'number');
        $addField(1, 'hair_color', 'رنگ مو', 'select', false, [
            $opt('black', 'مشکی'), $opt('dark_brown', 'قهوه‌ای تیره'),
            $opt('brown', 'قهوه‌ای'), $opt('light_brown', 'قهوه‌ای روشن'),
            $opt('blonde', 'بلوند'), $opt('red', 'قرمز'),
            $opt('white', 'سفید'), $opt('gray', 'خاکستری'),
        ]);
        $addField(1, 'eye_color', 'رنگ چشم', 'select', false, [
            $opt('black', 'مشکی'), $opt('dark_brown', 'قهوه‌ای تیره'),
            $opt('brown', 'قهوه‌ای'), $opt('green', 'سبز'),
            $opt('blue', 'آبی'), $opt('hazel', 'فندقی'),
            $opt('gray', 'خاکستری'),
        ]);
        $addField(1, 'accents', 'لهجه‌های تسلط', 'multiselect', false, [
            $opt('tehrani', 'تهرانی'), $opt('esfahani', 'اصفهانی'),
            $opt('mashhadi', 'مشهدی'), $opt('azeri', 'آذری'),
            $opt('kurdish', 'کردی'), $opt('gilaki', 'گیلکی'),
            $opt('english', 'انگلیسی'), $opt('arabic', 'عربی'),
        ]);
        $addField(1, 'special_skills', 'مهارت‌های ویژه', 'textarea', false);
        $addField(1, 'acting_method', 'متد بازیگری', 'multiselect', false, [
            $opt('stanislavski', 'استانیسلاوسکی'),
            $opt('method_acting', 'متد اکتینگ'),
            $opt('meisner', 'مایزنر'),
            $opt('brecht', 'برشت'),
            $opt('other', 'سایر'),
        ]);

        // ─── 2. کاسکادوری و بدل ──────────────────────────────────────────
        $addField(2, 'sub_specialty', 'زیرتخصص', 'select', true, [
            $opt('stuntman', 'کاسکادور'),
            $opt('stunt_double', 'بدل بازیگر'),
            $opt('action_coordinator', 'هماهنگ‌کننده صحنه‌های اکشن'),
        ]);
        $addField(2, 'stunt_types', 'انواع استانت', 'multiselect', true, [
            $opt('heights', 'ارتفاع'), $opt('fire', 'آتش'),
            $opt('water', 'آب'), $opt('vehicle', 'خودرو'),
            $opt('martial_arts', 'رزمی'), $opt('wire_work', 'وایرورک'),
            $opt('explosions', 'انفجار'),
        ]);
        $addField(2, 'certifications', 'گواهینامه‌ها و تأییدیه‌ها', 'textarea');
        $addField(2, 'height_cm', 'قد (سانتی‌متر)', 'number');
        $addField(2, 'weight_kg', 'وزن (کیلوگرم)', 'number');
        $addField(2, 'hair_color', 'رنگ مو', 'select', false, [
            $opt('black', 'مشکی'), $opt('dark_brown', 'قهوه‌ای تیره'),
            $opt('brown', 'قهوه‌ای'), $opt('blonde', 'بلوند'),
            $opt('white', 'سفید'), $opt('gray', 'خاکستری'),
        ]);

        // ─── 3. کارگردانی و دستیاری صحنه ────────────────────────────────
        $addField(3, 'sub_specialty', 'زیرتخصص', 'select', true, [
            $opt('director', 'کارگردان'),
            $opt('first_ad', 'دستیار اول کارگردان'),
            $opt('second_ad', 'دستیار دوم کارگردان'),
            $opt('script_supervisor', 'منشی صحنه (Continuity)'),
            $opt('casting_director', 'کارگردان کستینگ'),
        ]);
        $addField(3, 'genres', 'ژانرهای تجربه‌شده', 'multiselect', false, [
            $opt('drama', 'درام'), $opt('comedy', 'کمدی'),
            $opt('action', 'اکشن'), $opt('thriller', 'هیجانی'),
            $opt('documentary', 'مستند'), $opt('animation', 'انیمیشن'),
            $opt('commercial', 'تبلیغاتی'), $opt('music_video', 'موزیک ویدیو'),
            $opt('short_film', 'فیلم کوتاه'),
        ]);
        $addField(3, 'software', 'نرم‌افزارهای تخصصی', 'multiselect', false, [
            $opt('movie_magic', 'Movie Magic'),
            $opt('final_draft', 'Final Draft'),
            $opt('arc_studio', 'Arc Studio'),
            $opt('celtx', 'Celtx'),
        ]);

        // ─── 4. نویسندگی ─────────────────────────────────────────────────
        $addField(4, 'sub_specialty', 'زیرتخصص', 'select', true, [
            $opt('screenwriter', 'فیلمنامه‌نویس'),
            $opt('dialogue_writer', 'دیالوگ‌نویس'),
            $opt('story_writer', 'نویسنده داستان/طرح'),
            $opt('commercial_writer', 'نویسنده تیزر و تبلیغات'),
        ]);
        $addField(4, 'genres', 'ژانرهای نوشتاری', 'multiselect', false, [
            $opt('drama', 'درام'), $opt('comedy', 'کمدی'),
            $opt('action', 'اکشن'), $opt('thriller', 'هیجانی'),
            $opt('children', 'کودک و نوجوان'), $opt('historical', 'تاریخی'),
            $opt('social', 'اجتماعی'), $opt('commercial', 'تبلیغاتی'),
        ]);
        $addField(4, 'writing_languages', 'زبان‌های نوشتاری', 'multiselect', false, [
            $opt('persian', 'فارسی'), $opt('english', 'انگلیسی'),
            $opt('arabic', 'عربی'),
        ]);
        $addField(4, 'software', 'نرم‌افزارهای نویسندگی', 'multiselect', false, [
            $opt('final_draft', 'Final Draft'), $opt('celtx', 'Celtx'),
            $opt('arc_studio', 'Arc Studio'), $opt('fade_in', 'Fade In'),
            $opt('word', 'Microsoft Word'),
        ]);

        // ─── 5. تصویربرداری ──────────────────────────────────────────────
        $addField(5, 'sub_specialty', 'زیرتخصص', 'select', true, [
            $opt('dop', 'مدیر فیلمبرداری'),
            $opt('camera_operator', 'اپراتور دوربین'),
            $opt('focus_puller', 'فوکوس‌پولر'),
            $opt('drone_operator', 'اپراتور پهپاد/دکل'),
        ]);
        $addField(5, 'cameras', 'دوربین‌های تسلط', 'multiselect', false, [
            $opt('arri_alexa', 'ARRI Alexa'), $opt('arri_amira', 'ARRI Amira'),
            $opt('red', 'RED'), $opt('sony_venice', 'Sony Venice'),
            $opt('sony_fx', 'Sony FX series'), $opt('blackmagic', 'Blackmagic'),
            $opt('canon_cinema', 'Canon Cinema'), $opt('panasonic', 'Panasonic'),
        ]);
        $addField(5, 'shooting_styles', 'سبک‌های تصویربرداری', 'multiselect', false, [
            $opt('cinema', 'سینمایی'), $opt('documentary', 'مستند'),
            $opt('commercial', 'تبلیغاتی'), $opt('music_video', 'موزیک ویدیو'),
            $opt('aerial', 'هوایی'), $opt('underwater', 'زیر آب'),
        ]);
        $addField(5, 'owns_equipment', 'صاحب تجهیزات است', 'boolean', false);

        // ─── 6. نورپردازی ────────────────────────────────────────────────
        $addField(6, 'sub_specialty', 'زیرتخصص', 'select', true, [
            $opt('dp_lighting', 'مدیر نور'),
            $opt('gaffer', 'گافر'),
            $opt('lighting_assistant', 'دستیار نور'),
        ]);
        $addField(6, 'equipment', 'تجهیزات تسلط', 'multiselect', false, [
            $opt('hmi', 'HMI'), $opt('led_panel', 'LED Panel'),
            $opt('tungsten', 'Tungsten'), $opt('fresnel', 'Fresnel'),
            $opt('practical', 'Practical'), $opt('dmx_control', 'DMX Control'),
        ]);
        $addField(6, 'lighting_styles', 'سبک‌های نورپردازی', 'multiselect', false, [
            $opt('natural', 'طبیعی'), $opt('studio', 'استودیویی'),
            $opt('chiaroscuro', 'کیاروسکورو'), $opt('high_key', 'High Key'),
            $opt('low_key', 'Low Key'),
        ]);

        // ─── 7. صدا ──────────────────────────────────────────────────────
        $addField(7, 'sub_specialty', 'زیرتخصص', 'select', true, [
            $opt('production_sound_mixer', 'صدابردار سرصحنه'),
            $opt('boom_operator', 'بوم اپراتور'),
            $opt('sound_designer', 'طراح صدا'),
            $opt('mixer', 'میکس'),
            $opt('mastering', 'مسترینگ'),
        ]);
        $addField(7, 'software', 'نرم‌افزارهای صدا', 'multiselect', false, [
            $opt('pro_tools', 'Pro Tools'), $opt('logic_pro', 'Logic Pro'),
            $opt('cubase', 'Cubase'), $opt('reaper', 'Reaper'),
            $opt('nuendo', 'Nuendo'), $opt('ableton', 'Ableton Live'),
        ]);
        $addField(7, 'microphones', 'تجهیزات میکروفون', 'multiselect', false, [
            $opt('sennheiser', 'Sennheiser'), $opt('lectrosonics', 'Lectrosonics'),
            $opt('sound_devices', 'Sound Devices'), $opt('zaxcom', 'Zaxcom'),
        ]);

        // ─── 8. موسیقی ────────────────────────────────────────────────────
        $addField(8, 'sub_specialty', 'زیرتخصص', 'select', true, [
            $opt('composer', 'آهنگساز'),
            $opt('arranger', 'تنظیم‌کننده'),
            $opt('musician', 'نوازنده'),
            $opt('conductor', 'رهبر ارکستر'),
            $opt('singer', 'خواننده'),
        ]);
        $addField(8, 'instruments', 'سازهای تسلط', 'multiselect', false, [
            $opt('piano', 'پیانو'), $opt('guitar', 'گیتار'),
            $opt('violin', 'ویولن'), $opt('tar', 'تار'),
            $opt('setar', 'سه‌تار'), $opt('santur', 'سنتور'),
            $opt('ney', 'نی'), $opt('daf', 'دف'),
            $opt('drums', 'درام'), $opt('bass', 'بیس'),
        ]);
        $addField(8, 'music_genres', 'ژانرهای موسیقی', 'multiselect', false, [
            $opt('film_score', 'موسیقی فیلم'), $opt('classical', 'کلاسیک'),
            $opt('traditional', 'سنتی ایرانی'), $opt('pop', 'پاپ'),
            $opt('jazz', 'جاز'), $opt('electronic', 'الکترونیک'),
        ]);
        $addField(8, 'voice_range', 'محدوده صدا (خوانندگان)', 'select', false, [
            $opt('soprano', 'سوپرانو'), $opt('mezzo_soprano', 'متزوسوپرانو'),
            $opt('alto', 'آلتو'), $opt('tenor', 'تنور'),
            $opt('baritone', 'باریتون'), $opt('bass', 'باس'),
        ]);
        $addField(8, 'daw_software', 'نرم‌افزار تولید موسیقی', 'multiselect', false, [
            $opt('logic_pro', 'Logic Pro'), $opt('ableton', 'Ableton Live'),
            $opt('pro_tools', 'Pro Tools'), $opt('cubase', 'Cubase'),
            $opt('fl_studio', 'FL Studio'), $opt('sibelius', 'Sibelius'),
        ]);

        // ─── 9. تدوین و پساتولید ─────────────────────────────────────────
        $addField(9, 'sub_specialty', 'زیرتخصص', 'select', true, [
            $opt('picture_editor', 'تدوینگر تصویر'),
            $opt('sound_editor', 'تدوینگر صدا'),
            $opt('colorist', 'رنگ‌گردان'),
            $opt('motion_graphics', 'موشن‌گرافیست'),
        ]);
        $addField(9, 'software', 'نرم‌افزارهای تسلط', 'multiselect', true, [
            $opt('premiere', 'Adobe Premiere'), $opt('avid', 'Avid Media Composer'),
            $opt('fcpx', 'Final Cut Pro X'), $opt('davinci', 'DaVinci Resolve'),
            $opt('after_effects', 'After Effects'), $opt('resolve_fusion', 'DaVinci Fusion'),
        ]);
        $addField(9, 'genres', 'ژانرهای تخصصی', 'multiselect', false, [
            $opt('cinema', 'سینمایی'), $opt('tv_series', 'سریال تلویزیونی'),
            $opt('documentary', 'مستند'), $opt('commercial', 'تبلیغاتی'),
            $opt('music_video', 'موزیک ویدیو'),
        ]);

        // ─── 10. جلوه‌های ویژه و CGI ─────────────────────────────────────
        $addField(10, 'sub_specialty', 'زیرتخصص', 'select', true, [
            $opt('vfx_artist', 'آرتیست جلوه‌های دیجیتال'),
            $opt('virtual_production', 'متخصص Virtual Production'),
            $opt('motion_capture', 'متخصص Motion Capture'),
        ]);
        $addField(10, 'software', 'نرم‌افزارهای تسلط', 'multiselect', true, [
            $opt('houdini', 'Houdini'), $opt('nuke', 'Nuke'),
            $opt('maya', 'Maya'), $opt('3dsmax', '3ds Max'),
            $opt('blender', 'Blender'), $opt('unreal', 'Unreal Engine'),
            $opt('unity', 'Unity'), $opt('cinema4d', 'Cinema 4D'),
            $opt('zbrush', 'ZBrush'),
        ]);
        $addField(10, 'specializations', 'تخصص‌های VFX', 'multiselect', false, [
            $opt('compositing', 'Compositing'), $opt('simulation', 'Simulation'),
            $opt('matte_painting', 'Matte Painting'), $opt('rigging', 'Rigging'),
            $opt('lighting_rendering', 'Lighting & Rendering'),
        ]);

        // ─── 11. طراحی صحنه ──────────────────────────────────────────────
        $addField(11, 'sub_specialty', 'زیرتخصص', 'select', true, [
            $opt('production_designer', 'طراح صحنه'),
            $opt('set_builder', 'سازنده دکور'),
            $opt('construction_manager', 'مدیر ساخت'),
        ]);
        $addField(11, 'styles', 'سبک‌های طراحی', 'multiselect', false, [
            $opt('realistic', 'رئالیستی'), $opt('period', 'تاریخی/دوره‌ای'),
            $opt('fantasy', 'فانتزی'), $opt('minimalist', 'مینیمال'),
            $opt('contemporary', 'معاصر'),
        ]);
        $addField(11, 'software', 'نرم‌افزارهای طراحی', 'multiselect', false, [
            $opt('autocad', 'AutoCAD'), $opt('sketchup', 'SketchUp'),
            $opt('vectorworks', 'Vectorworks'), $opt('archicad', 'ArchiCAD'),
            $opt('photoshop', 'Photoshop'),
        ]);

        // ─── 12. طراحی لباس ──────────────────────────────────────────────
        $addField(12, 'sub_specialty', 'زیرتخصص', 'select', true, [
            $opt('costume_designer', 'طراح لباس'),
            $opt('tailor', 'خیاط/دوزنده اختصاصی'),
            $opt('wardrobe_manager', 'مدیر کارگاه لباس'),
        ]);
        $addField(12, 'periods', 'دوره‌های تاریخی تخصصی', 'multiselect', false, [
            $opt('contemporary', 'معاصر'), $opt('qajar', 'قاجاریه'),
            $opt('pahlavi', 'پهلوی'), $opt('ancient', 'باستان'),
            $opt('traditional', 'سنتی/محلی'), $opt('fantasy', 'فانتزی'),
        ]);
        $addField(12, 'techniques', 'تکنیک‌های دوخت', 'multiselect', false, [
            $opt('pattern_making', 'الگوسازی'), $opt('hand_sewing', 'دوخت دستی'),
            $opt('machine_sewing', 'دوخت ماشین'), $opt('embroidery', 'گلدوزی'),
            $opt('aging', 'فرسوده‌سازی لباس'),
        ]);

        // ─── 13. گریم ────────────────────────────────────────────────────
        $addField(13, 'sub_specialty', 'زیرتخصص', 'select', true, [
            $opt('beauty_makeup', 'گریمور زیبایی'),
            $opt('character_makeup', 'گریم کاراکتر'),
            $opt('sfx_prosthetics', 'گریم پروتز/SFX'),
            $opt('hair_designer', 'طراح مو'),
        ]);
        $addField(13, 'techniques', 'تکنیک‌های گریم', 'multiselect', false, [
            $opt('airbrush', 'ایربراش'), $opt('sfx', 'SFX'),
            $opt('old_age', 'پیرسازی'), $opt('body_painting', 'باری‌پینتینگ'),
            $opt('prosthetics', 'پروتز'), $opt('wigs', 'کلاهگیس'),
        ]);
        $addField(13, 'sfx_types', 'تخصص SFX', 'multiselect', false, [
            $opt('wounds', 'زخم و جراحت'), $opt('aging', 'پیرسازی'),
            $opt('creature', 'موجودات'), $opt('dental', 'دندان مصنوعی'),
        ]);

        // ─── 14. عکاسی ───────────────────────────────────────────────────
        $addField(14, 'sub_specialty', 'زیرتخصص', 'select', true, [
            $opt('advertising', 'عکاس تبلیغاتی'),
            $opt('industrial', 'عکاس صنعتی'),
            $opt('bts', 'عکاس پشت‌صحنه'),
            $opt('portrait', 'عکاس پرتره'),
            $opt('fashion', 'عکاس مد'),
        ]);
        $addField(14, 'cameras', 'دوربین‌های اصلی', 'multiselect', false, [
            $opt('canon', 'Canon'), $opt('nikon', 'Nikon'),
            $opt('sony_alpha', 'Sony Alpha'), $opt('fujifilm', 'Fujifilm'),
            $opt('hasselblad', 'Hasselblad'), $opt('phase_one', 'Phase One'),
        ]);
        $addField(14, 'editing_software', 'نرم‌افزارهای ویرایش', 'multiselect', false, [
            $opt('lightroom', 'Lightroom'), $opt('photoshop', 'Photoshop'),
            $opt('capture_one', 'Capture One'), $opt('luminar', 'Luminar'),
        ]);
        $addField(14, 'owns_studio', 'استودیوی شخصی دارد', 'boolean');

        // ─── 15. تهیه و مدیریت تولید ─────────────────────────────────────
        $addField(15, 'sub_specialty', 'زیرتخصص', 'select', true, [
            $opt('producer', 'تهیه‌کننده'),
            $opt('executive_producer', 'تهیه‌کننده اجرایی'),
            $opt('production_manager', 'مدیر تولید'),
            $opt('location_manager', 'مدیر لوکیشن'),
            $opt('coordinator', 'برنامه‌ریز'),
        ]);
        $addField(15, 'budget_range', 'بازه بودجه مدیریت‌شده', 'select', false, [
            $opt('low', 'کم‌بودجه (زیر ۵۰۰ میلیون)'),
            $opt('mid', 'متوسط (۵۰۰ میلیون - ۳ میلیارد)'),
            $opt('high', 'بودجه بالا (بیش از ۳ میلیارد)'),
        ]);
        $addField(15, 'production_types', 'انواع تولید', 'multiselect', false, [
            $opt('cinema', 'سینمایی'), $opt('tv_series', 'سریال'),
            $opt('documentary', 'مستند'), $opt('commercial', 'تبلیغاتی'),
            $opt('music_video', 'موزیک ویدیو'), $opt('corporate', 'سازمانی'),
        ]);

        // ─── 16. کستینگ ──────────────────────────────────────────────────
        $addField(16, 'sub_specialty', 'زیرتخصص', 'select', true, [
            $opt('professional_casting', 'کارگردان کستینگ حرفه‌ای'),
            $opt('child_casting', 'کستینگ کودک'),
            $opt('commercial_casting', 'کستینگ تبلیغاتی'),
        ]);
        $addField(16, 'casting_types', 'انواع کستینگ', 'multiselect', false, [
            $opt('actors', 'بازیگران'), $opt('models', 'مدل'),
            $opt('extras', 'سیاهی‌لشکر'), $opt('voice_over', 'گوینده/دوبلور'),
            $opt('stunt', 'کاسکادور'),
        ]);
        $addField(16, 'software', 'نرم‌افزار کستینگ', 'multiselect', false, [
            $opt('casting_networks', 'Casting Networks'),
            $opt('spotlight', 'Spotlight'),
        ]);

        // ─── 17. روابط عمومی و بازاریابی ─────────────────────────────────
        $addField(17, 'sub_specialty', 'زیرتخصص', 'select', true, [
            $opt('pr_manager', 'مدیر روابط عمومی'),
            $opt('marketing_manager', 'مدیر بازاریابی'),
            $opt('social_media_manager', 'مدیر شبکه‌های اجتماعی'),
        ]);
        $addField(17, 'platforms', 'پلتفرم‌های تخصصی', 'multiselect', false, [
            $opt('instagram', 'اینستاگرام'), $opt('telegram', 'تلگرام'),
            $opt('youtube', 'یوتیوب'), $opt('aparat', 'آپارات'),
            $opt('twitter', 'توییتر/X'), $opt('linkedin', 'لینکدین'),
        ]);
        $addField(17, 'tools', 'ابزارهای بازاریابی', 'multiselect', false, [
            $opt('google_analytics', 'Google Analytics'),
            $opt('meta_ads', 'Meta Ads'),
            $opt('google_ads', 'Google Ads'),
            $opt('mailchimp', 'Mailchimp'),
        ]);

        // ─── 18. انیمیشن ─────────────────────────────────────────────────
        $addField(18, 'sub_specialty', 'زیرتخصص', 'select', true, [
            $opt('2d_animator', 'انیماتور دو بعدی'),
            $opt('3d_animator', 'انیماتور سه بعدی'),
            $opt('character_animator', 'انیماتور کاراکتر'),
            $opt('motion_designer', 'موشن دیزاینر'),
        ]);
        $addField(18, 'software', 'نرم‌افزارهای تسلط', 'multiselect', true, [
            $opt('blender', 'Blender'), $opt('maya', 'Maya'),
            $opt('cinema4d', 'Cinema 4D'), $opt('after_effects', 'After Effects'),
            $opt('toon_boom', 'Toon Boom Harmony'),
            $opt('animate', 'Adobe Animate'),
            $opt('moho', 'Moho (Anime Studio)'),
        ]);
        $addField(18, 'animation_styles', 'سبک‌های انیمیشن', 'multiselect', false, [
            $opt('traditional', 'سنتی'), $opt('cut_out', 'Cut-out'),
            $opt('3d_cgi', '3D CGI'), $opt('stop_motion', 'Stop Motion'),
            $opt('motion_graphics', 'موشن گرافیک'),
        ]);

        // ─── 19. بازی‌های ویدیویی ─────────────────────────────────────────
        $addField(19, 'sub_specialty', 'زیرتخصص', 'select', true, [
            $opt('game_artist', 'هنرمند بازی'),
            $opt('game_animator', 'انیماتور بازی'),
            $opt('voice_actor_game', 'صداپیشه بازی'),
            $opt('narrative_designer', 'طراح روایت'),
        ]);
        $addField(19, 'engines', 'موتورهای بازی', 'multiselect', false, [
            $opt('unreal', 'Unreal Engine'),
            $opt('unity', 'Unity'),
            $opt('godot', 'Godot'),
            $opt('cryengine', 'CryEngine'),
        ]);
        $addField(19, 'software', 'نرم‌افزارهای هنری', 'multiselect', false, [
            $opt('maya', 'Maya'), $opt('blender', 'Blender'),
            $opt('substance', 'Substance Painter'),
            $opt('zbrush', 'ZBrush'), $opt('photoshop', 'Photoshop'),
        ]);

        // ─── 20. عوامل صحنه و پشت‌صحنه ──────────────────────────────────
        $addField(20, 'sub_specialty', 'زیرتخصص', 'select', true, [
            $opt('set_manager', 'مدیر صحنه'),
            $opt('equipment_manager', 'مسئول تجهیزات'),
            $opt('driver', 'راننده تولید'),
            $opt('transport', 'مسئول حمل‌ونقل'),
            $opt('logistics', 'مسئول تدارکات'),
            $opt('location_scout', 'مسئول لوکیشن'),
        ]);
        $addField(20, 'driving_licenses', 'گواهینامه‌های رانندگی', 'multiselect', false, [
            $opt('personal', 'پایه سه / شخصی'),
            $opt('heavy', 'پایه یک / سنگین'),
            $opt('motorcycle', 'موتورسیکلت'),
            $opt('international', 'بین‌المللی'),
        ]);
        $addField(20, 'equipment_types', 'تجهیزات تخصصی', 'multiselect', false, [
            $opt('generator', 'ژنراتور'), $opt('crane', 'جرثقیل/جیب آرم'),
            $opt('dolly', 'دالی'), $opt('technocrane', 'تکنوکرین'),
        ]);

        // ─── 21. آموزش و مربیگری ─────────────────────────────────────────
        $addField(21, 'sub_specialty', 'زیرتخصص', 'select', true, [
            $opt('acting_coach', 'مربی بازیگری'),
            $opt('accent_coach', 'مربی لهجه'),
            $opt('voice_coach', 'مربی صدا'),
            $opt('movement_coach', 'مربی حرکت'),
        ]);
        $addField(21, 'teaching_methods', 'روش‌های آموزشی', 'multiselect', false, [
            $opt('in_person', 'حضوری'), $opt('online', 'آنلاین'),
            $opt('workshop', 'کارگاهی'), $opt('private', 'خصوصی'),
        ]);
        $addField(21, 'age_groups', 'گروه‌های سنی', 'multiselect', false, [
            $opt('children', 'کودک (۶-۱۲)'), $opt('teen', 'نوجوان (۱۲-۱۸)'),
            $opt('adult', 'بزرگسال'), $opt('all', 'همه سنین'),
        ]);

        // ─── 22. ترجمه و زیرنویس ─────────────────────────────────────────
        $addField(22, 'sub_specialty', 'زیرتخصص', 'select', true, [
            $opt('screenplay_translator', 'مترجم فیلمنامه'),
            $opt('subtitler', 'زیرنویس‌گذار'),
            $opt('simultaneous_translator', 'مترجم همزمان صحنه'),
        ]);
        $addField(22, 'working_languages', 'جفت زبان‌های کاری', 'multiselect', true, [
            $opt('fa_en', 'فارسی ↔ انگلیسی'),
            $opt('fa_ar', 'فارسی ↔ عربی'),
            $opt('fa_fr', 'فارسی ↔ فرانسوی'),
            $opt('fa_de', 'فارسی ↔ آلمانی'),
            $opt('fa_tr', 'فارسی ↔ ترکی'),
            $opt('en_ar', 'انگلیسی ↔ عربی'),
        ]);
        $addField(22, 'subtitle_software', 'نرم‌افزار زیرنویس', 'multiselect', false, [
            $opt('subtitle_edit', 'Subtitle Edit'),
            $opt('aegisub', 'Aegisub'),
            $opt('fab_subtitler', 'FAB Subtitler'),
            $opt('eztitles', 'EZTitles'),
        ]);

        DB::table('specialty_attribute_definitions')->insert($rows);
    }
}
