<?php
namespace App\Middleware;

use Core\Session;

/**
 * =========================================================
 * Role Middleware (RBAC - Role Based Access Control)
 * =========================================================
 * 
 * Middleware untuk mengecek role user
 * Memastikan user memiliki akses ke resource tertentu
 */
class RoleMiddleware
{
    /**
     * Cek apakah user memiliki role tertentu
     * 
     * @param string|array $roles Role atau array of roles yang diizinkan
     * @return bool
     */
    public static function hasRole($roles): bool
    {
        // Pastikan user sudah login
        if (!AuthMiddleware::check()) {
            return false;
        }
        
        $user = Session::get('user');
        
        if (!$user || !isset($user['role'])) {
            setFlash('error', 'Data user tidak valid.');
            redirect('auth/login');
            return false;
        }
        
        // Jika roles adalah string, konversi ke array
        if (is_string($roles)) {
            $roles = [$roles];
        }
        
        // Cek apakah user role ada di dalam allowed roles
        if (!in_array($user['role'], $roles)) {
            setFlash('error', 'Anda tidak memiliki akses ke halaman ini.');
            redirect('admin/dashboard');
            return false;
        }
        
        return true;
    }
    
    /**
     * Cek apakah user adalah Super Admin
     * 
     * @return bool
     */
    public static function isSuperAdmin(): bool
    {
        return self::hasRole(ROLE_SUPER_ADMIN);
    }
    
    /**
     * Cek apakah user adalah Admin (termasuk Super Admin)
     * 
     * @return bool
     */
    public static function isAdmin(): bool
    {
        return self::hasRole([ROLE_ADMIN, ROLE_SUPER_ADMIN]);
    }
    
    /**
     * Require role Super Admin
     * Untuk aksi khusus super admin (manage users, view logs)
     * 
     * @return bool
     */
    public static function requireSuperAdmin(): bool
    {
        if (!self::isSuperAdmin()) {
            setFlash('error', 'Hanya Super Admin yang dapat mengakses fitur ini.');
            redirect('admin/dashboard');
            return false;
        }
        
        return true;
    }
    
    /**
     * Require role Admin atau Super Admin
     * Untuk aksi admin umum (CRUD content)
     * 
     * @return bool
     */
    public static function requireAdmin(): bool
    {
        if (!self::isAdmin()) {
            setFlash('error', 'Anda harus login sebagai Admin.');
            redirect('auth/login');
            return false;
        }
        
        return true;
    }
    
    /**
     * Cek akses berdasarkan resource dan action
     * Implementasi RBAC lebih detail
     * 
     * @param string $resource Nama resource (users, articles, etc)
     * @param string $action Aksi (create, read, update, delete)
     * @return bool
     */
    public static function can(string $resource, string $action): bool
    {
        $user = Session::get('user');
        
        if (!$user) {
            return false;
        }
        
        $role = $user['role'];
        
        // Definisi permission matrix
        $permissions = [
            ROLE_SUPER_ADMIN => [
                // Super Admin bisa semua
                'users' => ['create', 'read', 'update', 'delete', 'block'],
                'articles' => ['create', 'read', 'update', 'delete'],
                'events' => ['create', 'read', 'update', 'delete'],
                'schedules' => ['create', 'read', 'update', 'delete'],
                'members' => ['create', 'read', 'update', 'delete'],
                'messages' => ['read', 'delete'],
                'logs' => ['read'],
            ],
            ROLE_ADMIN => [
                // Admin biasa - tidak bisa manage users dan logs
                'articles' => ['create', 'read', 'update', 'delete'],
                'events' => ['create', 'read', 'update', 'delete'],
                'schedules' => ['create', 'read', 'update', 'delete'],
                'members' => ['create', 'read', 'update', 'delete'],
                'messages' => ['read', 'delete'],
            ],
        ];
        
        // Cek permission
        if (!isset($permissions[$role][$resource])) {
            return false;
        }
        
        return in_array($action, $permissions[$role][$resource]);
    }
    
