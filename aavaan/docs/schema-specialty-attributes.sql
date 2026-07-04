-- =====================================================================
-- AAVAAN — طرح دیتابیس ویژگی‌های تخصص هنرمندان (MariaDB 11.8)
-- ---------------------------------------------------------------------
-- این فایل صرفاً برای «ممیزی مستقل» روی دیتابیس واقعی است.
-- منبع حقیقتِ اجرا، migrationهای Laravel هستند (پوشهٔ database/migrations).
-- charset/collation مطابق پیش‌فرض پروژه: utf8mb4 / utf8mb4_unicode_ci.
--
-- شامل:
--   1) ALTER TABLE artist_profiles                 → افزودن ستون gender
--   2) ALTER TABLE specialty_attribute_definitions → افزودن unit و is_filterable
--   3) CREATE TABLE artist_specialty_attribute_values (ایندکس نرمال‌شدهٔ فیلتر)
-- =====================================================================

-- ─── ۱) جنسیت روی پروفایل پایه (ویژگی سراسری کستینگ) ──────────────────
ALTER TABLE `artist_profiles`
    ADD COLUMN `gender` ENUM('male','female') NULL DEFAULT NULL AFTER `birth_year`;

-- ─── ۲) قابلیت فیلتر و واحد روی تعریف ویژگی‌ها ────────────────────────
ALTER TABLE `specialty_attribute_definitions`
    ADD COLUMN `unit` VARCHAR(20) NULL DEFAULT NULL AFTER `field_type`;

ALTER TABLE `specialty_attribute_definitions`
    ADD COLUMN `is_filterable` TINYINT(1) NOT NULL DEFAULT 0 AFTER `visibility`;

-- ─── ۳) جدول ایندکس نرمال‌شدهٔ مقادیر (برای فیلتر SQL در فاز جستجوی تیم تولید) ──
-- منبع حقیقت همچنان ستون JSON «attributes» روی artist_specialties است؛
-- این جدول فقط ایندکس جستجو است و با App\Services\SpecialtyAttributeIndexer بازسازی می‌شود.
CREATE TABLE `artist_specialty_attribute_values` (
    `id`                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `artist_specialty_id` BIGINT UNSIGNED NOT NULL,
    `definition_id`       BIGINT UNSIGNED NOT NULL,
    `value_string`        VARCHAR(191)  NULL DEFAULT NULL,   -- select / boolean ('1'/'0') / text کوتاه / date (Y-m-d)
    `value_number`        DECIMAL(10,2) NULL DEFAULT NULL,   -- number
    `created_at`          TIMESTAMP NULL DEFAULT NULL,
    `updated_at`          TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),

    -- به‌ازای هر گزینهٔ multiselect یک ردیف جدا؛ به همین دلیل unique سه‌ستونه است.
    UNIQUE KEY `asav_unique` (`artist_specialty_id`, `definition_id`, `value_string`),
    KEY `asav_def_string_idx` (`definition_id`, `value_string`),
    KEY `asav_def_number_idx` (`definition_id`, `value_number`),

    CONSTRAINT `asav_specialty_fk`
        FOREIGN KEY (`artist_specialty_id`) REFERENCES `artist_specialties` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `asav_definition_fk`
        FOREIGN KEY (`definition_id`) REFERENCES `specialty_attribute_definitions` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- قواعد پرکردن ردیف‌ها (توسط SpecialtyAttributeIndexer::sync):
--   • multiselect  → به‌ازای هر گزینه یک ردیف، value_string = مقدار گزینه
--   • boolean      → value_string = '1' یا '0'
--   • number       → value_number = عدد (value_string خالی)
--   • select/text  → value_string = مقدار
--   • date         → value_string = تاریخ با فرمت Y-m-d
--   • textarea/url/file_link → ردیفی ساخته نمی‌شود (قابل فیلتر نیستند)
-- =====================================================================
