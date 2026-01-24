<?php
namespace Core;

/**
 * =========================================================
 * Session Class
 * =========================================================
 * 
 * Mengelola session dengan pengaturan keamanan
 * 
 * KEAMANAN:
 * - Cookie HttpOnly mencegah akses JavaScript
 * - Cookie Secure untuk HTTPS only
 * - SameSite mencegah CSRF
 * - Session regeneration mencegah session fixation
 */
class Session
{
    /**
     * Status apakah session sudah dimulai
     * @var bool
     */
    private static $started = false;
    
    /**
     * Inisialisasi session dengan pengaturan aman
     */
    public static function init(): void
    {
        if (self::$started) {
            return;
        }
        
        // Set session name
        session_name(SESSION_NAME);
        
        // Konfigurasi cookie session yang aman
        session_set_cookie_params([
            'lifetime' => SESSION_LIFETIME,
            'path' => '/',
            'domain' => '',
            'secure' => SESSION_SECURE,     // Hanya kirim via HTTPS
            'httponly' => SESSION_HTTPONLY,  // Tidak bisa diakses JavaScript
            'samesite' => SESSION_SAMESITE   // Mencegah CSRF
        ]);
        
        // Mulai session
        session_start();
        
        self::$started = true;
        
        // Cek session timeout
        self::checkTimeout();
        
        // Regenerate session ID secara periodik (setiap 30 menit)
        self::regeneratePeriodically();
    }
    
    /**
     * Cek apakah session sudah timeout
     */
    private static function checkTimeout(): void
    {
        $lastActivity = self::get('last_activity');
        
        if ($lastActivity !== null) {
            $inactiveTime = time() - $lastActivity;
            
            if ($inactiveTime > SESSION_LIFETIME) {
                // Session expired
                self::destroy();
                redirect('auth/login?expired=1');
            }
        }
        
        // Update last activity time
        self::set('last_activity', time());
    }
    
    /**
     * Regenerate session ID secara periodik
     */
    private static function regeneratePeriodically(): void
    {
        $regenerateTime = self::get('session_regenerate_time');
        
        if ($regenerateTime === null) {
            self::set('session_regenerate_time', time());
            return;
        }
        
        // Regenerate setiap 30 menit
        if (time() - $regenerateTime > 1800) {
            self::regenerate();
            self::set('session_regenerate_time', time());
        }
    }
    
    /**
     * Set nilai session
     * @param string $key
     * @param mixed $value
     */
    public static function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }
    
    /**
     * Get nilai session
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }
    
    /**
     * Cek apakah key ada di session
     * @param string $key
     * @return bool
     */
    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }
    
    /**
     * Hapus nilai dari session
     * @param string $key
     */
    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }
    
    /**
     * Regenerate session ID (untuk keamanan)
     * Digunakan setelah login untuk mencegah session fixation
     */
    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }
    
    /**
     * Hancurkan session (logout)
     */
    public static function destroy(): void
    {
        // Hapus semua data session
        $_SESSION = [];
        
        // Hapus session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        
        // Hancurkan session
        session_destroy();
        
        self::$started = false;
    }
    
    /**
     * Set flash message (hanya muncul sekali)
     * @param string $type Tipe pesan (success, error, warning, info)
     * @param string $message Pesan
     */
    public static function setFlash(string $type, string $message): void
    {
        self::set('flash_' . $type, $message);
    }
    
    /**
     * Get flash message dan hapus setelahnya
     * @param string $type
     * @return string|null
     */
    public static function getFlash(string $type): ?string
    {
        $key = 'flash_' . $type;
        $message = self::get($key);
        self::remove($key);
        return $message;
    }
    
    /**
     * Cek apakah ada flash message
     * @param string $type
     * @return bool
     */
    public static function hasFlash(string $type): bool
    {
        return self::has('flash_' . $type);
    }
    
    /**
     * Set data untuk login
     * @param array $user Data user
     */
    public static function setLogin(array $user): void
    {
        // Regenerate session ID untuk mencegah session fixation
        self::regenerate();
        
        self::set('user_id', $user['id']);
        self::set('user', $user);
        self::set('logged_in_at', time());
    }
}
