<?php
/**
 * =========================================================
 * Helper Functions
 * =========================================================
 * 
 * Fungsi-fungsi pembantu yang sering digunakan
 */

/**
 * Redirect ke URL tertentu
 * @param string $url URL tujuan
 */
function redirect($url) {
    // Simpan session sebelum redirect
    // Ini memastikan flash messages dan data session tersimpan
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }
    
    // Build redirect URL dengan benar (hindari double slash)
    $baseUrl = rtrim(APP_URL, '/');
    $path = '/' . ltrim($url, '/');
    header("Location: " . $baseUrl . $path);
    exit;
}

/**
 * Escape output untuk mencegah XSS
 * @param string $string String yang akan di-escape
 * @return string String yang sudah aman
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Generate URL lengkap
 * @param string $path Path relatif
 * @return string URL lengkap
 */
function url($path = '') {
    return APP_URL . '/' . ltrim($path, '/');
}

/**
 * Generate URL untuk asset
 * @param string $path Path relatif dari folder assets
 * @return string URL asset
 */
function asset($path) {
    $path = ltrim($path, '/');
    $url = APP_URL . '/assets/' . $path;

    // Cache-busting: versi berubah setiap file diubah, jadi browser tidak memakai CSS/JS lama
    $file = PUBLIC_PATH . 'assets' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path);
    if (is_file($file)) {
        $url .= '?v=' . filemtime($file);
    }

    return $url;
}

/**
 * Get upload file URL
 * @param string $path Path relatif dari folder uploads
 * @return string URL file
 */
function uploads($path) {
    return APP_URL . '/uploads/' . ltrim($path, '/');
}

/**
 * Get current full URL
 * @return string Current URL
 */
function currentUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

/**
 * Get setting value from database
 * @param string $key Setting key
 * @param mixed $default Default value if not found
 * @return mixed
 */
function setting($key, $default = '') {
    static $settings = null;
    
    // Cache settings untuk performa
    if ($settings === null) {
        try {
            $settings = \App\Models\Setting::getAllAsArray();
        } catch (\Exception $e) {
            $settings = [];
        }
    }
    
    return $settings[$key] ?? $default;
}

/**
 * Cek apakah user sudah login
 * @return bool
 */
function isLoggedIn() {
    return Core\Session::has('user_id');
}

/**
 * Dapatkan data user yang sedang login
 * @param string|null $key Key spesifik atau null untuk semua data
 * @return mixed
 */
function auth($key = null) {
    if (!isLoggedIn()) {
        return null;
    }
    
    if ($key === null) {
        return Core\Session::get('user');
    }
    
    $user = Core\Session::get('user');
    return $user[$key] ?? null;
}

/**
 * Cek role user
 * @param string $role Role yang dicek
 * @return bool
 */
function hasRole($role) {
    return auth('role') === $role;
}

/**
 * Cek apakah user adalah Super Admin
 * @return bool
 */
function isSuperAdmin() {
    return hasRole(ROLE_SUPER_ADMIN);
}

/**
 * Cek apakah user adalah Admin (termasuk Super Admin)
 * @return bool
 */
function isAdmin() {
    return hasRole(ROLE_ADMIN) || hasRole(ROLE_SUPER_ADMIN);
}

/**
 * Format tanggal ke format Indonesia
 * @param string $date Tanggal
 * @param string $format Format output
 * @return string
 */
function formatDate($date, $format = 'd F Y') {
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    $hari = [
        'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
    ];
    
    $timestamp = strtotime($date);
    $formatted = date($format, $timestamp);
    
    // Ganti nama bulan
    foreach ($bulan as $key => $value) {
        $formatted = str_replace(date('F', mktime(0, 0, 0, $key, 1)), $value, $formatted);
    }
    
    // Ganti nama hari
    foreach ($hari as $eng => $ind) {
        $formatted = str_replace($eng, $ind, $formatted);
    }
    
    return $formatted;
}

/**
 * Format tanggal dan waktu ke format Indonesia
 * @param string $datetime Tanggal dan waktu
 * @param string $format Format output
 * @return string
 */
function formatDateTime($datetime, $format = 'd F Y H:i') {
    if (empty($datetime)) {
        return '-';
    }
    return formatDate($datetime, $format);
}

/**
 * Format waktu ke format 24 jam
 * @param string $time Waktu
 * @return string
 */
function formatTime($time) {
    return date('H:i', strtotime($time)) . ' WIB';
}

/**
 * Potong teks dengan panjang tertentu
 * @param string $text Teks
 * @param int $length Panjang maksimal
 * @param string $suffix Suffix jika dipotong
 * @return string
 */
