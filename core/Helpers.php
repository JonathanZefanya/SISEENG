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
    header("Location: " . APP_URL . "/" . ltrim($url, '/'));
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
    return APP_URL . '/assets/' . ltrim($path, '/');
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
