-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 24 Jan 2026 pada 20.55
-- Versi server: 9.2.0
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
  `old_data` json DEFAULT NULL,
  `new_data` json DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `user_name`, `action`, `description`, `module`, `record_id`, `old_data`, `new_data`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, NULL, 'logout', 'User logout', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 OPR/125.0.0.0', '2026-01-24 19:16:46'),
(2, 1, NULL, 'login', 'User berhasil login', NULL, NULL, NULL, '{\"ip\": \"::1\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 OPR/125.0.0.0\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 OPR/125.0.0.0', '2026-01-24 19:17:09'),
(3, 1, NULL, 'delete_article', 'Menghapus artikel: Selamat Datang di Website Gereja Kami', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 OPR/125.0.0.0', '2026-01-24 19:32:11'),
(4, 1, NULL, 'delete_member', 'Menghapus jemaat: ', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 OPR/125.0.0.0', '2026-01-24 19:34:34'),
(5, 1, NULL, 'create_article', 'Menambah artikel: awd', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 OPR/125.0.0.0', '2026-01-24 19:50:34'),
(6, 1, NULL, 'create_article', 'Menambah artikel: sefsef', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 OPR/125.0.0.0', '2026-01-24 19:51:08'),
(7, 1, NULL, 'delete_message', 'Menghapus pesan dari: Jonathan Zefanya', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 OPR/125.0.0.0', '2026-01-24 19:57:23'),
(8, 1, NULL, 'delete_member', 'Menghapus jemaat: Budi Santoso', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 OPR/125.0.0.0', '2026-01-24 20:41:31'),
(9, 1, NULL, 'delete_member', 'Menghapus jemaat: Siti Rahayu', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 OPR/125.0.0.0', '2026-01-24 20:41:33');

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

--
-- Dumping data untuk tabel `articles`
--

INSERT INTO `articles` (`id`, `title`, `slug`, `content`, `excerpt`, `image`, `status`, `views`, `author_id`, `category_id`, `published_at`, `created_at`, `updated_at`) VALUES
(2, 'awd', 'awd', 'awdawd', NULL, NULL, 'published', 0, 1, NULL, '2026-01-24 19:50:34', '2026-01-24 19:50:34', '2026-01-24 19:50:34'),
(3, 'sefsef', 'sefsef', 'sefsef', NULL, NULL, 'published', 0, 1, NULL, '2026-01-24 19:51:08', '2026-01-24 19:51:08', '2026-01-24 19:51:08');

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
(1, 'Renungan', 'renungan', 'Artikel renungan harian dan mingguan', '#0d6efd', 1, '2026-01-25 02:49:38', NULL),
(2, 'Berita Gereja', 'berita-gereja', 'Berita dan informasi seputar gereja', '#198754', 0, '2026-01-25 02:49:38', '2026-01-24 20:54:48'),
(3, 'Kesaksian', 'kesaksian', 'Kesaksian jemaat', '#ffc107', 1, '2026-01-25 02:49:38', NULL),
(4, 'Pengajaran', 'pengajaran', 'Artikel pengajaran dan doktrin', '#6f42c1', 1, '2026-01-25 02:49:38', NULL),
(5, 'Keluarga', 'keluarga', 'Artikel seputar keluarga Kristen', '#fd7e14', 1, '2026-01-25 02:49:38', NULL),
(6, 'Pemuda', 'pemuda', 'Artikel untuk pemuda', '#20c997', 1, '2026-01-25 02:49:38', NULL);

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
-- Struktur dari tabel `events`
--

CREATE TABLE `events` (
  `id` int UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_date` date NOT NULL,
  `event_time` time NOT NULL,
  `end_date` date DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('upcoming','ongoing','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'upcoming',
  `created_by` int UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `events`
--

INSERT INTO `events` (`id`, `title`, `description`, `image`, `event_date`, `event_time`, `end_date`, `end_time`, `location`, `is_featured`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Natal Bersama 2024', 'Perayaan Natal bersama seluruh jemaat. Acara akan dimeriahkan dengan penampilan paduan suara dan drama musikal.', NULL, '2026-02-24', '17:00:00', NULL, NULL, 'Gedung Utama Gereja', 1, 'upcoming', NULL, '2026-01-25 00:51:15', NULL),
(2, 'Retreat Pemuda', 'Retreat tahunan untuk pemuda dan remaja dengan tema \"Generasi Pembawa Terang\"', NULL, '2026-02-08', '08:00:00', NULL, NULL, 'Villa Grace, Puncak', 0, 'upcoming', NULL, '2026-01-25 00:51:15', NULL);

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
(1, 'site_name', 'GBI HOP CISEENG', 'text', 'general', 'Nama website', '2026-01-25 00:51:16', '2026-01-25 02:20:31'),
(2, 'site_tagline', 'Melayani dengan Kasih', 'text', 'general', 'Tagline website', '2026-01-25 00:51:16', '2026-01-25 02:20:31'),
(3, 'site_email', 'info@gereja.com', 'email', 'general', 'Email utama', '2026-01-25 00:51:16', '2026-01-25 02:20:31'),
(4, 'site_phone', '(021) 1234-5678', 'text', 'general', 'Nomor telepon', '2026-01-25 00:51:16', '2026-01-25 02:20:31'),
(5, 'site_address', 'Jl. Gereja No. 123, Jakarta', 'textarea', 'general', 'Alamat gereja', '2026-01-25 00:51:16', '2026-01-25 02:20:31'),
(6, 'site_facebook', 'https://facebook.com/gereja', 'url', 'social', 'Link Facebook', '2026-01-25 00:51:16', '2026-01-25 02:20:31'),
(7, 'site_instagram', 'https://instagram.com/gereja', 'url', 'social', 'Link Instagram', '2026-01-25 00:51:16', '2026-01-25 02:20:31'),
(8, 'site_youtube', 'https://youtube.com/gereja', 'url', 'social', 'Link YouTube', '2026-01-25 00:51:16', '2026-01-25 02:20:31'),
(11, 'site_description', 'Website resmi Gereja Bethel Indonesia House Of Prayer Ciseeng', 'textarea', 'general', 'Deskripsi website untuk SEO', '2026-01-25 02:08:02', '2026-01-25 02:20:31'),
(14, 'site_whatsapp', '08123456789', 'text', 'contact', 'Nomor WhatsApp', '2026-01-25 02:08:02', '2026-01-25 02:20:31'),
(16, 'hero_title', 'Selamat Datang di', 'text', 'hero', 'Judul utama hero section', '2026-01-25 02:08:02', '2026-01-25 02:20:31'),
(17, 'hero_subtitle', 'Gereja Bethel Indonesia House Of Prayer Ciseeng', 'text', 'hero', 'Sub judul hero section', '2026-01-25 02:08:02', '2026-01-25 02:20:31'),
(18, 'hero_verse', 'Karena di mana dua atau tiga orang berkumpul dalam nama-Ku, di situ Aku ada di tengah-tengah mereka.', 'textarea', 'hero', 'Ayat Alkitab', '2026-01-25 02:08:02', '2026-01-25 02:20:31'),
(19, 'hero_verse_ref', 'Matius 18:20', 'text', 'hero', 'Referensi ayat', '2026-01-25 02:08:02', '2026-01-25 02:20:31'),
(23, 'site_tiktok', '', 'url', 'social', 'Link TikTok', '2026-01-25 02:08:02', '2026-01-25 02:20:31'),
(24, 'donation_title', 'Dukung Pelayanan Kami', 'text', 'donation', 'Judul halaman donasi', '2026-01-25 02:08:02', '2026-01-25 02:20:31'),
(25, 'donation_description', 'Persembahan dan donasi Anda sangat berarti untuk mendukung pelayanan gereja dan membantu sesama.', 'textarea', 'donation', 'Deskripsi donasi', '2026-01-25 02:08:02', '2026-01-25 02:20:31'),
(26, 'donation_bank_name', 'Bank BCA', 'text', 'donation', 'Nama Bank 1', '2026-01-25 02:08:02', '2026-01-25 02:20:31'),
(27, 'donation_bank_account', '1234567890', 'text', 'donation', 'Nomor Rekening 1', '2026-01-25 02:08:02', '2026-01-25 02:20:31'),
(28, 'donation_account_name', 'GBI Ciseeng', 'text', 'donation', 'Atas Nama 1', '2026-01-25 02:08:02', '2026-01-25 02:20:31'),
(29, 'donation_bank_name_2', '', 'text', 'donation', 'Nama Bank 2', '2026-01-25 02:08:02', '2026-01-25 02:20:31'),
(30, 'donation_bank_account_2', '', 'text', 'donation', 'Nomor Rekening 2', '2026-01-25 02:08:02', '2026-01-25 02:20:31'),
(31, 'donation_account_name_2', '', 'text', 'donation', 'Atas Nama 2', '2026-01-25 02:08:02', '2026-01-25 02:20:31'),
(32, 'about_vision', 'Menjadi gereja yang membawa transformasi bagi masyarakat melalui kasih Kristus.', 'textarea', 'about', 'Visi gereja', '2026-01-25 02:08:02', '2026-01-25 02:20:31'),
(33, 'about_mission', 'Menyebarkan Injil kepada semua orang\r\nMembina jemaat dalam iman dan kasih\r\nMelayani sesama dengan tulus\r\nMembangun komunitas yang saling mendukung', 'textarea', 'about', 'Misi gereja', '2026-01-25 02:08:02', '2026-01-25 02:20:31'),
(34, 'about_history', 'Gereja Bethel Indonesia Ciseeng didirikan dengan visi untuk menjangkau masyarakat di wilayah Ciseeng dan sekitarnya.', 'textarea', 'about', 'Sejarah gereja', '2026-01-25 02:08:02', '2026-01-25 02:20:31'),
(35, 'about_pastor', 'Pdt. Nama Gembala', 'text', 'about', 'Nama gembala/pendeta', '2026-01-25 02:08:02', '2026-01-25 02:20:31'),
(36, 'site_logo', '', 'text', 'general', NULL, '2026-01-25 02:17:22', NULL),
(37, 'hero_image', 'hero_1769282431.png', 'text', 'hero', NULL, '2026-01-25 02:17:22', '2026-01-25 02:20:31');

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
(1, 'Super Admin', 'admin@gbiciseeng.com', '$2y$12$/1zBQIyWnQGWNN11gmyRJ.qCHsG/g8I6OLxV33RT0u19X4Kqw8I8i', 'super_admin', 'active', '2026-01-25 01:17:09', 0, NULL, NULL, '2026-01-25 00:51:15', '2026-01-25 01:17:09');

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
-- Indeks untuk tabel `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
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
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `article_categories`
--
ALTER TABLE `article_categories`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `events`
--
ALTER TABLE `events`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `members`
--
ALTER TABLE `members`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `preacher_schedules`
--
ALTER TABLE `preacher_schedules`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `rate_limits`
--
ALTER TABLE `rate_limits`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
CREATE DEFINER=`root`@`localhost` EVENT `cleanup_rate_limits` ON SCHEDULE EVERY 1 HOUR STARTS '2026-01-25 00:51:16' ON COMPLETION NOT PRESERVE ENABLE DO DELETE FROM `rate_limits` WHERE `expires_at` < NOW()$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
