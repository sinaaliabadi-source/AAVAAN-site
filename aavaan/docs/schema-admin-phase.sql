-- =====================================================================
-- AAVAAN — فاز ادمین (افزودن کاربر، تأیید تیم تولید، اشتراک/اعتبار دستی)
-- DDL کامل MariaDB 11.8 برای ممیزی مستقل روی دیتابیس واقعی.
-- منبع حقیقتِ اجرا، migrationهای Laravel است (database/migrations).
-- charset/collation مطابق پیش‌فرض پروژه: utf8mb4 / utf8mb4_unicode_ci.
-- =====================================================================

-- ─── ۱) گردش‌کار تأیید تیم تولید (روی users) ─────────────────────────
-- نکتهٔ حیاتی سازگاری با گذشته: پیش‌فرض approval_status روی «approved» است
-- تا کاربران فعلی سرور (هنرمندان و تیم‌های موجود) قفل نشوند.
ALTER TABLE `users`
    ADD COLUMN `approval_status` VARCHAR(20) NOT NULL DEFAULT 'approved' AFTER `is_banned`,
    ADD COLUMN `approved_at` TIMESTAMP NULL DEFAULT NULL AFTER `approval_status`,
    ADD COLUMN `approved_by` BIGINT UNSIGNED NULL DEFAULT NULL AFTER `approved_at`,
    ADD COLUMN `rejection_reason` TEXT NULL DEFAULT NULL AFTER `approved_by`;

ALTER TABLE `users`
    ADD CONSTRAINT `users_approved_by_foreign`
        FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- ایندکس ترکیبی برای فیلتر لیست تیم‌های تولید بر اساس وضعیت.
ALTER TABLE `users`
    ADD INDEX `users_role_approval_status_index` (`role`, `approval_status`);
-- مقادیر approval_status: 'pending' | 'approved' | 'rejected'
-- در ثبت‌نام فقط نقش production مقدار pending می‌گیرد؛ هنرمندان همیشه approved.

-- ─── ۲) تفکیک روش پرداخت (روی payments) ──────────────────────────────
-- درگاه (gateway) یا ثبت دستی توسط ادمین (manual). پیش‌فرض gateway تا رکوردهای موجود نشکنند.
ALTER TABLE `payments`
    ADD COLUMN `payment_source` ENUM('gateway','manual') NOT NULL DEFAULT 'gateway' AFTER `gateway`;

-- ─── ۳) یادداشت ادمین برای اشتراک دستی (روی subscriptions) ───────────
ALTER TABLE `subscriptions`
    ADD COLUMN `admin_note` TEXT NULL DEFAULT NULL AFTER `expires_at`;

-- ─── ۴) اعتبار دستی تیم تولید (روی production_accesses) ──────────────
-- افزودن یادداشت ادمین + آزادسازی access_type از قید enum تا مقدار «manual» مجاز شود.
ALTER TABLE `production_accesses`
    ADD COLUMN `admin_note` TEXT NULL DEFAULT NULL AFTER `expires_at`;

-- enum قبلی: ('single','bundle_5','bundle_10') → VARCHAR تا 'manual' هم پذیرفته شود.
-- (مقادیر موجود دست‌نخورده می‌مانند.)
ALTER TABLE `production_accesses`
    MODIFY `access_type` VARCHAR(20) NOT NULL DEFAULT 'single';
-- مقادیر access_type: 'single' | 'bundle_5' | 'bundle_10' | 'manual'

-- =====================================================================
-- نمونه‌کوئری‌های ممیزی
-- =====================================================================

-- تیم‌های تولید در انتظار تأیید:
--   SELECT id, name, email, created_at FROM users
--   WHERE role='production' AND approval_status='pending' ORDER BY created_at DESC;

-- پرداخت‌های دستی (اشتراک/اعتبار هدیه):
--   SELECT id, user_id, amount, payable_type, payment_source, created_at
--   FROM payments WHERE payment_source='manual' ORDER BY created_at DESC;

-- اعتبارهای دستی تیم‌های تولید:
--   SELECT pa.id, pa.user_id, pa.bundle_size, pa.used_count, pa.admin_note
--   FROM production_accesses pa WHERE pa.access_type='manual';
