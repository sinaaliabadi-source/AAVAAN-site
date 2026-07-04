-- =====================================================================
-- AAVAAN — «کست‌یاب»: تغییرات دیتابیس + نمونه‌کوئری‌های فیلتر ترکیبی (MariaDB 11.8)
-- ---------------------------------------------------------------------
-- این فایل برای «ممیزی مستقل» روی دیتابیس واقعی است.
-- منبع حقیقتِ اجرا، migrationهای Laravel هستند (database/migrations).
--
-- پیش‌نیاز: ستون‌ها و جدول ویژگی‌ها از پرامپت قبل ساخته شده‌اند
-- (docs/schema-specialty-attributes.sql). این فایل فقط ایندکس تکمیلی کست‌یاب
-- و نمونه‌کوئری‌های فیلتر را دارد.
-- =====================================================================

-- ─── تغییر دیتابیس این پرامپت: ایندکس ترکیبی برای فیلتر بازهٔ عددیِ همبسته ──
-- کوئری فیلتر ویژگی به‌صورت whereExists همبسته است:
--   v.artist_specialty_id = sp.id AND v.definition_id = ? AND v.value_number BETWEEN ? AND ?
-- ایندکس یکتای موجود (asav_unique) با value_string است؛ این ایندکس مسیر عددی را کامل می‌کند.
ALTER TABLE `artist_specialty_attribute_values`
    ADD INDEX `asav_sp_def_number_idx` (`artist_specialty_id`, `definition_id`, `value_number`);

-- توجه: فیلترهای select/boolean/multiselect از ایندکس یکتای موجود
--   asav_unique (artist_specialty_id, definition_id, value_string) استفاده می‌کنند.
-- برای صحت طرح اجرا:  EXPLAIN <کوئری>  را روی دیتابیس واقعی اجرا کنید و
-- ستون key = asav_sp_def_number_idx / asav_unique را ببینید.

-- =====================================================================
-- ۵ نمونه‌کوئری فیلتر ترکیبی (SELECT) برای تست مستقل روی MariaDB
-- ---------------------------------------------------------------------
-- در همهٔ نمونه‌ها، تعریف ویژگی با «key + دستهٔ والد» یافت می‌شود (نه id هاردکد)،
-- دقیقاً مانند منطق کنترلر. «بازیگری و اجرا» = specialty_categories.slug = 'acting'.
-- زیرشاخه‌ها تعریف‌های والد را ارث می‌برند، پس تعریف روی رکورد والد (parent) است.
-- =====================================================================

-- ── نمونه ۱ ─ بازیگر زن، تهران، سن ۲۵–۳۵، قد ۱۶۰–۱۷۵، لهجهٔ آذری ─────────
-- (فیلتر پایه روی profile + دستهٔ acting و زیرشاخه‌هایش + قد عددی + لهجهٔ multiselect)
SET @acting := (SELECT id FROM specialty_categories WHERE slug = 'acting');
SET @def_height := (SELECT id FROM specialty_attribute_definitions
                    WHERE category_id = @acting AND `key` = 'height_cm');
SET @def_accents := (SELECT id FROM specialty_attribute_definitions
                     WHERE category_id = @acting AND `key` = 'accents');
-- سال جاری شمسی را جایگزین 1405 کنید (سن = سال_شمسی - birth_year):
SELECT p.id, p.city, p.birth_year, p.gender
FROM artist_profiles p
WHERE p.is_active = 1
  AND p.gender = 'female'
  AND p.city LIKE '%تهران%'
  AND p.birth_year BETWEEN (1405 - 35) AND (1405 - 25)
  AND EXISTS (
      SELECT 1 FROM artist_specialties sp
      WHERE sp.user_id = p.user_id
        AND sp.category_id IN (
            @acting, SELECT_CHILDREN /* @acting + زیرشاخه‌ها */
        )
        AND EXISTS (
            SELECT 1 FROM artist_specialty_attribute_values v
            WHERE v.artist_specialty_id = sp.id
              AND v.definition_id = @def_height
              AND v.value_number BETWEEN 160 AND 175
        )
        AND EXISTS (
            SELECT 1 FROM artist_specialty_attribute_values v
            WHERE v.artist_specialty_id = sp.id
              AND v.definition_id = @def_accents
              AND v.value_string IN ('azeri')
        )
  );
