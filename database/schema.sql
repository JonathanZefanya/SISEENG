-- =====================================================================
-- GBI CISEENG - Database Schema (STRUKTUR SAJA, TANPA DATA)
-- =====================================================================
-- Struktur tabel identik dengan database/deploy.sql, tetapi tanpa data
-- awal apa pun (tidak ada akun admin, jadwal, kategori, pengaturan).
--
--   deploy.sql  -> instalasi baru: struktur + data awal + akun admin
--   schema.sql  -> hanya membuat tabel kosong (mis. untuk migrasi data
--                  dari backup, atau database testing)
--
-- ⚠ Setelah import file ini belum ada akun untuk login. Masukkan data
--   dari backup, atau pakai deploy.sql untuk instalasi baru.
-- ⚠ Jika struktur berubah, ubah deploy.sql DAN schema.sql bersamaan.
-- =====================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET FOREIGN_KEY_CHECKS = 0;
START TRANSACTION;
SET time_zone = "+07:00";

-- =====================================================================
-- TABEL: users
-- =====================================================================
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id`             INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    `name`           VARCHAR(100)   NOT NULL,
    `email`          VARCHAR(100)   NOT NULL,
    `password`       VARCHAR(255)   NOT NULL,
    `role`           ENUM('super_admin','admin') NOT NULL DEFAULT 'admin',
    `status`         ENUM('active','inactive')   NOT NULL DEFAULT 'active',
    `last_login`     DATETIME       DEFAULT NULL,
    `login_attempts` TINYINT UNSIGNED NOT NULL DEFAULT 0,
    `locked_until`   DATETIME       DEFAULT NULL,
    `remember_token` VARCHAR(100)   DEFAULT NULL,
    `created_at`     DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     DATETIME       DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`),
    KEY `idx_role`   (`role`),
    KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- TABEL: schedules (Jadwal Ibadah)
-- =====================================================================
DROP TABLE IF EXISTS `schedules`;
CREATE TABLE `schedules` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title`       VARCHAR(200) NOT NULL,
    `description` TEXT         DEFAULT NULL,
    `day_of_week` ENUM('Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu') NOT NULL,
    `start_time`  TIME         NOT NULL,
    `end_time`    TIME         DEFAULT NULL,
    `location`    VARCHAR(200) DEFAULT NULL,
    `is_active`   TINYINT(1)   NOT NULL DEFAULT 1,
    `sort_order`  INT          NOT NULL DEFAULT 0,
    `created_by`  INT UNSIGNED DEFAULT NULL,
    `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME     DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_day_of_week` (`day_of_week`),
    KEY `idx_is_active`   (`is_active`),
    KEY `idx_sort_order`  (`sort_order`),
    KEY `fk_schedules_user` (`created_by`),
    CONSTRAINT `fk_schedules_user`
        FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- TABEL: articles
-- =====================================================================
DROP TABLE IF EXISTS `articles`;
CREATE TABLE `articles` (
    `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title`        VARCHAR(255) NOT NULL,
    `slug`         VARCHAR(255) NOT NULL,
    `content`      TEXT         NOT NULL,
    `excerpt`      TEXT         DEFAULT NULL,
    `image`        VARCHAR(255) DEFAULT NULL,
    `status`       ENUM('draft','published') NOT NULL DEFAULT 'draft',
    `views`        INT UNSIGNED NOT NULL DEFAULT 0,
    `author_id`    INT UNSIGNED DEFAULT NULL,
    `category_id`  INT UNSIGNED DEFAULT NULL,
    `published_at` DATETIME     DEFAULT NULL,
    `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   DATETIME     DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug` (`slug`),
    KEY `idx_status`       (`status`),
    KEY `idx_published_at` (`published_at`),
    KEY `idx_category`     (`category_id`),
    KEY `fk_articles_author` (`author_id`),
    CONSTRAINT `fk_articles_author`
        FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- TABEL: article_categories
-- =====================================================================
DROP TABLE IF EXISTS `article_categories`;
CREATE TABLE `article_categories` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`        VARCHAR(100) NOT NULL,
    `slug`        VARCHAR(100) NOT NULL UNIQUE,
    `description` TEXT         NULL,
    `color`       VARCHAR(20)  DEFAULT '#6c757d',
    `is_active`   TINYINT(1)   DEFAULT 1,
    `created_at`  DATETIME     DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME     ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_slug`   (`slug`),
    INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `articles`
    ADD CONSTRAINT `fk_article_category`
        FOREIGN KEY (`category_id`) REFERENCES `article_categories`(`id`)
        ON DELETE SET NULL ON UPDATE CASCADE;

-- =====================================================================
-- TABEL: events 
-- =====================================================================
DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title`       VARCHAR(255) NOT NULL,
    `slug`        VARCHAR(255) NOT NULL,
    `description` TEXT         NOT NULL,
    `image`       VARCHAR(255) DEFAULT NULL,
    `event_date`  DATE         NOT NULL,
    `event_time`  TIME         NOT NULL,
    `end_date`    DATE         DEFAULT NULL,
    `end_time`    TIME         DEFAULT NULL,
    `location`    VARCHAR(255) DEFAULT NULL,
    `is_featured` TINYINT(1)   NOT NULL DEFAULT 0,
    `status`      ENUM('draft','published') NOT NULL DEFAULT 'draft',
    `created_by`  INT UNSIGNED DEFAULT NULL,
    `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME     DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug` (`slug`),
    KEY `idx_event_date`  (`event_date`),
    KEY `idx_status`      (`status`),
    KEY `idx_is_featured` (`is_featured`),
    KEY `fk_events_user`  (`created_by`),
    CONSTRAINT `fk_events_user`
        FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- TABEL: preacher_schedules (Jadwal Pengkhotbah)
