-- =============================================================
-- AAVAAN Admin Panel Phase 1 — MariaDB 10.6 Compatible SQL
-- Run in phpMyAdmin or mysql client
-- Drop order: child tables first
-- =============================================================

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `discount_code_uses`;
DROP TABLE IF EXISTS `discount_codes`;
DROP TABLE IF EXISTS `system_settings`;

SET FOREIGN_KEY_CHECKS = 1;

-- -----------------------------------------------------------
-- 1. system_settings
-- -----------------------------------------------------------
CREATE TABLE `system_settings` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `key`        VARCHAR(100)    NOT NULL,
  `value`      TEXT            NULL,
  `label_fa`   VARCHAR(200)    NOT NULL,
  `group`      VARCHAR(50)     NOT NULL DEFAULT 'general',
  `updated_at` TIMESTAMP       NULL,
  `updated_by` BIGINT UNSIGNED NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ss_key_unique` (`key`),
  CONSTRAINT `ss_updated_by_fk`
    FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `system_settings` (`key`, `value`, `label_fa`, `group`) VALUES
  ('subscription_monthly_price', '150000',  'قیمت اشتراک ماهانه (تومان)', 'pricing'),
  ('subscription_yearly_price',  '1500000', 'قیمت اشتراک سالانه (تومان)', 'pricing'),
  ('production_access_price',    '200000',  'قیمت دسترسی تیم تولید',       'pricing'),
  ('max_photos_per_artist',      '30',      'حداکثر عکس هر هنرمند',        'limits'),
  ('site_maintenance_mode',      '0',       'حالت تعمیر و نگهداری',        'general');

-- -----------------------------------------------------------
-- 2. discount_codes
-- -----------------------------------------------------------
CREATE TABLE `discount_codes` (
  `id`          BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `code`        VARCHAR(50)      NOT NULL,
  `type`        ENUM('percent','fixed') NOT NULL,
  `value`       DECIMAL(10,2)    NOT NULL,
  `max_uses`    INT UNSIGNED     NULL,
  `used_count`  INT UNSIGNED     NOT NULL DEFAULT 0,
  `valid_from`  TIMESTAMP        NULL,
  `valid_until` TIMESTAMP        NULL,
  `is_active`   TINYINT(1)       NOT NULL DEFAULT 1,
  `created_by`  BIGINT UNSIGNED  NOT NULL,
  `created_at`  TIMESTAMP        NULL,
  `updated_at`  TIMESTAMP        NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dc_code_unique` (`code`),
  CONSTRAINT `dc_created_by_fk`
    FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- 3. discount_code_uses
-- -----------------------------------------------------------
CREATE TABLE `discount_code_uses` (
  `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `discount_code_id` BIGINT UNSIGNED NOT NULL,
  `user_id`          BIGINT UNSIGNED NOT NULL,
  `subscription_id`  BIGINT UNSIGNED NULL,
  `used_at`          TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `dcu_dc_fk`
    FOREIGN KEY (`discount_code_id`) REFERENCES `discount_codes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `dcu_user_fk`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `dcu_sub_fk`
    FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- 4. ALTER users — add is_banned, admin_notes, deleted_at
--    (skip each if column already exists)
-- -----------------------------------------------------------
ALTER TABLE `users`
  ADD COLUMN IF NOT EXISTS `is_banned`    TINYINT(1) NOT NULL DEFAULT 0 AFTER `admin_role`,
  ADD COLUMN IF NOT EXISTS `admin_notes`  TEXT       NULL          AFTER `is_banned`,
  ADD COLUMN IF NOT EXISTS `deleted_at`   TIMESTAMP  NULL          AFTER `admin_notes`;

-- =============================================================
-- END
-- =============================================================
