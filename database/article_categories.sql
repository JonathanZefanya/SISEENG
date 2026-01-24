-- =========================================================
-- Tabel Kategori Artikel
-- =========================================================

CREATE TABLE IF NOT EXISTS `article_categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `description` TEXT NULL,
    `color` VARCHAR(20) DEFAULT '#6c757d' COMMENT 'Warna badge untuk tampilan',
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_slug` (`slug`),
    INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tambahkan kolom category_id ke tabel articles
ALTER TABLE `articles` 
ADD COLUMN `category_id` INT UNSIGNED NULL AFTER `author_id`,
ADD INDEX `idx_category` (`category_id`),
ADD CONSTRAINT `fk_article_category` 
    FOREIGN KEY (`category_id`) REFERENCES `article_categories`(`id`) 
    ON DELETE SET NULL ON UPDATE CASCADE;

-- Insert sample categories
INSERT INTO `article_categories` (`name`, `slug`, `description`, `color`) VALUES
('Renungan', 'renungan', 'Artikel renungan harian dan mingguan', '#0d6efd'),
('Berita Gereja', 'berita-gereja', 'Berita dan informasi seputar gereja', '#198754'),
('Kesaksian', 'kesaksian', 'Kesaksian jemaat', '#ffc107'),
('Pengajaran', 'pengajaran', 'Artikel pengajaran dan doktrin', '#6f42c1'),
('Keluarga', 'keluarga', 'Artikel seputar keluarga Kristen', '#fd7e14'),
('Pemuda', 'pemuda', 'Artikel untuk pemuda', '#20c997');
