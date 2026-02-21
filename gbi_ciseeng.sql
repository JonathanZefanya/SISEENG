-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 21 Feb 2026 pada 15.27
-- Versi server: 8.2.0
-- Versi PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gbi_ciseeng`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  `user_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `module` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `record_id` int UNSIGNED DEFAULT NULL,
  `old_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `new_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ;

--
-- Dumping data untuk tabel `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `user_name`, `action`, `description`, `module`, `record_id`, `old_data`, `new_data`, `ip_address`, `user_agent`, `created_at`) VALUES
(69, 1, NULL, 'delete', 'Menghapus gambar QRIS donasi', 'settings', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-02-21 15:26:01'),
(70, 1, NULL, 'delete_schedule', 'Menghapus jadwal: Ibadah Raya 1', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-02-21 15:26:19'),
(71, 1, NULL, 'delete_schedule', 'Menghapus jadwal: Ibadah Raya 2', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-02-21 15:26:20'),
(72, 1, NULL, 'delete_schedule', 'Menghapus jadwal: Ibadah Raya 3', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-02-21 15:26:22'),
(73, 1, NULL, 'delete_schedule', 'Menghapus jadwal: DRP (Anak Muda)', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-02-21 15:26:24');

-- --------------------------------------------------------

--
-- Struktur dari tabel `articles`
--

CREATE TABLE `articles` (
  `id` int UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `excerpt` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('draft','published') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `views` int UNSIGNED NOT NULL DEFAULT '0',
  `author_id` int UNSIGNED DEFAULT NULL,
  `category_id` int UNSIGNED DEFAULT NULL,
  `published_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `article_categories`
--

CREATE TABLE `article_categories` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `color` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '#6c757d' COMMENT 'Warna badge untuk tampilan',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `article_categories`
--