function truncate($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

/**
 * Generate slug dari string
 * @param string $string String input
 * @return string Slug
 */
function slugify($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    $string = preg_replace('/[\s-]+/', '-', $string);
    return trim($string, '-');
}

/**
 * Set flash message
 * @param string $type Tipe pesan (success, error, warning, info)
 * @param string $message Pesan
 */
function setFlash($type, $message) {
    Core\Session::setFlash($type, $message);
}

/**
 * Get flash message
 * @param string $type Tipe pesan
 * @return string|null
 */
function getFlash($type) {
    return Core\Session::getFlash($type);
}

/**
 * Cek apakah ada flash message
 * @param string $type Tipe pesan
 * @return bool
 */
function hasFlash($type) {
    return Core\Session::hasFlash($type);
}

/**
 * Generate CSRF token field untuk form
 * @return string HTML hidden input
 */
function csrfField() {
    $token = Core\Security::generateCsrfToken();
    return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . e($token) . '">';
}

/**
 * Get old input value (untuk repopulate form setelah error)
 * @param string $key Key input
 * @param string $default Default value
 * @return string
 */
function old($key, $default = '') {
    $oldInput = Core\Session::get('old_input');
    return $oldInput[$key] ?? $default;
}

/**
 * Get month name in Indonesian
 * @param int $month Month number (1-12)
 * @return string Month name
 */
function getMonthName(int $month): string {
    $months = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    return $months[$month] ?? '';
}

/**
 * Get day name in Indonesian
 * @param int $day Day of week (0=Sunday, 1=Monday, ..., 6=Saturday)
 * @return string Day name
 */
function getDayName(int $day): string {
    $days = [
        0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu',
        4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'
    ];
    return $days[$day] ?? '';
}

/**
 * Format date to Indonesian format
 * @param string $date Date string
 * @param bool $withDay Include day name
 * @return string Formatted date
 */
function formatDateIndo(string $date, bool $withDay = false): string {
    $timestamp = strtotime($date);
    $day = date('j', $timestamp);
    $month = getMonthName((int) date('n', $timestamp));
    $year = date('Y', $timestamp);
    
    if ($withDay) {
        $dayName = getDayName((int) date('w', $timestamp));
        return "$dayName, $day $month $year";
    }
    
    return "$day $month $year";
}

/**
 * Warna tema default (hijau)
 */
const DEFAULT_THEME_COLOR = '#00aa13';

/**
 * Ambil warna tema dari pengaturan (format #rrggbb)
 * @return string
 */
function themeColor() {
    $color = strtolower(trim((string) setting('theme_color', DEFAULT_THEME_COLOR)));
    return preg_match('/^#[0-9a-f]{6}$/', $color) ? $color : DEFAULT_THEME_COLOR;
}

/**
 * Campur warna hex dengan warna lain
 * @param string $hex Warna asal (#rrggbb)
 * @param array $with RGB warna pencampur, mis. [0,0,0] untuk menggelapkan
 * @param float $amount 0..1, porsi warna pencampur
 * @return array RGB
 */
function mixColor($hex, array $with, $amount) {
    $rgb = sscanf($hex, '#%02x%02x%02x');
    foreach ($rgb as $i => $c) {
        $rgb[$i] = (int) round($c * (1 - $amount) + $with[$i] * $amount);
    }
    return $rgb;
}

/**
 * Tag <style> yang meng-override variabel warna tema di CSS
 * Dipanggil di <head> setelah stylesheet utama.
 * @return string
 */
function themeStyleTag() {
    $base = themeColor();
    if ($base === DEFAULT_THEME_COLOR) {
        return '';
    }

    $toHex = function (array $rgb) {
        return vsprintf('#%02x%02x%02x', $rgb);
    };
    $rgb = sscanf($base, '#%02x%02x%02x');
    $dark = mixColor($base, [0, 0, 0], 0.18);

    $vars = [
        '--brand' => $base,
        '--brand-rgb' => implode(', ', $rgb),
        '--brand-dark' => $toHex($dark),
        '--brand-dark-rgb' => implode(', ', $dark),
        '--brand-darker' => $toHex(mixColor($base, [0, 0, 0], 0.35)),
        '--brand-light' => $toHex(mixColor($base, [255, 255, 255], 0.25)),
        '--brand-soft' => $toHex(mixColor($base, [255, 255, 255], 0.88)),
    ];

    $css = '';
    foreach ($vars as $name => $value) {
        $css .= $name . ':' . $value . ';';
    }
    return '<style>:root{' . $css . '}</style>';
}

/**
 * Tag favicon dari logo yang diupload di Pengaturan (site_logo)
 * Versi file ditambahkan ke URL agar browser memuat ulang saat logo diganti.
 * @return string
 */
/**
 * Isi tanda brand: logo gereja (setting site_logo) jika ada, jika tidak ikon matahari
 * @return string HTML <img> atau <i>
 */
function brandMark() {
    $logo = setting('site_logo');
    if (!$logo) {
        return '<i class="bi bi-brightness-high-fill"></i>';
    }

    $file = PUBLIC_PATH . 'uploads' . DIRECTORY_SEPARATOR . 'settings' . DIRECTORY_SEPARATOR . $logo;
    $src = uploads('settings/' . $logo) . (is_file($file) ? '?v=' . filemtime($file) : '');

    return '<img class="brand-logo-img" src="' . e($src) . '" alt="' . e(setting('site_name') ?: APP_NAME) . '">';
}

function faviconTag() {
    $logo = setting('site_logo');
    if (!$logo) {
        return '';
    }

    $types = [
        'png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg',
        'gif' => 'image/gif', 'svg' => 'image/svg+xml', 'webp' => 'image/webp', 'ico' => 'image/x-icon',
    ];
    $ext = strtolower(pathinfo($logo, PATHINFO_EXTENSION));
    $type = $types[$ext] ?? 'image/png';

    $file = PUBLIC_PATH . 'uploads' . DIRECTORY_SEPARATOR . 'settings' . DIRECTORY_SEPARATOR . $logo;
    $href = e(uploads('settings/' . $logo) . (is_file($file) ? '?v=' . filemtime($file) : ''));

    return '<link rel="icon" type="' . $type . '" href="' . $href . '">' . "\n"
         . '    <link rel="apple-touch-icon" href="' . $href . '">';
}
