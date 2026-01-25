<?php
/**
 * =========================================================
 * SISTEM INFORMASI MANAJEMEN GEREJA (SISEENG)
 * Configuration File
 * =========================================================
 * 
 * File konfigurasi utama aplikasi
 * Berisi konstanta dan pengaturan global
 */

// Mencegah akses langsung ke file
if (!defined('BASE_PATH')) {
    exit('No direct script access allowed');
}

// =========================================================
// ENVIRONMENT SETTINGS
// =========================================================
define('ENVIRONMENT', 'development'); // 'development' atau 'production' - TEMPORARY DEBUG MODE

// Error reporting berdasarkan environment
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    // Path error log di dalam project folder
    ini_set('error_log', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'error.log');
}

// =========================================================
// APPLICATION SETTINGS
// =========================================================
define('APP_NAME', 'GBI HOP Ciseeng');
define('APP_VERSION', '1.0.0');

// Deteksi APP_URL secara otomatis
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
// define('APP_URL', $protocol . '://' . $host); // Uncomment untuk deployment

// TEMPORARY FIX FOR LOCALHOST
define('APP_URL', 'http://localhost/project-website/siseeng/public');

define('ADMIN_EMAIL', 'admin@gereja.com');

// =========================================================
// PATH DEFINITIONS
// =========================================================
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
}
define('APP_PATH', BASE_PATH . 'app' . DIRECTORY_SEPARATOR);
define('CORE_PATH', BASE_PATH . 'core' . DIRECTORY_SEPARATOR);
define('VIEW_PATH', BASE_PATH . 'views' . DIRECTORY_SEPARATOR);
define('PUBLIC_PATH', BASE_PATH . 'public' . DIRECTORY_SEPARATOR);
define('UPLOAD_PATH', PUBLIC_PATH . 'uploads' . DIRECTORY_SEPARATOR);

// =========================================================
// SECURITY SETTINGS
// =========================================================
define('CSRF_TOKEN_NAME', 'csrf_token');
define('CSRF_TOKEN_EXPIRE', 3600); // 1 jam dalam detik

// Session settings
define('SESSION_NAME', 'SISEENG_SESSION');
define('SESSION_LIFETIME', 7200); // 2 jam dalam detik
define('SESSION_SECURE', false); // Set true jika menggunakan HTTPS
define('SESSION_HTTPONLY', true);
define('SESSION_SAMESITE', 'Strict');
// Path untuk session files - gunakan tmp directory yang writable
define('SESSION_PATH', sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'siseeng_sessions');

// Rate limiting untuk login (DoS protection)
define('LOGIN_MAX_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 60); // dalam detik (1 menit)

// Password hashing
define('PASSWORD_ALGO', PASSWORD_BCRYPT);
define('PASSWORD_OPTIONS', ['cost' => 12]);

// =========================================================
// DATABASE SETTINGS
// =========================================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'gbi_ciseeng');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_CHARSET', 'utf8mb4');

// =========================================================
// DEFAULT ROUTES
// =========================================================
define('DEFAULT_CONTROLLER', 'Home');
define('DEFAULT_ACTION', 'index');

// =========================================================
// USER ROLES
// =========================================================
define('ROLE_SUPER_ADMIN', 'super_admin');
define('ROLE_ADMIN', 'admin');

// =========================================================
// PAGINATION
// =========================================================
define('ITEMS_PER_PAGE', 10);
