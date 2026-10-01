-- =====================================================================
-- Migration: Data Jemaat berbasis Keluarga (Kartu Keluarga)
-- =====================================================================
-- Jalankan SEKALI pada database yang sudah berjalan (import lewat
-- phpMyAdmin atau: mysql -u root -p db_siseeng < migrate_family_members.sql).
--
-- Yang dilakukan:
--   1. Backup tabel members ke members_backup_20261001
--   2. Menambah kolom status pernikahan, pasangan, tanggal & keterangan
--      status pada members (TIDAK ada kolom lama yang dihapus)
--   3. Membuat tabel families, family_members, member_status_logs
--   4. Setiap jemaat lama dibuatkan 1 KK dengan dia sebagai kepala
--      (penggabungan keluarga dilakukan manual lewat menu Pindahkan KK)
--   5. Menampilkan query verifikasi di akhir: semua angka "sebelum" dan
--      "sesudah" harus sama, dan kolom "masalah" harus 0
-- =====================================================================

-- 1. Backup
CREATE TABLE `members_backup_20261001` AS SELECT * FROM `members`;

-- 2. Kolom baru di members
ALTER TABLE `members`
    MODIFY COLUMN `status` ENUM('active','inactive','deceased','moved_church','moved_religion')
        NOT NULL DEFAULT 'active',
    ADD COLUMN `status_date`    DATE NULL DEFAULT NULL AFTER `status`,
    ADD COLUMN `status_note`    TEXT NULL DEFAULT NULL AFTER `status_date`,
    ADD COLUMN `marital_status` ENUM('single','married','widowed') NULL DEFAULT NULL AFTER `gender`,
    ADD COLUMN `spouse_id`      INT UNSIGNED NULL DEFAULT NULL AFTER `marital_status`,
    ADD KEY `idx_marital_status` (`marital_status`),
    ADD CONSTRAINT `fk_members_spouse`
        FOREIGN KEY (`spouse_id`) REFERENCES `members` (`id`) ON DELETE SET NULL;

-- 3. Tabel baru
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

-- Keanggotaan KK sekaligus riwayatnya: pindah KK = tutup baris lama + buat baris baru.
-- Dua UNIQUE pada generated column menjaga aturan di level database:
--   * 1 orang hanya punya 1 keanggotaan terbuka (aktif di 1 KK)
--   * 1 KK hanya punya 1 kepala keluarga aktif
-- FK sengaja tanpa CASCADE: MySQL tidak mengizinkan CASCADE pada kolom dasar generated column.
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

-- 4. Setiap jemaat lama = 1 KK (id KK = id jemaat agar mudah ditelusuri)
START TRANSACTION;

INSERT INTO `families` (`id`, `family_code`, `address`, `phone`, `created_by`, `created_at`)
SELECT `id`, CONCAT('KK-', LPAD(`id`, 5, '0')), `address`, `phone`, `created_by`, `created_at`
FROM `members`;

-- Jemaat yang sudah tidak aktif: keanggotaannya langsung ditutup (KK-nya jadi Arsip)
INSERT INTO `family_members` (`family_id`, `member_id`, `relationship`, `joined_at`, `left_at`, `left_reason`)
SELECT `id`, `id`, 'head',
       COALESCE(`membership_date`, DATE(`created_at`)),
       IF(`status` = 'active', NULL, COALESCE(DATE(`updated_at`), DATE(`created_at`))),
       IF(`status` = 'active', NULL, 'status_change')
FROM `members`;

INSERT INTO `member_status_logs` (`member_id`, `family_id`, `field`, `old_value`, `new_value`, `changed_at`, `reason`)
SELECT `id`, `id`, 'family', NULL, CONCAT('KK-', LPAD(`id`, 5, '0')), CURDATE(), 'Migrasi data lama'
FROM `members`;

COMMIT;

-- 5. Verifikasi (sebelum = sesudah, masalah = 0)
SELECT
    (SELECT COUNT(*) FROM `members_backup_20261001`)                          AS total_sebelum,
    (SELECT COUNT(*) FROM `members`)                                          AS total_sesudah,
    (SELECT COUNT(*) FROM `members_backup_20261001` WHERE `status` = 'active') AS aktif_sebelum,
    (SELECT COUNT(*) FROM `members` WHERE `status` = 'active')                AS aktif_sesudah,
    (SELECT COUNT(*) FROM `members` m
      WHERE m.`status` = 'active'
        AND (SELECT COUNT(*) FROM `family_members` fm
              WHERE fm.`member_id` = m.`id` AND fm.`left_at` IS NULL) <> 1)   AS masalah_aktif_tanpa_1_kk,
    (SELECT COUNT(*) FROM `members` m
       LEFT JOIN `family_members` fm ON fm.`member_id` = m.`id`
      WHERE fm.`id` IS NULL)                                                  AS masalah_tanpa_kk;
