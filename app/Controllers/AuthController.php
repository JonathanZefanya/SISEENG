<?php
namespace App\Controllers;

use Core\Controller;
use Core\Session;
use Core\Security;
use App\Models\User;
use App\Models\ActivityLog;
use App\Middleware\AuthMiddleware;

/**
 * =========================================================
 * Auth Controller
 * =========================================================
 * 
 * Controller untuk autentikasi (login/logout)
 * Menerapkan keamanan:
 * - CSRF Protection
 * - Rate Limiting
 * - Session Regeneration
 * - Secure Password Verification
 */
class AuthController extends Controller
{
    protected $layout = null;
    private $userModel;
    
    public function __construct()
    {
        $this->userModel = new User();
    }
    
    /**
     * Halaman login
     */
    public function login(): void
    {
        // Redirect jika sudah login
        if (isLoggedIn()) {
            $this->redirect('admin/dashboard');
            return;
        }
        
        $this->view('auth/login', [
            'title' => 'Login - ' . APP_NAME,
        ]);
    }
    
    /**
     * Proses login
     */
    public function processLogin(): void
    {
        if (!$this->isPost()) {
            $this->redirect('auth/login');
            return;
        }
        
        // Validasi CSRF Token
        $this->validateCsrf();
        
        // Dapatkan IP untuk rate limiting
        $ip = Security::getClientIp();
        
        // Cek rate limiting
        if (!Security::checkLoginRateLimit($ip)) {
            $remaining = Security::getRemainingLockoutTime($ip);
            setFlash('error', "Terlalu banyak percobaan login. Silakan tunggu {$remaining} detik lagi.");
            $this->redirect('auth/login');
            return;
        }
        
        // Ambil data login
        $email = $this->post('email');
        $password = $this->post('password');
        
        // Validasi input
        if (empty($email) || empty($password)) {
            Security::recordLoginAttempt($ip);
            setFlash('error', 'Email dan password harus diisi.');
            $this->redirect('auth/login');
            return;
        }
        
        // Validasi email format
        if (!Security::validateEmail($email)) {
            Security::recordLoginAttempt($ip);
            setFlash('error', 'Format email tidak valid.');
            $this->redirect('auth/login');
            return;
        }
        
        // Coba autentikasi
        $user = $this->userModel->authenticate($email, $password);
        
        if (!$user) {
            // Catat percobaan gagal
            Security::recordLoginAttempt($ip);
            setFlash('error', 'Email atau password salah.');
            $this->redirect('auth/login');
            return;
        }
        
        // Login berhasil - reset rate limiting
        Security::resetLoginAttempts($ip);
        
        // Set session login dengan session regeneration
        Session::setLogin($user);
        
        // Catat aktivitas login
        ActivityLog::log($user['id'], 'login', 'User berhasil login', [
            'ip' => $ip,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
        
        // Set flash message sukses
        setFlash('success', 'Selamat datang kembali, ' . e($user['name']) . '!');
        
        // Redirect ke dashboard
        $this->redirect('admin/dashboard');
    }
    
    /**
     * Logout
     */
    public function logout(): void
    {
        // Catat aktivitas logout jika user login
        if (isLoggedIn()) {
            ActivityLog::log(auth('id'), 'logout', 'User logout');
        }
        
        // Hancurkan session
        Session::destroy();
        
        setFlash('success', 'Anda telah berhasil logout.');
        $this->redirect('auth/login');
    }
}
