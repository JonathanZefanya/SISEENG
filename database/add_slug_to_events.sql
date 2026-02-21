-- =====================================================
-- Migration: Tambah kolom slug & perbaiki ENUM status
-- pada tabel events
-- =====================================================

USE `gbi_ciseeng`;

-- 1. Tambah kolom slug
ALTER TABLE `events`
    ADD COLUMN `slug` VARCHAR(255) NULL AFTER `title`,
    ADD UNIQUE KEY `slug` (`slug`);

-- 2. Sesuaikan ENUM status agar cocok dengan kode Controller
--    (Controller pakai 'draft' dan 'published')
ALTER TABLE `events`
    MODIFY COLUMN `status` ENUM('draft', 'published') NOT NULL DEFAULT 'draft';

-- 3. Isi slug untuk data yang sudah ada (generate dari title)
UPDATE `events`
SET `slug` = LOWER(
    REPLACE(
        REPLACE(
            REPLACE(
                REPLACE(
                    REPLACE(`title`, ' ', '-'),
                ' ', '-'),
            '/', '-'),
        '.', ''),
    ',', '')
)
WHERE `slug` IS NULL OR `slug` = '';

-- 4. Ubah slug menjadi NOT NULL setelah diisi
ALTER TABLE `events`
    MODIFY COLUMN `slug` VARCHAR(255) NOT NULL;
