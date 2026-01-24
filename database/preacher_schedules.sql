-- =========================================================
-- Tabel Jadwal Pengkhotbah Bulanan
-- =========================================================

CREATE TABLE IF NOT EXISTS `preacher_schedules` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `schedule_date` DATE NOT NULL COMMENT 'Tanggal ibadah',
    `service_name` VARCHAR(100) NOT NULL COMMENT 'Nama ibadah (Ibadah Raya, Sekolah Minggu, dll)',
    `service_time` TIME NOT NULL COMMENT 'Waktu ibadah',
    `preacher_name` VARCHAR(100) NULL COMMENT 'Nama pengkhotbah (NULL jika belum diketahui)',
    `sermon_title` VARCHAR(255) NULL COMMENT 'Judul khotbah (opsional)',
    `notes` TEXT NULL COMMENT 'Catatan tambahan',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_schedule_date` (`schedule_date`),
    INDEX `idx_month_year` (`schedule_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Contoh data untuk bulan ini
INSERT INTO `preacher_schedules` (`schedule_date`, `service_name`, `service_time`, `preacher_name`, `sermon_title`) VALUES
('2026-01-04', 'Ibadah Raya', '09:00:00', 'Pdt. Yohanes Surya', 'Memulai Tahun Baru dengan Iman'),
('2026-01-04', 'Ibadah Raya', '17:00:00', 'Pdt. Maria Santoso', 'Berkat Awal Tahun'),
('2026-01-11', 'Ibadah Raya', '09:00:00', 'Pdt. Andreas Wijaya', 'Hidup dalam Kasih'),
('2026-01-11', 'Ibadah Raya', '17:00:00', 'Pdt. Yohanes Surya', 'Pengharapan yang Teguh'),
('2026-01-18', 'Ibadah Raya', '09:00:00', NULL, NULL),
('2026-01-18', 'Ibadah Raya', '17:00:00', NULL, NULL),
('2026-01-25', 'Ibadah Raya', '09:00:00', 'Pdt. Paulus Simatupang', 'Iman yang Bekerja'),
('2026-01-25', 'Ibadah Raya', '17:00:00', 'Pdt. Maria Santoso', 'Kasih Kristus');
