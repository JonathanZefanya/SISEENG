-- =====================================================================
-- GBI CISEENG - Database Deploy Script
-- Versi untuk Shared Hosting (tanpa DEFINER, tanpa CREATE EVENT)
-- =====================================================================
-- Cara pemakaian:
--   1. Buat database baru di cPanel/phpMyAdmin
--   2. Import file ini
--   3. Login dengan: admin@gbiciseeng.com / admin123
--   4. Segera ganti password setelah login pertama!
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

-- Default Super Admin  (password: admin123)
INSERT INTO `users` (`name`, `email`, `password`, `role`, `status`) VALUES
('Super Admin', 'admin@gbiciseeng.com',
 '$2y$12$/1zBQIyWnQGWNN11gmyRJ.qCHsG/g8I6OLxV33RT0u19X4Kqw8I8i',
 'super_admin', 'active');

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

INSERT INTO `schedules` (`title`, `description`, `day_of_week`, `start_time`, `end_time`, `location`, `is_active`, `sort_order`) VALUES
('Ibadah Minggu Pagi',   'Ibadah umum untuk seluruh jemaat', 'Minggu', '07:00:00', '09:00:00', 'Gedung Utama',           1, 1),
('Ibadah Minggu Siang',  'Ibadah umum untuk seluruh jemaat', 'Minggu', '10:00:00', '12:00:00', 'Gedung Utama',           1, 2),
('Ibadah Sekolah Minggu','Ibadah untuk anak-anak',           'Minggu', '08:00:00', '10:00:00', 'Ruang Sekolah Minggu',  1, 3),
('Ibadah Pemuda',        'Ibadah untuk pemuda dan remaja',   'Sabtu',  '17:00:00', '19:00:00', 'Aula Pemuda',            1, 4),
('Persekutuan Doa',      'Persekutuan doa bersama',          'Rabu',   '18:00:00', '20:00:00', 'Ruang Doa',              1, 5);

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

INSERT INTO `articles` (`title`, `slug`, `content`, `status`, `author_id`, `published_at`) VALUES
('Selamat Datang di Website Gereja Kami', 'selamat-datang-di-website-gereja-kami',
 'Puji syukur kepada Tuhan atas peluncuran website resmi gereja kami.',
 'published', 1, NOW());

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

INSERT INTO `article_categories` (`name`, `slug`, `description`, `color`) VALUES
('Renungan',      'renungan',       'Artikel renungan harian dan mingguan', '#0d6efd'),
('Berita Gereja', 'berita-gereja',  'Berita dan informasi seputar gereja',  '#198754'),
('Kesaksian',     'kesaksian',      'Kesaksian jemaat',                     '#ffc107'),
('Pengajaran',    'pengajaran',     'Artikel pengajaran dan doktrin',       '#6f42c1'),
('Keluarga',      'keluarga',       'Artikel seputar keluarga Kristen',     '#fd7e14'),
('Pemuda',        'pemuda',         'Artikel untuk pemuda',                 '#20c997');

-- =====================================================================
-- TABEL: events (sudah include kolom slug & status draft/published)
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

INSERT INTO `donation_accounts` (`bank_name`, `account_number`, `account_name`, `is_active`, `sort_order`) VALUES
('Bank BCA',    '1234567890', 'GBI Ciseeng', 1, 1),
('Bank Mandiri','0987654321', 'GBI Ciseeng', 1, 2);

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

INSERT INTO `settings` (`key`, `value`, `type`, `group`, `description`) VALUES
-- General
('site_name',        'GBI Ciseeng',                         'text',     'general', 'Nama website'),
('site_tagline',     'Melayani dengan Kasih',                'text',     'general', 'Tagline website'),
('site_description', 'Website resmi Gereja Bethel Indonesia Ciseeng. Melayani dengan kasih Kristus.', 'textarea','general', 'Deskripsi website untuk SEO'),
-- Contact
('site_email',       'info@gbiciseeng.com',                  'email',    'contact', 'Email utama'),
('site_phone',       '(021) 1234-5678',                      'text',     'contact', 'Nomor telepon'),
('site_whatsapp',    '',                                      'text',     'contact', 'Nomor WhatsApp'),
('site_address',     'Jl. Raya Ciseeng No. 123, Bogor, Jawa Barat', 'textarea','contact', 'Alamat gereja'),
('site_gmaps_embed', '',                                      'url',      'contact', 'Google Maps Embed URL'),
('site_operational_hours','Senin - Jumat: 08:00 - 17:00\nSabtu: 08:00 - 12:00\nMinggu: Ibadah','textarea','contact','Jam operasional'),
-- Hero
('hero_title',       'Selamat Datang di',                    'text',     'hero',   'Judul hero'),
('hero_subtitle',    'Gereja Bethel Indonesia Ciseeng',       'text',     'hero',   'Sub judul hero'),
('hero_verse',       'Karena di mana dua atau tiga orang berkumpul dalam nama-Ku, di situ Aku ada di tengah-tengah mereka.','textarea','hero','Ayat Alkitab'),
('hero_verse_ref',   'Matius 18:20',                          'text',     'hero',   'Referensi ayat'),
-- Social Media
('site_facebook',    '',                                      'url',      'social', 'Link Facebook'),
('site_instagram',   '',                                      'url',      'social', 'Link Instagram'),
('site_youtube',     '',                                      'url',      'social', 'Link YouTube'),
('site_tiktok',      '',                                      'url',      'social', 'Link TikTok'),
-- Donation
('donation_title',       'Dukung Pelayanan Kami',             'text',     'donation','Judul halaman donasi'),
('donation_description', 'Persembahan dan donasi Anda sangat berarti untuk mendukung pelayanan gereja.','textarea','donation','Deskripsi donasi'),
('donation_qris_image',  '',                                  'image',    'donation','Gambar QRIS'),
('donation_qris_name',   '',                                  'text',     'donation','Nama QRIS'),
-- About
('about_image',   '',  'text', 'about', 'Foto halaman tentang kami'),
('about_vision',  'Menjadi gereja yang membawa transformasi bagi kehidupan pribadi, keluarga, dan masyarakat melalui kuasa Injil Kristus.','textarea','about','Visi gereja'),
('about_mission', 'Menyebarkan Injil kepada semua orang\nMembina jemaat dalam iman dan kasih\nMelayani sesama dengan tulus\nMembangun komunitas yang saling mendukung','textarea','about','Misi gereja'),
('about_history', 'Gereja Bethel Indonesia Ciseeng didirikan dengan visi untuk menjangkau masyarakat di wilayah Ciseeng dan sekitarnya, menjadi berkat bagi banyak orang sejak awal berdirinya.','textarea','about','Sejarah gereja'),
('about_pastor',  'Pdt. Nama Gembala',                        'text',     'about', 'Nama gembala/pendeta');

-- =====================================================================
SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

-- =====================================================================
-- SELESAI
-- =====================================================================
-- Akun default:
--   Email   : admin@gbiciseeng.com
--   Password: admin123
--   ⚠ Ganti password segera setelah login pertama!
--
-- Catatan shared hosting:
--   - CREATE EVENT dihapus (butuh SUPER privilege)
--   - Cleanup rate_limits ditangani otomatis oleh kode PHP
--   - Pastikan folder uploads/ bisa ditulis (chmod 755)
-- =====================================================================
