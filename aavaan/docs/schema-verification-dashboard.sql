-- =====================================================================
-- AAVAAN — تأیید تخصص + داشبورد ادمین (MariaDB 11.8)
-- DDL کامل این تسک + نمونه‌کوئری‌ها برای ممیزی مستقل.
-- منبع حقیقتِ اجرا، migrationهای Laravel است (database/migrations).
-- charset/collation پیش‌فرض پروژه: utf8mb4 / utf8mb4_unicode_ci.
-- =====================================================================

-- ─── اتصال جدول verifications به تخصص‌های هنرمند ─────────────────────
-- جریان «تأیید تخصص»: type = 'specialty'. مقادیر قبلی (phone/resume/professional) دست‌نخورده می‌مانند.
ALTER TABLE `verifications`
    ADD COLUMN `artist_specialty_id` BIGINT UNSIGNED NULL AFTER `user_id`,
    ADD COLUMN `artist_note` TEXT NULL AFTER `type`,          -- توضیح هنرمند
    ADD COLUMN `evidence_links` JSON NULL AFTER `artist_note`; -- لینک مدارک/نمونه‌کار

-- کلید خارجی با حذف آبشاری: با حذف تخصص، درخواست‌های تأییدش پاک می‌شوند.
ALTER TABLE `verifications`
    ADD CONSTRAINT `verifications_artist_specialty_id_foreign`
        FOREIGN KEY (`artist_specialty_id`) REFERENCES `artist_specialties` (`id`)
        ON DELETE CASCADE;

-- ایندکس ترکیبی برای فیلتر صف بررسی بر اساس وضعیت و تاریخ (نام < ۶۴ کاراکتر).
ALTER TABLE `verifications`
    ADD INDEX `verifications_status_created_at_index` (`status`, `created_at`);

-- =====================================================================
-- ۳ نمونه‌کوئری برای ممیزی
-- =====================================================================

-- ── نمونه ۱ ─ تخصص‌های «تأییدشده» یک کاربر (مثلاً user_id = 42) ─────────
SELECT sp.id AS specialty_id, sc.name_fa AS specialty, v.reviewed_at
FROM artist_specialties sp
JOIN specialty_categories sc ON sc.id = sp.category_id
JOIN verifications v
      ON v.artist_specialty_id = sp.id
     AND v.type = 'specialty'
     AND v.status = 'approved'
WHERE sp.user_id = 42;

-- ── نمونه ۲ ─ فیلتر «فقط تأییدشده‌ها» در کست‌یاب ───────────────────────
-- (هنرمندان فعالی که دستِ‌کم یک تخصص تأییدشده در دستهٔ acting و زیرشاخه‌هایش دارند)
SET @acting := (SELECT id FROM specialty_categories WHERE slug = 'acting');
SELECT p.id, p.city
FROM artist_profiles p
WHERE p.is_active = 1
  AND EXISTS (
      SELECT 1
      FROM verifications vr
      JOIN artist_specialties vsp ON vsp.id = vr.artist_specialty_id
      WHERE vsp.user_id = p.user_id
        AND vr.type = 'specialty'
        AND vr.status = 'approved'
        AND vsp.category_id IN (SELECT id FROM specialty_categories WHERE id = @acting OR parent_id = @acting)
  );

-- ── نمونه ۳ ─ صف اقدامات داشبورد: درخواست‌های تأیید تخصص در انتظار ─────
SELECT v.id, u.name AS artist, sc.name_fa AS specialty, v.created_at
FROM verifications v
JOIN users u ON u.id = v.user_id
LEFT JOIN artist_specialties sp ON sp.id = v.artist_specialty_id
LEFT JOIN specialty_categories sc ON sc.id = sp.category_id
WHERE v.type = 'specialty' AND v.status = 'pending'
ORDER BY v.created_at DESC
LIMIT 8;
