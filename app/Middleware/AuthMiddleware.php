<?php
namespace App\Middleware;

use Core\Session;

/**
 * =========================================================
 * Auth Middleware
 * =========================================================
 * 
 * Middleware untuk mengecek apakah user sudah login
 */
class AuthMiddleware
{
    /**
     * Cek apakah user sudah login
     * Jika belum, redirect ke halaman login
     * 
     * @return bool
     */
    public static function check(): bool
    {
        if (!Session::has('user_id')) {
            setFlash('error', 'Silakan login terlebih dahulu.');
            redirect('auth/login');
            return false;
        }
        
        // Cek apakah session masih valid
        $user = Session::get('user');
        if (!$user || !isset($user['status']) || $user['status'] !== 'active') {
            Session::destroy();
            setFlash('error', 'Akun Anda tidak aktif atau telah diblokir.');
            redirect('auth/login');
            return false;
        }
        
        return true;
    }
    
    /**
     * Cek apakah user adalah guest (belum login)
     * Digunakan untuk halaman login/register
     * 
     * @return bool
     */
    public static function guest(): bool
    {
        if (Session::has('user_id')) {
            redirect('admin/dashboard');
            return false;
        }
        
        return true;
    }
    
    /**
     * Handle middleware
     * 
     * @param callable $next
     */
    public static function handle(callable $next): void
    {
        if (self::check()) {
            $next();
        }
    }
}
