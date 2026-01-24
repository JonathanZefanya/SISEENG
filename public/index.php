<?php
/**
 * =========================================================
 * SISTEM INFORMASI MANAJEMEN GEREJA (SISEENG)
 * Entry Point / Front Controller
 * =========================================================
 * 
 * Semua request akan melewati file ini
 * File ini bertanggung jawab untuk:
 * 1. Mendefinisikan BASE_PATH
 * 2. Memuat konfigurasi
 * 3. Memuat autoloader
 * 4. Menjalankan aplikasi
 */

// Definisikan BASE_PATH terlebih dahulu
define('BASE_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);

// Muat file konfigurasi
require_once BASE_PATH . 'config/config.php';

// Muat autoloader
require_once CORE_PATH . 'Autoloader.php';

// Muat helper functions
require_once CORE_PATH . 'Helpers.php';

// Inisialisasi session dengan pengaturan aman
Core\Session::init();

// Jalankan aplikasi
$app = new Core\App();
$app->run();
