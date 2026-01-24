-- =====================================================
-- Additional Settings for GBI Ciseeng
-- Run this SQL to add new settings
-- =====================================================

-- Insert additional settings jika belum ada
INSERT IGNORE INTO `settings` (`key`, `value`, `type`, `group`, `description`) VALUES
-- General
('site_name', 'Gereja Bethel Indonesia Ciseeng', 'text', 'general', 'Nama website'),
('site_tagline', 'Melayani dengan Kasih', 'text', 'general', 'Tagline website'),
('site_description', 'Website resmi Gereja Bethel Indonesia Ciseeng', 'textarea', 'general', 'Deskripsi website untuk SEO'),
('site_email', 'info@gbiciseeng.com', 'email', 'contact', 'Email utama'),
('site_phone', '(021) 1234-5678', 'text', 'contact', 'Nomor telepon'),
('site_whatsapp', '08123456789', 'text', 'contact', 'Nomor WhatsApp'),
('site_address', 'Jl. Raya Ciseeng No. 123, Bogor, Jawa Barat', 'textarea', 'contact', 'Alamat gereja'),

-- Hero Section
('hero_title', 'Selamat Datang di', 'text', 'hero', 'Judul utama hero section'),
('hero_subtitle', 'Gereja Bethel Indonesia Ciseeng', 'text', 'hero', 'Sub judul hero section'),
('hero_verse', 'Karena di mana dua atau tiga orang berkumpul dalam nama-Ku, di situ Aku ada di tengah-tengah mereka.', 'textarea', 'hero', 'Ayat Alkitab'),
('hero_verse_ref', 'Matius 18:20', 'text', 'hero', 'Referensi ayat'),

-- Social Media
('site_facebook', 'https://facebook.com/gbiciseeng', 'url', 'social', 'Link Facebook'),
('site_instagram', 'https://instagram.com/gbiciseeng', 'url', 'social', 'Link Instagram'),
('site_youtube', 'https://youtube.com/gbiciseeng', 'url', 'social', 'Link YouTube'),
('site_tiktok', '', 'url', 'social', 'Link TikTok'),

-- Donation
('donation_title', 'Dukung Pelayanan Kami', 'text', 'donation', 'Judul halaman donasi'),
('donation_description', 'Persembahan dan donasi Anda sangat berarti untuk mendukung pelayanan gereja dan membantu sesama.', 'textarea', 'donation', 'Deskripsi donasi'),
('donation_bank_name', 'Bank BCA', 'text', 'donation', 'Nama Bank 1'),
('donation_bank_account', '1234567890', 'text', 'donation', 'Nomor Rekening 1'),
('donation_account_name', 'GBI Ciseeng', 'text', 'donation', 'Atas Nama 1'),
('donation_bank_name_2', '', 'text', 'donation', 'Nama Bank 2'),
('donation_bank_account_2', '', 'text', 'donation', 'Nomor Rekening 2'),
('donation_account_name_2', '', 'text', 'donation', 'Atas Nama 2'),

-- About
('about_vision', 'Menjadi gereja yang membawa transformasi bagi masyarakat melalui kasih Kristus.', 'textarea', 'about', 'Visi gereja'),
('about_mission', 'Menyebarkan Injil kepada semua orang\nMembina jemaat dalam iman dan kasih\nMelayani sesama dengan tulus\nMembangun komunitas yang saling mendukung', 'textarea', 'about', 'Misi gereja'),
('about_history', 'Gereja Bethel Indonesia Ciseeng didirikan pada tahun 2000 dengan visi untuk menjangkau masyarakat di wilayah Ciseeng dan sekitarnya.', 'textarea', 'about', 'Sejarah gereja'),
('about_pastor', 'Pdt. Nama Gembala', 'text', 'about', 'Nama gembala/pendeta');

-- Update group untuk settings yang sudah ada
UPDATE `settings` SET `group` = 'contact' WHERE `key` IN ('site_email', 'site_phone', 'site_address');
UPDATE `settings` SET `group` = 'social' WHERE `key` IN ('site_facebook', 'site_instagram', 'site_youtube');
