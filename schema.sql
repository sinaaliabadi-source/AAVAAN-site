-- =============================================================================
-- Aavaan Platform — Full Database Schema
-- Generated from Laravel migrations (MySQL 5.7+ / MariaDB 10.3+)
-- Import via phpMyAdmin: Select database → Import tab → choose this file
-- =============================================================================

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET time_zone = '+03:30';
SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------------------------------
-- 1. users
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id`                bigint unsigned NOT NULL AUTO_INCREMENT,
  `name`              varchar(255) NOT NULL,
  `email`             varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password`          varchar(255) NOT NULL,
  `phone`             varchar(255) DEFAULT NULL,
  `role`              enum('artist','production','admin') NOT NULL DEFAULT 'artist',
  `remember_token`    varchar(100) DEFAULT NULL,
  `created_at`        timestamp NULL DEFAULT NULL,
  `updated_at`        timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 2. password_reset_tokens
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email`      varchar(255) NOT NULL,
  `token`      varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 3. sessions
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `sessions` (
  `id`            varchar(255) NOT NULL,
  `user_id`       bigint unsigned DEFAULT NULL,
  `ip_address`    varchar(45) DEFAULT NULL,
  `user_agent`    text,
  `payload`       longtext NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 4. cache & cache_locks
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cache` (
  `key`        varchar(255) NOT NULL,
  `value`      mediumtext NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key`        varchar(255) NOT NULL,
  `owner`      varchar(255) NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 5. jobs, job_batches, failed_jobs
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `jobs` (
  `id`           bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue`        varchar(255) NOT NULL,
  `payload`      longtext NOT NULL,
  `attempts`     smallint unsigned NOT NULL,
  `reserved_at`  int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at`   int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `job_batches` (
  `id`              varchar(255) NOT NULL,
  `name`            varchar(255) NOT NULL,
  `total_jobs`      int NOT NULL,
  `pending_jobs`    int NOT NULL,
  `failed_jobs`     int NOT NULL,
  `failed_job_ids`  longtext NOT NULL,
  `options`         mediumtext DEFAULT NULL,
  `cancelled_at`    int DEFAULT NULL,
  `created_at`      int NOT NULL,
  `finished_at`     int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id`         bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid`       varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue`      text NOT NULL,
  `payload`    longtext NOT NULL,
  `exception`  longtext NOT NULL,
  `failed_at`  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`(191), `queue`(191), `failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 6. artist_profiles
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `artist_profiles` (
  `id`               bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id`          bigint unsigned NOT NULL,
  `username`         varchar(255) DEFAULT NULL,
  `field`            varchar(255) NOT NULL,
  `city`             varchar(255) DEFAULT NULL,
  `birth_year`       smallint unsigned DEFAULT NULL,
  `years_experience` tinyint unsigned NOT NULL DEFAULT 0,
  `bio`              text,
  `avatar`           varchar(255) DEFAULT NULL,
  `reel_video`       varchar(255) DEFAULT NULL,
  `reel_is_external` tinyint(1) NOT NULL DEFAULT 0,
  `phone_contact`    varchar(255) DEFAULT NULL,
  `email_contact`    varchar(255) DEFAULT NULL,
  `is_active`        tinyint(1) NOT NULL DEFAULT 0,
  `profile_views`    bigint unsigned NOT NULL DEFAULT 0,
  `created_at`       timestamp NULL DEFAULT NULL,
  `updated_at`       timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `artist_profiles_username_unique` (`username`),
  KEY `artist_profiles_user_id_foreign` (`user_id`),
  CONSTRAINT `artist_profiles_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 7. work_histories
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `work_histories` (
  `id`                bigint unsigned NOT NULL AUTO_INCREMENT,
  `artist_profile_id` bigint unsigned NOT NULL,
  `title`             varchar(255) NOT NULL,
  `role`              varchar(255) NOT NULL,
  `year`              smallint unsigned DEFAULT NULL,
  `director`          varchar(255) DEFAULT NULL,
  `description`       text,
  `order`             tinyint unsigned NOT NULL DEFAULT 0,
  `created_at`        timestamp NULL DEFAULT NULL,
  `updated_at`        timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `work_histories_artist_profile_id_foreign` (`artist_profile_id`),
  CONSTRAINT `work_histories_artist_profile_id_foreign`
    FOREIGN KEY (`artist_profile_id`) REFERENCES `artist_profiles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 8. portfolio_images
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `portfolio_images` (
  `id`                bigint unsigned NOT NULL AUTO_INCREMENT,
  `artist_profile_id` bigint unsigned NOT NULL,
  `file_path`         varchar(255) NOT NULL,
  `caption`           varchar(255) DEFAULT NULL,
  `order`             tinyint unsigned NOT NULL DEFAULT 0,
  `created_at`        timestamp NULL DEFAULT NULL,
  `updated_at`        timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `portfolio_images_artist_profile_id_foreign` (`artist_profile_id`),
  CONSTRAINT `portfolio_images_artist_profile_id_foreign`
    FOREIGN KEY (`artist_profile_id`) REFERENCES `artist_profiles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 9. portfolio_videos
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `portfolio_videos` (
  `id`                bigint unsigned NOT NULL AUTO_INCREMENT,
  `artist_profile_id` bigint unsigned NOT NULL,
  `file_path`         varchar(255) NOT NULL,
  `duration`          smallint unsigned DEFAULT NULL COMMENT 'duration in seconds',
  `caption`           varchar(255) DEFAULT NULL,
  `is_reel`           tinyint(1) NOT NULL DEFAULT 0,
  `order`             tinyint unsigned NOT NULL DEFAULT 0,
  `created_at`        timestamp NULL DEFAULT NULL,
  `updated_at`        timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `portfolio_videos_artist_profile_id_foreign` (`artist_profile_id`),
  CONSTRAINT `portfolio_videos_artist_profile_id_foreign`
    FOREIGN KEY (`artist_profile_id`) REFERENCES `artist_profiles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 10. subscriptions
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `subscriptions` (
  `id`         bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id`    bigint unsigned NOT NULL,
  `plan`       enum('monthly','yearly') NOT NULL,
  `status`     enum('pending','active','expired') NOT NULL DEFAULT 'pending',
  `starts_at`  timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subscriptions_user_id_foreign` (`user_id`),
  CONSTRAINT `subscriptions_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 11. production_accesses
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `production_accesses` (
  `id`          bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id`     bigint unsigned NOT NULL,
  `access_type` enum('single','bundle_5','bundle_10') NOT NULL,
  `bundle_size` tinyint unsigned NOT NULL DEFAULT 1,
  `used_count`  tinyint unsigned NOT NULL DEFAULT 0,
  `expires_at`  timestamp NULL DEFAULT NULL,
  `created_at`  timestamp NULL DEFAULT NULL,
  `updated_at`  timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `production_accesses_user_id_foreign` (`user_id`),
  CONSTRAINT `production_accesses_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 12. payments  (polymorphic: payable_type + payable_id)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `payments` (
  `id`           bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id`      bigint unsigned NOT NULL,
  `payable_type` varchar(255) NOT NULL,
  `payable_id`   bigint unsigned NOT NULL,
  `amount`       int unsigned NOT NULL,
  `gateway`      varchar(255) NOT NULL DEFAULT 'zarinpal',
  `authority`    varchar(255) DEFAULT NULL COMMENT 'gateway authority code before payment',
  `ref_id`       varchar(255) DEFAULT NULL COMMENT 'gateway reference ID after success',
  `status`       enum('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  `paid_at`      timestamp NULL DEFAULT NULL,
  `created_at`   timestamp NULL DEFAULT NULL,
  `updated_at`   timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_user_id_foreign` (`user_id`),
  KEY `payments_payable_type_payable_id_index` (`payable_type`,`payable_id`),
  CONSTRAINT `payments_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 13. production_access_logs
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `production_access_logs` (
  `id`                   bigint unsigned NOT NULL AUTO_INCREMENT,
  `production_access_id` bigint unsigned NOT NULL,
  `production_user_id`   bigint unsigned NOT NULL,
  `artist_profile_id`    bigint unsigned NOT NULL,
  `accessed_at`          timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at`           timestamp NULL DEFAULT NULL,
  `updated_at`           timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pal_user_profile_unique` (`production_user_id`,`artist_profile_id`),
  KEY `production_access_logs_production_access_id_foreign` (`production_access_id`),
  KEY `production_access_logs_artist_profile_id_foreign` (`artist_profile_id`),
  CONSTRAINT `production_access_logs_production_access_id_foreign`
    FOREIGN KEY (`production_access_id`) REFERENCES `production_accesses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `production_access_logs_production_user_id_foreign`
    FOREIGN KEY (`production_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `production_access_logs_artist_profile_id_foreign`
    FOREIGN KEY (`artist_profile_id`) REFERENCES `artist_profiles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 14. blog_posts
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `blog_posts` (
  `id`           bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id`      bigint unsigned DEFAULT NULL,
  `title`        varchar(255) NOT NULL,
  `slug`         varchar(255) NOT NULL,
  `excerpt`      text,
  `body`         longtext NOT NULL,
  `cover_image`  varchar(255) DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at`   timestamp NULL DEFAULT NULL,
  `updated_at`   timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blog_posts_slug_unique` (`slug`),
  KEY `blog_posts_user_id_foreign` (`user_id`),
  CONSTRAINT `blog_posts_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 15. faqs
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `faqs` (
  `id`         bigint unsigned NOT NULL AUTO_INCREMENT,
  `question`   varchar(255) NOT NULL,
  `answer`     text NOT NULL,
  `category`   enum('artist','production','general') NOT NULL DEFAULT 'general',
  `order`      tinyint unsigned NOT NULL DEFAULT 0,
  `is_active`  tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 16. contact_messages
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id`         bigint unsigned NOT NULL AUTO_INCREMENT,
  `name`       varchar(255) NOT NULL,
  `email`      varchar(255) NOT NULL,
  `phone`      varchar(15) DEFAULT NULL,
  `subject`    varchar(255) NOT NULL,
  `message`    text NOT NULL,
  `is_read`    tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 17. migrations  (Laravel's internal tracking table)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `migrations` (
  `id`        int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch`     int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`migration`, `batch`) VALUES
  ('0001_01_01_000000_create_users_table', 1),
  ('0001_01_01_000001_create_cache_table', 1),
  ('0001_01_01_000002_create_jobs_table', 1),
  ('2026_06_17_110110_create_artist_profiles_table', 1),
  ('2026_06_17_110110_create_work_histories_table', 1),
  ('2026_06_17_110111_create_blog_posts_table', 1),
  ('2026_06_17_110111_create_contact_messages_table', 1),
  ('2026_06_17_110111_create_faqs_table', 1),
  ('2026_06_17_110111_create_production_accesses_table', 1),
  ('2026_06_17_110112_create_production_access_logs_table', 1),
  ('2026_06_18_000001_create_portfolio_images_table', 1),
  ('2026_06_18_000002_create_portfolio_videos_table', 1),
  ('2026_06_18_000003_create_subscriptions_table', 1),
  ('2026_06_18_000005_create_payments_table', 1);

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================================================
-- End of schema
-- =============================================================================
