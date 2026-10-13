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
        
        if ($user['must_change_password']) {
            setFlash('warning', 'Demi keamanan, silakan ganti password Anda terlebih dahulu.');
            $this->redirect('auth/ganti-password');
            return;
        }

        // Set flash message sukses
        setFlash('success', 'Selamat datang kembali, ' . e($user['name']) . '!');
        
        // Redirect ke dashboard
        $this->redirect('admin/dashboard');
    }
    
    /**
     * Halaman ganti password (wajib jika must_change_password aktif)
     */
    public function changePassword(): void
    {
        if (!AuthMiddleware::check(false)) {
            return;
        }

        $this->view('auth/change-password', [
            'title' => 'Ganti Password - ' . APP_NAME,
            'forced' => !empty(auth('must_change_password')),
        ]);
    }

    /**
     * Simpan password baru
     */
    public function updatePassword(): void
    {
        if (!AuthMiddleware::check(false)) {
            return;
        }

        if (!$this->isPost()) {
            $this->redirect('auth/ganti-password');
            return;
        }

        $this->validateCsrf();

        $current = (string) $this->post('current_password');
        $new = (string) $this->post('new_password');
        $confirm = (string) $this->post('confirm_password');

        $user = $this->userModel->find((int) auth('id'));

        $error = null;
        if (!$user || !Security::verifyPassword($current, $user['password'])) {
            $error = 'Password saat ini salah.';
        } elseif (strlen($new) < 8) {
            $error = 'Password baru minimal 8 karakter.';
        } elseif ($new !== $confirm) {
            $error = 'Konfirmasi password baru tidak cocok.';
        } elseif ($new === $current || hash_equals(User::DEFAULT_PASSWORD, $new)) {
            $error = 'Password baru harus berbeda dari password lama dan password default.';
        }

        if ($error !== null) {
            setFlash('error', $error);
            $this->redirect('auth/ganti-password');
            return;
        }

        $this->userModel->changePassword((int) $user['id'], $new);

        // Perbarui session: hapus flag & regenerate ID
        $sessionUser = Session::get('user');
        $sessionUser['must_change_password'] = false;
        Session::setLogin($sessionUser);

        ActivityLog::log($user['id'], 'change_password', 'User mengganti password');

        setFlash('success', 'Password berhasil diganti.');
        $this->redirect('admin/dashboard');
    }

    /**
     * Tunda ganti password untuk sesi login ini saja.
     * Flag di database tidak diubah, jadi user diminta lagi pada login berikutnya.
     */
    public function skipPasswordChange(): void
    {
        if (!AuthMiddleware::check(false)) {
            return;
        }

        if (!$this->isPost()) {
            $this->redirect('auth/ganti-password');
            return;
        }

        $this->validateCsrf();

        $sessionUser = Session::get('user');
        $sessionUser['password_change_skipped'] = true;
        Session::set('user', $sessionUser);

        ActivityLog::log(auth('id'), 'skip_change_password', 'User menunda penggantian password');

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
