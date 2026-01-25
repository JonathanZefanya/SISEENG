-- =====================================================
-- Donation Accounts Table
-- Tabel untuk menyimpan rekening bank donasi (dinamis)
-- =====================================================

-- Create table donation_accounts
CREATE TABLE IF NOT EXISTS `donation_accounts` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `bank_name` VARCHAR(100) NOT NULL COMMENT 'Nama Bank (BCA, Mandiri, dll)',
    `account_number` VARCHAR(50) NOT NULL COMMENT 'Nomor Rekening',
    `account_name` VARCHAR(150) NOT NULL COMMENT 'Nama Pemilik Rekening',
    `bank_logo` VARCHAR(255) DEFAULT NULL COMMENT 'Logo bank (opsional)',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Status aktif',
    `sort_order` INT NOT NULL DEFAULT 0 COMMENT 'Urutan tampil',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_is_active` (`is_active`),
    KEY `idx_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample data
INSERT INTO `donation_accounts` (`bank_name`, `account_number`, `account_name`, `is_active`, `sort_order`) VALUES
('Bank BCA', '1234567890', 'GBI Ciseeng', 1, 1),
('Bank Mandiri', '0987654321', 'GBI Ciseeng', 1, 2);

-- Add QRIS setting
INSERT IGNORE INTO `settings` (`key`, `value`, `type`, `group`, `description`) VALUES
('donation_qris_image', '', 'image', 'donation', 'Gambar QRIS untuk donasi'),
('donation_qris_name', '', 'text', 'donation', 'Nama QRIS (opsional)');
