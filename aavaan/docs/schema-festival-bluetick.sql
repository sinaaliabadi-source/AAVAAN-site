-- ═══════════════════════════════════════════════════════════════════════════
-- آوان — فاز «جشنوارهٔ افتتاح + تیک آبی هنرمندان»
-- DDL کامل MariaDB (نسخهٔ 11.8) این فاز.
--
-- این فایل مرجعِ خواندنی است؛ منبعِ اجراییِ رسمی، مهاجرت‌های Laravel هستند:
--   database/migrations/2026_07_05_000001_add_festival_to_subscription_plan.php
--   database/migrations/2026_07_05_000002_add_festival_system_settings.php
--   database/migrations/2026_07_05_000003_add_blue_tick_to_artist_profiles.php
--   database/migrations/2026_07_05_000004_make_production_access_id_nullable_on_logs.php
--
-- روشِ اجرای توصیه‌شده در تولید: php8.5 artisan migrate --force
-- (این SQL برای مرجع/بازبینی و اجرای دستیِ احتمالی است.)
-- ═══════════════════════════════════════════════════════════════════════════

-- ─────────────────────────────────────────────────────────────────────────
-- ۱) اشتراک جشنواره: آزادسازی ستون plan تا مقدار 'festival' مجاز شود.
--    ستون enum('monthly','yearly') به VARCHAR(20) تبدیل می‌شود (سازگار با مقادیر موجود).
-- ─────────────────────────────────────────────────────────────────────────
ALTER TABLE `subscriptions`
    MODIFY `plan` VARCHAR(20) NOT NULL;

-- ─────────────────────────────────────────────────────────────────────────
-- ۲) کلیدهای تنظیماتِ جشنواره در system_settings (درج idempotent).
--    festival_active  : بولی ('1' روشن، '0' خاموش)
--    festival_ends_at : تاریخ پایان جشنواره (۳۱ شهریور ۱۴۰۵ = 2026-09-22)
-- ─────────────────────────────────────────────────────────────────────────
INSERT INTO `system_settings` (`key`, `value`, `label_fa`, `group`, `updated_at`)
SELECT * FROM (SELECT
    'festival_active' AS `key`,
    '1' AS `value`,
    'جشنوارهٔ افتتاح فعال باشد (رایگان تا پایان تابستان)' AS `label_fa`,
    'general' AS `group`,
    NOW() AS `updated_at`
) AS t
WHERE NOT EXISTS (SELECT 1 FROM `system_settings` WHERE `key` = 'festival_active');

INSERT INTO `system_settings` (`key`, `value`, `label_fa`, `group`, `updated_at`)
SELECT * FROM (SELECT
    'festival_ends_at' AS `key`,
    '2026-09-22' AS `value`,
    'تاریخ پایان جشنواره (میلادی، مثل 2026-09-22)' AS `label_fa`,
    'general' AS `group`,
    NOW() AS `updated_at`
) AS t
WHERE NOT EXISTS (SELECT 1 FROM `system_settings` WHERE `key` = 'festival_ends_at');

-- ─────────────────────────────────────────────────────────────────────────
-- ۳) تیک آبی آوان روی artist_profiles.
--    has_blue_tick        : نشان برگزیدگیِ کلِ پروفایل (فقط ادمین اعطا می‌کند).
--    blue_tick_granted_at  : زمان اعطای نشان.
--    ایندکسِ has_blue_tick برای واکشیِ سریعِ «هنرمندان برگزیده».
-- ─────────────────────────────────────────────────────────────────────────
ALTER TABLE `artist_profiles`
    ADD COLUMN `has_blue_tick` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_active`,
    ADD COLUMN `blue_tick_granted_at` TIMESTAMP NULL DEFAULT NULL AFTER `has_blue_tick`,
    ADD INDEX `artist_profiles_has_blue_tick_idx` (`has_blue_tick`);

-- ─────────────────────────────────────────────────────────────────────────
-- ۴) در جشنواره تیم تولید تأییدشده بدون بستهٔ اعتبار پروفایل را باز می‌کند؛
--    پس production_access_id در لاگ دسترسی می‌تواند NULL باشد.
--    قید کلید خارجی حفظ می‌شود (NULL توسط FK بررسی نمی‌شود).
-- ─────────────────────────────────────────────────────────────────────────
ALTER TABLE `production_access_logs`
    MODIFY `production_access_id` BIGINT UNSIGNED NULL;

-- ═══════════════════════════════════════════════════════════════════════════
-- پایان DDL این فاز.
-- ═══════════════════════════════════════════════════════════════════════════
