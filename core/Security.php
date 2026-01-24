<?php
namespace Core;

/**
 * =========================================================
 * Security Class
 * =========================================================
 * 
 * Menangani berbagai aspek keamanan aplikasi
 * - CSRF Protection
 * - Input sanitization
 * - Rate limiting
 * - XSS Prevention
 */
class Security
{
    /**
     * Generate CSRF token
     * @return string Token CSRF
     */
    public static function generateCsrfToken(): string
    {
        // Cek apakah token sudah ada dan masih valid
        $existingToken = Session::get(CSRF_TOKEN_NAME);
        $tokenTime = Session::get(CSRF_TOKEN_NAME . '_time');
        
        if ($existingToken && $tokenTime) {
            // Cek apakah token masih valid (belum expire)
            if (time() - $tokenTime < CSRF_TOKEN_EXPIRE) {
                return $existingToken;
            }
        }
        
        // Generate token baru
        $token = bin2hex(random_bytes(32));
        
        // Simpan ke session
        Session::set(CSRF_TOKEN_NAME, $token);
        Session::set(CSRF_TOKEN_NAME . '_time', time());
        
        return $token;
    }
    
    /**
     * Validasi CSRF token
     * @param string $token Token dari form
     * @return bool
     */
    public static function validateCsrfToken(?string $token): bool
    {
        if (empty($token)) {
            return false;
        }
        
        $sessionToken = Session::get(CSRF_TOKEN_NAME);
        $tokenTime = Session::get(CSRF_TOKEN_NAME . '_time');
        
        // Cek apakah token ada dan cocok
        if (!$sessionToken || !$tokenTime) {
            return false;
        }
        
        // Cek apakah token belum expire
        if (time() - $tokenTime > CSRF_TOKEN_EXPIRE) {
            return false;
        }
        
        // Gunakan hash_equals untuk mencegah timing attack
        return hash_equals($sessionToken, $token);
    }
    
    /**
     * Validasi CSRF dari request POST
     * @return bool
     */
    public static function verifyCsrf(): bool
    {
        $token = $_POST[CSRF_TOKEN_NAME] ?? null;
        return self::validateCsrfToken($token);
    }
    
    /**
     * Sanitasi input untuk mencegah XSS
     * @param mixed $input Input yang akan disanitasi
     * @return mixed
     */
    public static function sanitize($input)
    {
        if (is_array($input)) {
            return array_map([self::class, 'sanitize'], $input);
        }
        
        if (is_string($input)) {
            // Trim whitespace
            $input = trim($input);
            // Hapus null bytes
            $input = str_replace(chr(0), '', $input);
            // Konversi special chars
            $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        }
        
        return $input;
    }
    
    /**
     * Sanitasi input tanpa encoding HTML (untuk menyimpan ke database)
     * @param mixed $input
     * @return mixed
     */
    public static function cleanInput($input)
    {
        if (is_array($input)) {
            return array_map([self::class, 'cleanInput'], $input);
        }
        
        if (is_string($input)) {
            $input = trim($input);
            $input = str_replace(chr(0), '', $input);
            $input = stripslashes($input);
        }
        
        return $input;
    }
    
    /**
     * Cek rate limiting untuk login
     * @param string $ip IP address
     * @return bool True jika masih dalam limit
     */
    public static function checkLoginRateLimit(string $ip): bool
    {
        $key = 'login_attempts_' . md5($ip);
        $attempts = Session::get($key, []);
        
        // Hapus attempt yang sudah lebih dari LOCKOUT_TIME
        $now = time();
        $attempts = array_filter($attempts, function($timestamp) use ($now) {
            return ($now - $timestamp) < LOGIN_LOCKOUT_TIME;
        });
        
        // Simpan kembali
        Session::set($key, $attempts);
        
        // Cek apakah sudah melebihi limit
        return count($attempts) < LOGIN_MAX_ATTEMPTS;
    }
    
    /**
     * Catat percobaan login
     * @param string $ip IP address
     */
    public static function recordLoginAttempt(string $ip): void
    {
        $key = 'login_attempts_' . md5($ip);
        $attempts = Session::get($key, []);
        $attempts[] = time();
        Session::set($key, $attempts);
    }
    
    /**
     * Reset percobaan login (setelah login berhasil)
     * @param string $ip IP address
     */
    public static function resetLoginAttempts(string $ip): void
    {
        $key = 'login_attempts_' . md5($ip);
        Session::remove($key);
    }
    
    /**
     * Hitung sisa waktu lockout
     * @param string $ip IP address
     * @return int Sisa waktu dalam detik
     */
    public static function getRemainingLockoutTime(string $ip): int
    {
        $key = 'login_attempts_' . md5($ip);
        $attempts = Session::get($key, []);
        
        if (empty($attempts)) {
            return 0;
        }
        
        // Dapatkan attempt terakhir
        $lastAttempt = max($attempts);
        $remaining = LOGIN_LOCKOUT_TIME - (time() - $lastAttempt);
        
        return max(0, $remaining);
    }
    
    /**
     * Dapatkan IP address client
     * @return string
     */
    public static function getClientIp(): string
    {
        // Cek berbagai header untuk IP
        $headers = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];
        
        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ip = $_SERVER[$header];
                
                // Jika ada multiple IP (proxy), ambil yang pertama
                if (strpos($ip, ',') !== false) {
                    $ip = explode(',', $ip)[0];
                }
                
                $ip = trim($ip);
                
                // Validasi IP
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        
        return '0.0.0.0';
    }
    
    /**
     * Hash password dengan algorithm yang aman
     * @param string $password
     * @return string
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_ALGO, PASSWORD_OPTIONS);
    }
    
    /**
     * Verifikasi password
     * @param string $password Password plain
     * @param string $hash Password hash
     * @return bool
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
    
    /**
     * Cek apakah password perlu di-rehash
     * @param string $hash
     * @return bool
     */
    public static function needsRehash(string $hash): bool
    {
        return password_needs_rehash($hash, PASSWORD_ALGO, PASSWORD_OPTIONS);
    }
    
    /**
     * Validasi email
     * @param string $email
     * @return bool
     */
    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Generate random string
     * @param int $length
     * @return string
     */
    public static function generateRandomString(int $length = 32): string
    {
        return bin2hex(random_bytes($length / 2));
    }
}