    /**
     * Require permission untuk resource dan action tertentu
     * 
     * @param string $resource
     * @param string $action
     * @return bool
     */
    public static function requirePermission(string $resource, string $action): bool
    {
        if (!self::can($resource, $action)) {
            setFlash('error', 'Anda tidak memiliki izin untuk melakukan aksi ini.');
            redirect('admin/dashboard');
            return false;
        }
        
        return true;
    }
    
    /**
     * Dapatkan daftar menu yang bisa diakses user
     * 
     * @return array
     */
    public static function getAccessibleMenus(): array
    {
        $user = Session::get('user');
        
        if (!$user) {
            return [];
        }
        
        $role = $user['role'];
        
        // Menu dasar untuk semua admin
        $menus = [
            [
                'title' => 'Dashboard',
                'url' => 'admin/dashboard',
                'icon' => 'bi-speedometer2',
                'active' => 'admin/dashboard',
                'type' => 'single'
            ],
            [
                'title' => 'Jadwal',
                'icon' => 'bi-calendar-week',
                'type' => 'dropdown',
                'active' => 'admin/jadwal',
                'submenu' => [
                    [
                        'title' => 'Jadwal Ibadah',
                        'url' => 'admin/jadwal',
                        'icon' => 'bi-calendar-check',
                        'active' => 'admin/jadwal'
                    ],
                    [
                        'title' => 'Jadwal Pengkhotbah',
                        'url' => 'admin/jadwal-pengkhotbah',
                        'icon' => 'bi-person-video3',
                        'active' => 'admin/jadwal-pengkhotbah'
                    ]
                ]
            ],
            [
                'title' => 'Artikel',
                'icon' => 'bi-file-earmark-text',
                'type' => 'dropdown',
                'active' => 'admin/artikel',
                'submenu' => [
                    [
                        'title' => 'Kategori Artikel',
                        'url' => 'admin/kategori-artikel',
                        'icon' => 'bi-tags',
                        'active' => 'admin/kategori-artikel'
                    ],
                    [
                        'title' => 'Semua Artikel',
                        'url' => 'admin/artikel',
                        'icon' => 'bi-file-earmark-text',
                        'active' => 'admin/artikel'
                    ],
                ]
            ],
            [
                'title' => 'Kegiatan',
                'url' => 'admin/kegiatan',
                'icon' => 'bi-calendar-event',
                'active' => 'admin/kegiatan',
                'type' => 'single'
            ],
            [
                'title' => 'Data Jemaat',
                'url' => 'admin/jemaat',
                'icon' => 'bi-people',
                'active' => 'admin/jemaat',
                'type' => 'single'
            ],
            [
                'title' => 'Pesan Masuk',
                'url' => 'admin/pesan',
                'icon' => 'bi-envelope',
                'active' => 'admin/pesan',
                'type' => 'single'
            ],
            [
                'title' => 'Rekening Donasi',
                'url' => 'admin/rekening-donasi',
                'icon' => 'bi-credit-card',
                'active' => 'admin/rekening-donasi',
                'type' => 'single'
            ],
            [
                'title' => 'Pengaturan',
                'url' => 'admin/pengaturan',
                'icon' => 'bi-gear',
                'active' => 'admin/pengaturan',
                'type' => 'single'
            ],
        ];
        
        // Menu khusus Super Admin
        if ($role === ROLE_SUPER_ADMIN) {
            $menus[] = [
                'title' => 'Kelola Admin',
                'url' => 'admin/users',
                'icon' => 'bi-person-gear',
                'active' => 'admin/users',
                'type' => 'single'
            ];
            $menus[] = [
                'title' => 'Log Aktivitas',
                'url' => 'admin/logs',
                'icon' => 'bi-clock-history',
                'active' => 'admin/logs',
                'type' => 'single'
            ];
        }
        
        return $menus;
    }
}