-- =====================================================================
DROP TABLE IF EXISTS `preacher_schedules`;
CREATE TABLE `preacher_schedules` (
    `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `schedule_date` DATE         NOT NULL,
    `service_name`  VARCHAR(100) NOT NULL,
    `service_time`  TIME         NOT NULL,
    `preacher_name` VARCHAR(100) NULL,
    `sermon_title`  VARCHAR(255) NULL,
    `notes`         TEXT         NULL,
    `created_at`    DATETIME     DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME     ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_schedule_date` (`schedule_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- TABEL: members (Data Jemaat - per orang)
-- =====================================================================
DROP TABLE IF EXISTS `members`;
CREATE TABLE `members` (
    `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `full_name`        VARCHAR(100) NOT NULL,
    `gender`           ENUM('M','F') NOT NULL,
    `marital_status`   ENUM('single','married','widowed') DEFAULT NULL COMMENT 'widowed = Janda (F) / Duda (M); NULL = belum diisi',
    `spouse_id`        INT UNSIGNED DEFAULT NULL,
    `birth_date`       DATE         DEFAULT NULL,
    `birth_place`      VARCHAR(100) DEFAULT NULL,
    `phone`            VARCHAR(20)  DEFAULT NULL,
    `email`            VARCHAR(100) DEFAULT NULL,
    `address`          TEXT         DEFAULT NULL,
    `baptism_date`     DATE         DEFAULT NULL,
    `membership_date`  DATE         DEFAULT NULL,
    `status`           ENUM('active','inactive','deceased','moved_church','moved_religion') NOT NULL DEFAULT 'active',
    `status_date`      DATE         DEFAULT NULL,
    `status_note`      TEXT         DEFAULT NULL,
    `notes`            TEXT         DEFAULT NULL,
    `created_by`       INT UNSIGNED DEFAULT NULL,
    `created_at`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       DATETIME     DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_full_name` (`full_name`),
    KEY `idx_gender`    (`gender`),
    KEY `idx_status`    (`status`),
    KEY `idx_marital_status` (`marital_status`),
    KEY `fk_members_user` (`created_by`),
    KEY `fk_members_spouse` (`spouse_id`),
    CONSTRAINT `fk_members_user`
        FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_members_spouse`
        FOREIGN KEY (`spouse_id`) REFERENCES `members` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- TABEL: families (Kartu Keluarga)
-- =====================================================================
DROP TABLE IF EXISTS `families`;
CREATE TABLE `families` (
    `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `family_code`      VARCHAR(20)  NOT NULL,
    `address`          TEXT         DEFAULT NULL,
    `phone`            VARCHAR(20)  DEFAULT NULL,
    `origin_family_id` INT UNSIGNED DEFAULT NULL COMMENT 'KK asal (anak menikah / cerai lalu buat KK baru)',
    `notes`            TEXT         DEFAULT NULL,
    `created_by`       INT UNSIGNED DEFAULT NULL,
    `created_at`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       DATETIME     DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_family_code` (`family_code`),
    KEY `fk_families_origin` (`origin_family_id`),
    KEY `fk_families_user` (`created_by`),
    CONSTRAINT `fk_families_origin`
        FOREIGN KEY (`origin_family_id`) REFERENCES `families` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_families_user`
        FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- TABEL: family_members (Keanggotaan KK + riwayatnya)
-- UNIQUE pada generated column: 1 orang aktif di 1 KK, 1 kepala per KK
-- =====================================================================
DROP TABLE IF EXISTS `family_members`;
CREATE TABLE `family_members` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `family_id`     INT UNSIGNED NOT NULL,
    `member_id`     INT UNSIGNED NOT NULL,
    `relationship`  ENUM('head','spouse','child','child_in_law','grandchild','parent','parent_in_law','sibling','other') NOT NULL,
    `joined_at`     DATE         DEFAULT NULL,
    `left_at`       DATE         DEFAULT NULL,
    `left_reason`   ENUM('married_out','moved_family','divorce','status_change','data_fix') DEFAULT NULL,
    `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `current_member_key` INT UNSIGNED
        GENERATED ALWAYS AS (IF(`left_at` IS NULL, `member_id`, NULL)) STORED,
    `current_head_key`   INT UNSIGNED
        GENERATED ALWAYS AS (IF(`left_at` IS NULL AND `relationship` = 'head', `family_id`, NULL)) STORED,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_current_member` (`current_member_key`),
    UNIQUE KEY `uq_current_head` (`current_head_key`),
    KEY `idx_family` (`family_id`, `left_at`),
    KEY `idx_member` (`member_id`),
    CONSTRAINT `fk_fm_family` FOREIGN KEY (`family_id`) REFERENCES `families` (`id`),
    CONSTRAINT `fk_fm_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- TABEL: member_status_logs (Riwayat status, pernikahan, hubungan, KK)
-- =====================================================================
DROP TABLE IF EXISTS `member_status_logs`;
CREATE TABLE `member_status_logs` (
    `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `member_id`         INT UNSIGNED NOT NULL,
    `family_id`         INT UNSIGNED DEFAULT NULL COMMENT 'KK tempat perubahan terjadi',
    `field`             ENUM('status','marital_status','relationship','family') NOT NULL,
    `old_value`         VARCHAR(50)  DEFAULT NULL,
    `new_value`         VARCHAR(50)  DEFAULT NULL,
    `changed_at`        DATE         NOT NULL,
    `reason`            VARCHAR(100) DEFAULT NULL,
    `note`              TEXT         DEFAULT NULL,
    `related_member_id` INT UNSIGNED DEFAULT NULL COMMENT 'Mis. pasangan yang meninggal / mantan pasangan',
    `created_by`        INT UNSIGNED DEFAULT NULL,
    `created_at`        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_member` (`member_id`),
    KEY `idx_family` (`family_id`),
    CONSTRAINT `fk_msl_member`  FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_msl_family`  FOREIGN KEY (`family_id`) REFERENCES `families` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_msl_related` FOREIGN KEY (`related_member_id`) REFERENCES `members` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_msl_user`    FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- TABEL: contact_messages (Pesan Kontak)
-- =====================================================================
DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE `contact_messages` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(100) NOT NULL,
    `email`      VARCHAR(100) NOT NULL,
    `phone`      VARCHAR(20)  DEFAULT NULL,
    `subject`    VARCHAR(200) DEFAULT NULL,
    `message`    TEXT         NOT NULL,
    `is_read`    TINYINT(1)   NOT NULL DEFAULT 0,
    `read_at`    DATETIME     DEFAULT NULL,
    `read_by`    INT UNSIGNED DEFAULT NULL,
    `ip_address` VARCHAR(45)  DEFAULT NULL,
    `user_agent` VARCHAR(255) DEFAULT NULL,
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_is_read`   (`is_read`),
    KEY `idx_created_at`(`created_at`),
    KEY `fk_messages_read_by` (`read_by`),
    CONSTRAINT `fk_messages_read_by`
        FOREIGN KEY (`read_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- TABEL: activity_logs (Log Aktivitas)
-- =====================================================================
DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`     INT UNSIGNED DEFAULT NULL,
    `user_name`   VARCHAR(100) DEFAULT NULL,
    `action`      VARCHAR(100) NOT NULL,
    `description` TEXT         DEFAULT NULL,
    `module`      VARCHAR(50)  DEFAULT NULL,
    `record_id`   INT UNSIGNED DEFAULT NULL,
    `old_data`    JSON         DEFAULT NULL,
    `new_data`    JSON         DEFAULT NULL,
    `ip_address`  VARCHAR(45)  DEFAULT NULL,
    `user_agent`  VARCHAR(255) DEFAULT NULL,
    `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user_id`   (`user_id`),
    KEY `idx_action`    (`action`),
    KEY `idx_module`    (`module`),
    KEY `idx_created_at`(`created_at`),
    CONSTRAINT `fk_activity_logs_user`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- TABEL: rate_limits
-- Catatan: Tanpa EVENT SCHEDULER (tidak didukung shared hosting).
--          Baris kadaluarsa dibersihkan otomatis oleh aplikasi PHP.
-- =====================================================================
DROP TABLE IF EXISTS `rate_limits`;
CREATE TABLE `rate_limits` (
    `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `key`          VARCHAR(255) NOT NULL,
    `attempts`     INT UNSIGNED NOT NULL DEFAULT 1,
    `last_attempt` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `expires_at`   DATETIME     NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `key` (`key`),
    KEY `idx_expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- TABEL: donation_accounts (Rekening Donasi)
-- =====================================================================
DROP TABLE IF EXISTS `donation_accounts`;
CREATE TABLE `donation_accounts` (
    `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `bank_name`      VARCHAR(100) NOT NULL,
    `account_number` VARCHAR(50)  NOT NULL,
    `account_name`   VARCHAR(150) NOT NULL,
    `bank_logo`      VARCHAR(255) DEFAULT NULL,
    `is_active`      TINYINT(1)   NOT NULL DEFAULT 1,
    `sort_order`     INT          NOT NULL DEFAULT 0,
    `created_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     DATETIME     DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_is_active`  (`is_active`),
    KEY `idx_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- TABEL: settings (Pengaturan Website)
-- =====================================================================
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `key`         VARCHAR(100) NOT NULL,
    `value`       TEXT         DEFAULT NULL,
    `type`        VARCHAR(50)  NOT NULL DEFAULT 'text',
    `group`       VARCHAR(50)  NOT NULL DEFAULT 'general',
    `description` VARCHAR(255) DEFAULT NULL,
    `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME     DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `key` (`key`),
    KEY `idx_group` (`group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

-- =====================================================================
-- SELESAI - semua tabel dibuat kosong
-- =====================================================================
