-- =====================================================
-- gbi_ciseeng - Sistem Informasi Manajemen Gereja
-- Database Schema
-- =====================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET FOREIGN_KEY_CHECKS = 0;
START TRANSACTION;
SET time_zone = "+07:00";

-- =====================================================
-- Create Database
-- =====================================================
CREATE DATABASE IF NOT EXISTS `gbi_ciseeng` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `gbi_ciseeng`;

-- =====================================================
-- Table: users (Admin & Super Admin)
-- =====================================================
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('super_admin', 'admin') NOT NULL DEFAULT 'admin',
    `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    `last_login` DATETIME DEFAULT NULL,
    `login_attempts` TINYINT UNSIGNED NOT NULL DEFAULT 0,
    `locked_until` DATETIME DEFAULT NULL,
    `remember_token` VARCHAR(100) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`),
    KEY `idx_role` (`role`),
    KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default Super Admin (password: admin123)
INSERT INTO `users` (`name`, `email`, `password`, `role`, `status`) VALUES
('Super Admin', 'admin@gbiciseeng.com', '$2y$12$/1zBQIyWnQGWNN11gmyRJ.qCHsG/g8I6OLxV33RT0u19X4Kqw8I8i', 'super_admin', 'active');

-- =====================================================
-- Table: schedules (Jadwal Ibadah)
-- =====================================================
DROP TABLE IF EXISTS `schedules`;
CREATE TABLE `schedules` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(200) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `day_of_week` ENUM('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu') NOT NULL,
    `start_time` TIME NOT NULL,
    `end_time` TIME DEFAULT NULL,
    `location` VARCHAR(200) DEFAULT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_by` INT UNSIGNED DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_day_of_week` (`day_of_week`),
    KEY `idx_is_active` (`is_active`),
    KEY `idx_sort_order` (`sort_order`),
    KEY `fk_schedules_user` (`created_by`),
    CONSTRAINT `fk_schedules_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample schedules
INSERT INTO `schedules` (`title`, `description`, `day_of_week`, `start_time`, `end_time`, `location`, `is_active`, `sort_order`) VALUES
('Ibadah Minggu Pagi', 'Ibadah umum untuk seluruh jemaat', 'Minggu', '07:00:00', '09:00:00', 'Gedung Utama', 1, 1),
('Ibadah Minggu Siang', 'Ibadah umum untuk seluruh jemaat', 'Minggu', '10:00:00', '12:00:00', 'Gedung Utama', 1, 2),
('Ibadah Sekolah Minggu', 'Ibadah untuk anak-anak', 'Minggu', '08:00:00', '10:00:00', 'Ruang Sekolah Minggu', 1, 3),
('Ibadah Pemuda', 'Ibadah untuk pemuda dan remaja', 'Sabtu', '17:00:00', '19:00:00', 'Aula Pemuda', 1, 4),
('Persekutuan Doa', 'Persekutuan doa bersama', 'Rabu', '18:00:00', '20:00:00', 'Ruang Doa', 1, 5);

-- =====================================================
-- Table: articles (Artikel & Renungan)
-- =====================================================
DROP TABLE IF EXISTS `articles`;
CREATE TABLE `articles` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `content` TEXT NOT NULL,
    `excerpt` TEXT DEFAULT NULL,
    `image` VARCHAR(255) DEFAULT NULL,
    `status` ENUM('draft', 'published') NOT NULL DEFAULT 'draft',
    `views` INT UNSIGNED NOT NULL DEFAULT 0,
    `author_id` INT UNSIGNED DEFAULT NULL,
    `published_at` DATETIME DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug` (`slug`),
    KEY `idx_status` (`status`),
    KEY `idx_published_at` (`published_at`),
    KEY `fk_articles_author` (`author_id`),
    CONSTRAINT `fk_articles_author` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample article
INSERT INTO `articles` (`title`, `slug`, `content`, `status`, `author_id`, `published_at`) VALUES
('Selamat Datang di Website Gereja Kami', 'selamat-datang-di-website-gereja-kami', 
'Puji syukur kepada Tuhan atas peluncuran website resmi gereja kami. Website ini hadir sebagai sarana informasi dan komunikasi bagi seluruh jemaat dan masyarakat umum.\n\nMelalui website ini, Anda dapat:\n- Mengetahui jadwal ibadah\n- Membaca artikel dan renungan harian\n- Mengikuti informasi kegiatan gereja\n- Menghubungi kami untuk berbagai keperluan\n\nKami berdoa agar website ini menjadi berkat bagi banyak orang. Tuhan memberkati.',
'published', 1, NOW());

