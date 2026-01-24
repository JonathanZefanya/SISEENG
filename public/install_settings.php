<?php
/**
 * Script untuk menambahkan settings ke database
 * Jalankan sekali: http://localhost/project-website/siseeng/public/install_settings.php
 */

// Load konfigurasi
define('BASE_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
require_once BASE_PATH . 'config/config.php';
require_once BASE_PATH . 'core/Database.php';

use Core\Database;

try {
    $db = Database::getInstance();
    
    $settings = [
        // General
        ['site_name', 'Gereja Bethel Indonesia Ciseeng', 'text', 'general', 'Nama website'],
        ['site_tagline', 'Melayani dengan Kasih', 'text', 'general', 'Tagline website'],
        ['site_description', 'Website resmi Gereja Bethel Indonesia Ciseeng', 'textarea', 'general', 'Deskripsi website untuk SEO'],
        
        // Contact
        ['site_email', 'info@gbiciseeng.com', 'email', 'contact', 'Email utama'],
        ['site_phone', '(021) 1234-5678', 'text', 'contact', 'Nomor telepon'],
        ['site_whatsapp', '08123456789', 'text', 'contact', 'Nomor WhatsApp'],
        ['site_address', 'Jl. Raya Ciseeng No. 123, Bogor, Jawa Barat', 'textarea', 'contact', 'Alamat gereja'],
        
        // Hero Section
        ['hero_title', 'Selamat Datang di', 'text', 'hero', 'Judul utama hero section'],
        ['hero_subtitle', 'Gereja Bethel Indonesia Ciseeng', 'text', 'hero', 'Sub judul hero section'],
        ['hero_verse', 'Karena di mana dua atau tiga orang berkumpul dalam nama-Ku, di situ Aku ada di tengah-tengah mereka.', 'textarea', 'hero', 'Ayat Alkitab'],
        ['hero_verse_ref', 'Matius 18:20', 'text', 'hero', 'Referensi ayat'],
        
        // Social Media
        ['site_facebook', 'https://facebook.com/gbiciseeng', 'url', 'social', 'Link Facebook'],
        ['site_instagram', 'https://instagram.com/gbiciseeng', 'url', 'social', 'Link Instagram'],
        ['site_youtube', 'https://youtube.com/gbiciseeng', 'url', 'social', 'Link YouTube'],
        ['site_tiktok', '', 'url', 'social', 'Link TikTok'],
        
        // Donation
        ['donation_title', 'Dukung Pelayanan Kami', 'text', 'donation', 'Judul halaman donasi'],
        ['donation_description', 'Persembahan dan donasi Anda sangat berarti untuk mendukung pelayanan gereja dan membantu sesama.', 'textarea', 'donation', 'Deskripsi donasi'],
        ['donation_bank_name', 'Bank BCA', 'text', 'donation', 'Nama Bank 1'],
        ['donation_bank_account', '1234567890', 'text', 'donation', 'Nomor Rekening 1'],
        ['donation_account_name', 'GBI Ciseeng', 'text', 'donation', 'Atas Nama 1'],
        ['donation_bank_name_2', '', 'text', 'donation', 'Nama Bank 2'],
        ['donation_bank_account_2', '', 'text', 'donation', 'Nomor Rekening 2'],
        ['donation_account_name_2', '', 'text', 'donation', 'Atas Nama 2'],
        
        // About
        ['about_vision', 'Menjadi gereja yang membawa transformasi bagi masyarakat melalui kasih Kristus.', 'textarea', 'about', 'Visi gereja'],
        ['about_mission', "Menyebarkan Injil kepada semua orang\nMembina jemaat dalam iman dan kasih\nMelayani sesama dengan tulus\nMembangun komunitas yang saling mendukung", 'textarea', 'about', 'Misi gereja'],
        ['about_history', 'Gereja Bethel Indonesia Ciseeng didirikan dengan visi untuk menjangkau masyarakat di wilayah Ciseeng dan sekitarnya.', 'textarea', 'about', 'Sejarah gereja'],
        ['about_pastor', 'Pdt. Nama Gembala', 'text', 'about', 'Nama gembala/pendeta'],
    ];
    
    $stmt = $db->prepare("INSERT IGNORE INTO settings (`key`, `value`, `type`, `group`, `description`) VALUES (?, ?, ?, ?, ?)");
    
    $inserted = 0;
    foreach ($settings as $setting) {
        $stmt->execute($setting);
        if ($stmt->rowCount() > 0) {
            $inserted++;
        }
    }
    
    echo "<!DOCTYPE html>
<html lang='id'>
<head>
    <meta charset='UTF-8'>
    <title>Install Settings</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body class='bg-light'>
    <div class='container py-5'>
        <div class='row justify-content-center'>
            <div class='col-md-6'>
                <div class='card shadow-sm'>
                    <div class='card-body text-center p-5'>
                        <i class='bi bi-check-circle text-success' style='font-size: 4rem;'></i>
                        <h2 class='mt-3'>Instalasi Berhasil!</h2>
                        <p class='text-muted'>$inserted settings baru telah ditambahkan ke database.</p>
                        <a href='../admin/pengaturan' class='btn btn-primary'>Buka Halaman Pengaturan</a>
                        <p class='mt-3 text-danger small'>Hapus file ini setelah selesai untuk keamanan!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css' rel='stylesheet'>
</body>
</html>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