-- نکته: به‌جای SELECT_CHILDREN در کد از این استفاده می‌شود:
--   (SELECT id FROM specialty_categories WHERE id = @acting OR parent_id = @acting)
-- که در IN(...) به‌صورت لیست idها پاس داده می‌شود.

-- ── نمونه ۲ ─ همان دسته، فقط بازهٔ قد ۱۸۰ به بالا (کران بالا آزاد) ─────────
SELECT p.id, p.city
FROM artist_profiles p
WHERE p.is_active = 1
  AND EXISTS (
      SELECT 1 FROM artist_specialties sp
      WHERE sp.user_id = p.user_id
        AND sp.category_id IN (SELECT id FROM specialty_categories WHERE id = @acting OR parent_id = @acting)
        AND EXISTS (
            SELECT 1 FROM artist_specialty_attribute_values v
            WHERE v.artist_specialty_id = sp.id
              AND v.definition_id = @def_height
              AND v.value_number >= 180
        )
  );

-- ── نمونه ۳ ─ چند گزینهٔ یک ویژگی (OR داخل ویژگی): لهجهٔ آذری یا کردی ─────
SELECT p.id
FROM artist_profiles p
WHERE p.is_active = 1
  AND EXISTS (
      SELECT 1 FROM artist_specialties sp
      WHERE sp.user_id = p.user_id
        AND sp.category_id IN (SELECT id FROM specialty_categories WHERE id = @acting OR parent_id = @acting)
        AND EXISTS (
            SELECT 1 FROM artist_specialty_attribute_values v
            WHERE v.artist_specialty_id = sp.id
              AND v.definition_id = @def_accents
              AND v.value_string IN ('azeri', 'kurdish')   -- OR میان گزینه‌ها
        )
  );

-- ── نمونه ۴ ─ فیلتر select (رنگ چشم = سبز) + boolean (توان اجرای زنده در موسیقی) ──
-- select و boolean هر دو در value_string ذخیره می‌شوند ('green' و '1').
SET @music := (SELECT id FROM specialty_categories WHERE slug = 'music');
SET @def_live := (SELECT id FROM specialty_attribute_definitions
                  WHERE category_id = @music AND `key` = 'live_performance');
SELECT p.id
FROM artist_profiles p
WHERE p.is_active = 1
  AND EXISTS (
      SELECT 1 FROM artist_specialties sp
      WHERE sp.user_id = p.user_id
        AND sp.category_id IN (SELECT id FROM specialty_categories WHERE id = @music OR parent_id = @music)
        AND EXISTS (
            SELECT 1 FROM artist_specialty_attribute_values v
            WHERE v.artist_specialty_id = sp.id
              AND v.definition_id = @def_live
              AND v.value_string = '1'      -- boolean = بله
        )
  );

-- ── نمونه ۵ ─ فیلتر بدون دسته: فقط مشخصات پایه (زن، سابقهٔ ۵+ سال، شهر شیراز) ──
SELECT p.id, p.city, p.years_experience
FROM artist_profiles p
WHERE p.is_active = 1
  AND p.gender = 'female'
  AND p.years_experience >= 5
  AND p.city LIKE '%شیراز%'
ORDER BY p.id DESC
LIMIT 20;

-- =====================================================================
-- EXPLAIN پیشنهادی برای تأیید استفاده از ایندکس‌ها (روی دیتابیس واقعی):
--   EXPLAIN SELECT ... (نمونه ۱) ...;
-- انتظار: زیرکوئری value_number → asav_sp_def_number_idx
--         زیرکوئری value_string → asav_unique یا asav_def_string_idx
-- =====================================================================
