-- =====================================================
-- Jalankan SQL ini di phpMyAdmin jika belum ada settings about
-- =====================================================
USE `gbi_ciseeng`;

INSERT IGNORE INTO `settings` (`key`, `value`, `type`, `group`, `description`) VALUES
('about_image',
 '',
 'text', 'about', 'Foto halaman tentang kami'),

('about_vision',
 'Menjadi gereja yang membawa transformasi bagi kehidupan pribadi, keluarga, dan masyarakat melalui kuasa Injil Kristus.',
 'textarea', 'about', 'Visi gereja'),

('about_mission',
 'Menyebarkan Injil kepada semua orang\nMembina jemaat dalam iman dan kasih\nMelayani sesama dengan tulus\nMembangun komunitas yang saling mendukung',
 'textarea', 'about', 'Misi gereja (satu baris = satu poin misi)'),

('about_history',
 'Gereja Bethel Indonesia Ciseeng didirikan dengan visi untuk menjangkau masyarakat di wilayah Ciseeng dan sekitarnya, menjadi berkat bagi banyak orang sejak awal berdirinya.',
 'textarea', 'about', 'Sejarah gereja'),

('about_pastor',
 'Pdt. Nama Gembala',
 'text', 'about', 'Nama gembala/pendeta'),

('site_description',
 'Website resmi Gereja Bethel Indonesia Ciseeng. Melayani dengan kasih Kristus.',
 'textarea', 'general', 'Deskripsi singkat gereja'),

('site_whatsapp',
 '',
 'text', 'contact', 'Nomor WhatsApp');