INSERT INTO `article_categories` (`id`, `name`, `slug`, `description`, `color`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Renungan', 'renungan', 'Artikel renungan harian dan mingguan', '#0d6efd', 0, '2026-01-25 02:49:38', '2026-01-25 08:08:00'),
(2, 'Berita Gereja', 'berita-gereja', 'Berita dan informasi seputar gereja', '#198754', 0, '2026-01-25 02:49:38', '2026-02-21 15:02:02'),
(3, 'Kesaksian', 'kesaksian', 'Kesaksian jemaat', '#ffc107', 0, '2026-01-25 02:49:38', '2026-01-25 08:07:58'),
(4, 'Pengajaran', 'pengajaran', 'Artikel pengajaran dan doktrin', '#6f42c1', 0, '2026-01-25 02:49:38', '2026-01-25 08:07:59'),
(5, 'Keluarga', 'keluarga', 'Artikel seputar keluarga Kristen', '#fd7e14', 0, '2026-01-25 02:49:38', '2026-01-25 08:07:57'),
(6, 'Pemuda', 'pemuda', 'Artikel untuk pemuda', '#20c997', 0, '2026-01-25 02:49:38', '2026-01-25 08:07:59');

-- --------------------------------------------------------

--
-- Struktur dari tabel `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `read_at` datetime DEFAULT NULL,
  `read_by` int UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `donation_accounts`
--

CREATE TABLE `donation_accounts` (
  `id` int UNSIGNED NOT NULL,
  `bank_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nama Bank (BCA, Mandiri, dll)',
  `account_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nomor Rekening',
  `account_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nama Pemilik Rekening',
  `bank_logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Logo bank (opsional)',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Status aktif',
  `sort_order` int NOT NULL DEFAULT '0' COMMENT 'Urutan tampil',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `events`
--

CREATE TABLE `events` (
  `id` int UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_date` date NOT NULL,
  `event_time` time NOT NULL,
  `end_date` date DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('draft','published') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_by` int UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `members`
--

CREATE TABLE `members` (
  `id` int UNSIGNED NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` enum('M','F') COLLATE utf8mb4_unicode_ci NOT NULL,
  `birth_date` date DEFAULT NULL,
  `birth_place` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `baptism_date` date DEFAULT NULL,
  `membership_date` date DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` int UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `preacher_schedules`
--

CREATE TABLE `preacher_schedules` (
  `id` int UNSIGNED NOT NULL,
  `schedule_date` date NOT NULL COMMENT 'Tanggal ibadah',
  `service_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nama ibadah (Ibadah Raya, Sekolah Minggu, dll)',
  `service_time` time NOT NULL COMMENT 'Waktu ibadah',
  `preacher_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nama pengkhotbah (NULL jika belum diketahui)',
  `sermon_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Judul khotbah (opsional)',
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Catatan tambahan',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `rate_limits`
--

CREATE TABLE `rate_limits` (
  `id` int UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` int UNSIGNED NOT NULL DEFAULT '1',
  `last_attempt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `schedules`
--

CREATE TABLE `schedules` (
  `id` int UNSIGNED NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `day_of_week` enum('Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu') COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time DEFAULT NULL,
  `location` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_by` int UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `settings`
--

CREATE TABLE `settings` (
  `id` int UNSIGNED NOT NULL,
  `key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `group` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `type`, `group`, `description`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'GBI HOP CISEENG', 'text', 'general', 'Nama website', '2026-01-25 00:51:16', '2026-02-21 21:25:39'),
(2, 'site_tagline', 'Melayani dengan Kasih', 'text', 'general', 'Tagline website', '2026-01-25 00:51:16', '2026-02-21 21:25:39'),
(3, 'site_email', 'gbihopciseeng@gmail.com', 'email', 'general', 'Email utama', '2026-01-25 00:51:16', '2026-02-21 21:25:39'),
(4, 'site_phone', '08xxxxxxxxxx', 'text', 'general', 'Nomor telepon', '2026-01-25 00:51:16', '2026-02-21 21:25:39'),
(5, 'site_address', 'Jalan Iwul, Parigi Mekar, Kec. Ciseeng, Kabupaten Bogor, Jawa Barat', 'textarea', 'general', 'Alamat gereja', '2026-01-25 00:51:16', '2026-02-21 21:25:39'),
(6, 'site_facebook', '', 'url', 'social', 'Link Facebook', '2026-01-25 00:51:16', '2026-02-21 21:25:39'),
(7, 'site_instagram', 'https://www.instagram.com/gbihopciseeng/', 'url', 'social', 'Link Instagram', '2026-01-25 00:51:16', '2026-02-21 21:25:39'),
(8, 'site_youtube', 'https://www.youtube.com/@GBIHOUSEOFPRAYERCISEENG', 'url', 'social', 'Link YouTube', '2026-01-25 00:51:16', '2026-02-21 21:25:39'),
(11, 'site_description', 'Website resmi Gereja Bethel Indonesia House Of Prayer Ciseeng', 'textarea', 'general', 'Deskripsi website untuk SEO', '2026-01-25 02:08:02', '2026-02-21 21:25:39'),
(14, 'site_whatsapp', '62xxxxxxxx', 'text', 'contact', 'Nomor WhatsApp', '2026-01-25 02:08:02', '2026-02-21 21:25:39'),
(16, 'hero_title', 'Selamat Datang di', 'text', 'hero', 'Judul utama hero section', '2026-01-25 02:08:02', '2026-02-21 21:25:39'),
(17, 'hero_subtitle', 'Gereja Bethel Indonesia House Of Prayer Ciseeng', 'text', 'hero', 'Sub judul hero section', '2026-01-25 02:08:02', '2026-02-21 21:25:39'),
(18, 'hero_verse', 'Karena di mana dua atau tiga orang berkumpul dalam nama-Ku, di situ Aku ada di tengah-tengah mereka.', 'textarea', 'hero', 'Ayat Alkitab', '2026-01-25 02:08:02', '2026-02-21 21:25:39'),
(19, 'hero_verse_ref', 'Matius 18:20', 'text', 'hero', 'Referensi ayat', '2026-01-25 02:08:02', '2026-02-21 21:25:39'),
(23, 'site_tiktok', '', 'url', 'social', 'Link TikTok', '2026-01-25 02:08:02', '2026-02-21 21:25:39'),
(24, 'donation_title', 'Dukung Pelayanan Kami', 'text', 'donation', 'Judul halaman donasi', '2026-01-25 02:08:02', '2026-02-21 21:25:39'),
(25, 'donation_description', 'Persembahan dan donasi Anda sangat berarti untuk mendukung pelayanan gereja dan membantu sesama.', 'textarea', 'donation', 'Deskripsi donasi', '2026-01-25 02:08:02', '2026-02-21 21:25:39'),
(26, 'donation_bank_name', '', 'text', 'donation', 'Nama Bank 1', '2026-01-25 02:08:02', '2026-02-21 21:25:39'),
(27, 'donation_bank_account', '', 'text', 'donation', 'Nomor Rekening 1', '2026-01-25 02:08:02', '2026-02-21 21:25:39'),
(28, 'donation_account_name', '', 'text', 'donation', 'Atas Nama 1', '2026-01-25 02:08:02', '2026-02-21 21:25:39'),
(29, 'donation_bank_name_2', '', 'text', 'donation', 'Nama Bank 2', '2026-01-25 02:08:02', '2026-02-21 21:25:39'),
(30, 'donation_bank_account_2', '', 'text', 'donation', 'Nomor Rekening 2', '2026-01-25 02:08:02', '2026-02-21 21:25:39'),
(31, 'donation_account_name_2', '', 'text', 'donation', 'Atas Nama 2', '2026-01-25 02:08:02', '2026-02-21 21:25:39'),
(32, 'about_vision', 'Menjadi gereja yang membawa transformasi bagi masyarakat melalui kasih Kristus.', 'textarea', 'about', 'Visi gereja', '2026-01-25 02:08:02', '2026-02-21 21:25:39'),
(33, 'about_mission', 'Menyebarkan Injil kepada semua orang\r\nMembina jemaat dalam iman dan kasih\r\nMelayani sesama dengan tulus\r\nMembangun komunitas yang saling mendukung', 'textarea', 'about', 'Misi gereja', '2026-01-25 02:08:02', '2026-02-21 21:25:39'),
(34, 'about_history', 'Gereja Bethel Indonesia Ciseeng didirikan dengan visi untuk menjangkau masyarakat di wilayah Ciseeng dan sekitarnya.', 'textarea', 'about', 'Sejarah gereja', '2026-01-25 02:08:02', '2026-02-21 21:25:39'),
(35, 'about_pastor', 'Pdt. Okan Suhendra', 'text', 'about', 'Nama gembala/pendeta', '2026-01-25 02:08:02', '2026-02-21 21:25:39'),
(36, 'site_logo', 'logo_1769340609.png', 'text', 'general', NULL, '2026-01-25 02:17:22', '2026-01-25 18:30:09'),
(37, 'hero_image', 'hero_1769282431.png', 'text', 'hero', NULL, '2026-01-25 02:17:22', '2026-01-25 02:20:31'),
(38, 'donation_qris_image', '', 'image', 'donation', 'Gambar QRIS untuk donasi', '2026-01-25 13:55:59', '2026-02-21 21:26:01'),
(39, 'donation_qris_name', '', 'text', 'donation', 'Nama QRIS (opsional)', '2026-01-25 13:55:59', '2026-02-21 21:26:01'),
(40, 'site_operational_hours', 'Minggu: 08:00 - 16:00', 'textarea', 'contact', 'Jam operasional gereja', '2026-01-25 14:21:00', '2026-02-21 21:25:39'),
(49, 'site_gmaps_embed', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3964.557737417737!2d106.700113!3d-6.4507789!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69e7eb43e30b6f%3A0x1b1e5e0267ce6814!2sGBI%20House%20Of%20Prayer%20Ciseeng!5e0!3m2!1sid!2sid!4v1746534651424!5m2!1sid!2sid', 'text', 'general', NULL, '2026-01-25 14:23:31', '2026-02-21 21:25:39'),
(226, 'about_image', 'about_1771682873.png', 'text', 'general', NULL, '2026-02-21 21:00:49', '2026-02-21 21:07:53');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('super_admin','admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'admin',
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `login_attempts` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `locked_until` datetime DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `status`, `last_login`, `login_attempts`, `locked_until`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'admin@gbiciseeng.com', '$2y$12$zyoCZabz67tWiKWNX..dSu8InmD6YZMnpnao9Wu5vO8aXgMTidb/y', 'super_admin', 'active', '2026-02-21 21:25:07', 0, NULL, NULL, '2026-01-25 00:51:15', '2026-02-21 21:25:07');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_module` (`module`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indeks untuk tabel `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_published_at` (`published_at`),
  ADD KEY `fk_articles_author` (`author_id`),
  ADD KEY `idx_category` (`category_id`);

--
-- Indeks untuk tabel `article_categories`
--
ALTER TABLE `article_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indeks untuk tabel `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_is_read` (`is_read`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `fk_messages_read_by` (`read_by`);

--
-- Indeks untuk tabel `donation_accounts`
--
ALTER TABLE `donation_accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_is_active` (`is_active`),
  ADD KEY `idx_sort_order` (`sort_order`);

--
-- Indeks untuk tabel `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_event_date` (`event_date`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_is_featured` (`is_featured`),
  ADD KEY `fk_events_user` (`created_by`);

--
-- Indeks untuk tabel `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_full_name` (`full_name`),
  ADD KEY `idx_gender` (`gender`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `fk_members_user` (`created_by`);

--
-- Indeks untuk tabel `preacher_schedules`
--
ALTER TABLE `preacher_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_schedule_date` (`schedule_date`),
  ADD KEY `idx_month_year` (`schedule_date`);

--
-- Indeks untuk tabel `rate_limits`
--
ALTER TABLE `rate_limits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key` (`key`),
  ADD KEY `idx_expires_at` (`expires_at`);

--
-- Indeks untuk tabel `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_day_of_week` (`day_of_week`),
  ADD KEY `idx_is_active` (`is_active`),
  ADD KEY `idx_sort_order` (`sort_order`),
  ADD KEY `fk_schedules_user` (`created_by`);

--
-- Indeks untuk tabel `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key` (`key`),
  ADD KEY `idx_group` (`group`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_status` (`status`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `article_categories`
--
ALTER TABLE `article_categories`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `donation_accounts`
--
ALTER TABLE `donation_accounts`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `events`
--
ALTER TABLE `events`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `members`
--
ALTER TABLE `members`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `preacher_schedules`
--
ALTER TABLE `preacher_schedules`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT untuk tabel `rate_limits`
--
ALTER TABLE `rate_limits`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=295;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `fk_activity_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `fk_article_category` FOREIGN KEY (`category_id`) REFERENCES `article_categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_articles_author` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD CONSTRAINT `fk_messages_read_by` FOREIGN KEY (`read_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `fk_events_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `members`
--
ALTER TABLE `members`
  ADD CONSTRAINT `fk_members_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `fk_schedules_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

DELIMITER $$
--
-- Event
--
CREATE DEFINER=`cxhuqbmc`@`localhost` EVENT `cleanup_rate_limits` ON SCHEDULE EVERY 1 HOUR STARTS '2026-01-25 18:08:45' ON COMPLETION NOT PRESERVE ENABLE DO DELETE FROM rate_limits
  WHERE expires_at < NOW()$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