-- =====================================================
-- Table: events (Kegiatan/Acara)
-- =====================================================
DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `image` VARCHAR(255) DEFAULT NULL,
    `event_date` DATE NOT NULL,
    `event_time` TIME NOT NULL,
    `end_date` DATE DEFAULT NULL,
    `end_time` TIME DEFAULT NULL,
    `location` VARCHAR(255) DEFAULT NULL,
    `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
    `status` ENUM('upcoming', 'ongoing', 'completed', 'cancelled') NOT NULL DEFAULT 'upcoming',
    `created_by` INT UNSIGNED DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_event_date` (`event_date`),
    KEY `idx_status` (`status`),
    KEY `idx_is_featured` (`is_featured`),
    KEY `fk_events_user` (`created_by`),
    CONSTRAINT `fk_events_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample events
INSERT INTO `events` (`title`, `description`, `event_date`, `event_time`, `location`, `is_featured`) VALUES
('Natal Bersama 2024', 'Perayaan Natal bersama seluruh jemaat. Acara akan dimeriahkan dengan penampilan paduan suara dan drama musikal.', DATE_ADD(CURDATE(), INTERVAL 30 DAY), '17:00:00', 'Gedung Utama Gereja', 1),
('Retreat Pemuda', 'Retreat tahunan untuk pemuda dan remaja dengan tema "Generasi Pembawa Terang"', DATE_ADD(CURDATE(), INTERVAL 14 DAY), '08:00:00', 'Villa Grace, Puncak', 0);

-- =====================================================
-- Table: members (Data Jemaat)
-- =====================================================
DROP TABLE IF EXISTS `members`;
CREATE TABLE `members` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `full_name` VARCHAR(100) NOT NULL,
    `gender` ENUM('M', 'F') NOT NULL,
    `birth_date` DATE DEFAULT NULL,
    `birth_place` VARCHAR(100) DEFAULT NULL,
    `phone` VARCHAR(20) DEFAULT NULL,
    `email` VARCHAR(100) DEFAULT NULL,
    `address` TEXT DEFAULT NULL,
    `baptism_date` DATE DEFAULT NULL,
    `membership_date` DATE DEFAULT NULL,
    `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    `notes` TEXT DEFAULT NULL,
    `created_by` INT UNSIGNED DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_full_name` (`full_name`),
    KEY `idx_gender` (`gender`),
    KEY `idx_status` (`status`),
    KEY `fk_members_user` (`created_by`),
    CONSTRAINT `fk_members_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample members
INSERT INTO `members` (`full_name`, `gender`, `birth_date`, `birth_place`, `phone`, `email`, `status`) VALUES
('Budi Santoso', 'M', '1985-03-15', 'Jakarta', '081234567890', 'budi@email.com', 'active'),
('Siti Rahayu', 'F', '1990-07-22', 'Bandung', '082345678901', 'siti@email.com', 'active'),
('Agus Wijaya', 'M', '1978-11-08', 'Surabaya', '083456789012', 'agus@email.com', 'active');

-- =====================================================
-- Table: contact_messages (Pesan Kontak)
-- =====================================================
DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE `contact_messages` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `phone` VARCHAR(20) DEFAULT NULL,
    `subject` VARCHAR(200) DEFAULT NULL,
    `message` TEXT NOT NULL,
    `is_read` TINYINT(1) NOT NULL DEFAULT 0,
    `read_at` DATETIME DEFAULT NULL,
    `read_by` INT UNSIGNED DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` VARCHAR(255) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_is_read` (`is_read`),
    KEY `idx_created_at` (`created_at`),
    KEY `fk_messages_read_by` (`read_by`),
    CONSTRAINT `fk_messages_read_by` FOREIGN KEY (`read_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: activity_logs (Log Aktivitas)
-- =====================================================
DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED DEFAULT NULL,
    `user_name` VARCHAR(100) DEFAULT NULL,
    `action` VARCHAR(100) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `module` VARCHAR(50) DEFAULT NULL,
    `record_id` INT UNSIGNED DEFAULT NULL,
    `old_data` JSON DEFAULT NULL,
    `new_data` JSON DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` VARCHAR(255) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_action` (`action`),
    KEY `idx_module` (`module`),
    KEY `idx_created_at` (`created_at`),
    CONSTRAINT `fk_activity_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: settings (Pengaturan Website)
-- =====================================================
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `key` VARCHAR(100) NOT NULL,
    `value` TEXT DEFAULT NULL,
    `type` VARCHAR(50) NOT NULL DEFAULT 'text',
    `group` VARCHAR(50) NOT NULL DEFAULT 'general',
    `description` VARCHAR(255) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `key` (`key`),
    KEY `idx_group` (`group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default settings
INSERT INTO `settings` (`key`, `value`, `type`, `group`, `description`) VALUES
('site_name', 'gbi_ciseeng - Sistem Informasi Gereja', 'text', 'general', 'Nama website'),
('site_tagline', 'Melayani dengan Kasih', 'text', 'general', 'Tagline website'),
('site_email', 'info@gereja.com', 'email', 'general', 'Email utama'),
('site_phone', '(021) 1234-5678', 'text', 'general', 'Nomor telepon'),
('site_address', 'Jl. Gereja No. 123, Jakarta', 'textarea', 'general', 'Alamat gereja'),
('site_facebook', 'https://facebook.com/gereja', 'url', 'social', 'Link Facebook'),
('site_instagram', 'https://instagram.com/gereja', 'url', 'social', 'Link Instagram'),
('site_youtube', 'https://youtube.com/gereja', 'url', 'social', 'Link YouTube');

-- =====================================================
-- Table: rate_limits (Rate Limiting untuk keamanan)
-- =====================================================
DROP TABLE IF EXISTS `rate_limits`;
CREATE TABLE `rate_limits` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `key` VARCHAR(255) NOT NULL,
    `attempts` INT UNSIGNED NOT NULL DEFAULT 1,
    `last_attempt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `expires_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `key` (`key`),
    KEY `idx_expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Create event to clean up expired rate limits
-- =====================================================
DROP EVENT IF EXISTS `cleanup_rate_limits`;
CREATE EVENT `cleanup_rate_limits`
ON SCHEDULE EVERY 1 HOUR
DO DELETE FROM `rate_limits` WHERE `expires_at` < NOW();

-- Enable event scheduler (run as root if needed)
-- SET GLOBAL event_scheduler = ON;

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

-- =====================================================
-- Notes:
-- 1. Default Super Admin credentials:
--    Email: admin@gbi_ciseeng.com
--    Password: admin123
--
-- 2. Make sure to change the password after first login!
--
-- 3. Enable event scheduler for automatic cleanup:
--    SET GLOBAL event_scheduler = ON;
-- =====================================================
