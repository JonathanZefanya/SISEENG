<?php
namespace Core;

/**
 * =========================================================
 * App Class (Router)
 * =========================================================
 * 
 * Front Controller yang menangani routing
 * Mengurai URL dan memanggil controller yang sesuai
 */
class App
{
    /**
     * Controller yang akan dipanggil
     * @var string
     */
    protected $controller = DEFAULT_CONTROLLER;
    
    /**
     * Method/action yang akan dipanggil
     * @var string
     */
    protected $action = DEFAULT_ACTION;
    
    /**
     * Parameter yang diteruskan ke method
     * @var array
     */
    protected $params = [];
    
    /**
     * Daftar route yang terdaftar
     * @var array
     */
    protected $routes = [];
    
    /**
     * Constructor - parse URL
     */
    public function __construct()
    {
        $this->registerRoutes();
    }
    
    /**
     * Mendaftarkan route-route aplikasi
     */
    protected function registerRoutes(): void
    {
        // =====================================================
        // PUBLIC ROUTES
        // =====================================================
        $this->routes = [
            // Homepage
            '' => ['controller' => 'Home', 'action' => 'index'],
            '/' => ['controller' => 'Home', 'action' => 'index'],
            'home' => ['controller' => 'Home', 'action' => 'index'],
            
            // Tentang Kami
            'tentang' => ['controller' => 'About', 'action' => 'index'],
            'tentang/visi-misi' => ['controller' => 'About', 'action' => 'visiMisi'],
            'tentang/sejarah' => ['controller' => 'About', 'action' => 'sejarah'],
            
            // Kegiatan/Events (support both /kegiatan/{slug} and /kegiatan/detail/{slug})
            'kegiatan' => ['controller' => 'Event', 'action' => 'index'],
            'kegiatan/detail' => ['controller' => 'Event', 'action' => 'detail'],
            
            // Donasi
            'donasi' => ['controller' => 'Donation', 'action' => 'index'],
            
            // Kontak
            'kontak' => ['controller' => 'Contact', 'action' => 'index'],
            'kontak/kirim' => ['controller' => 'Contact', 'action' => 'send'],
            
            // Artikel/Berita (support both /artikel/{slug} and /artikel/baca/{slug})
            'artikel' => ['controller' => 'Article', 'action' => 'index'],
            'artikel/baca' => ['controller' => 'Article', 'action' => 'read'],
            
            // Jadwal Pengkhotbah Bulanan
            'jadwal-pengkhotbah' => ['controller' => 'Home', 'action' => 'preacherSchedule'],
            
            // =====================================================
            // AUTHENTICATION ROUTES
            // =====================================================
            'auth/login' => ['controller' => 'Auth', 'action' => 'login'],
            'auth/proses-login' => ['controller' => 'Auth', 'action' => 'processLogin'],
            'auth/logout' => ['controller' => 'Auth', 'action' => 'logout'],
            
            // =====================================================
            // ADMIN ROUTES
            // =====================================================
            'admin' => ['controller' => 'Admin\Dashboard', 'action' => 'index'],
            'admin/dashboard' => ['controller' => 'Admin\Dashboard', 'action' => 'index'],
            
            // Admin - Jadwal Ibadah
            'admin/jadwal' => ['controller' => 'Admin\Schedule', 'action' => 'index'],
            'admin/jadwal/tambah' => ['controller' => 'Admin\Schedule', 'action' => 'create'],
            'admin/jadwal/simpan' => ['controller' => 'Admin\Schedule', 'action' => 'store'],
            'admin/jadwal/edit' => ['controller' => 'Admin\Schedule', 'action' => 'edit'],
            'admin/jadwal/update' => ['controller' => 'Admin\Schedule', 'action' => 'update'],
            'admin/jadwal/hapus' => ['controller' => 'Admin\Schedule', 'action' => 'delete'],
            
            // Admin - Artikel (Indonesian & English URLs)
            'admin/artikel' => ['controller' => 'Admin\Article', 'action' => 'index'],
            'admin/artikel/tambah' => ['controller' => 'Admin\Article', 'action' => 'create'],
            'admin/artikel/simpan' => ['controller' => 'Admin\Article', 'action' => 'store'],
            'admin/artikel/edit' => ['controller' => 'Admin\Article', 'action' => 'edit'],
            'admin/artikel/update' => ['controller' => 'Admin\Article', 'action' => 'update'],
            'admin/artikel/hapus' => ['controller' => 'Admin\Article', 'action' => 'delete'],
            // English aliases
            'admin/articles' => ['controller' => 'Admin\Article', 'action' => 'index'],
            'admin/articles/create' => ['controller' => 'Admin\Article', 'action' => 'create'],
            'admin/articles/store' => ['controller' => 'Admin\Article', 'action' => 'store'],
            'admin/articles/edit' => ['controller' => 'Admin\Article', 'action' => 'edit'],
            'admin/articles/update' => ['controller' => 'Admin\Article', 'action' => 'update'],
            'admin/articles/delete' => ['controller' => 'Admin\Article', 'action' => 'delete'],
            
            // Admin - Kategori Artikel
            'admin/kategori-artikel' => ['controller' => 'Admin\ArticleCategory', 'action' => 'index'],
            'admin/kategori-artikel/create' => ['controller' => 'Admin\ArticleCategory', 'action' => 'create'],
            'admin/kategori-artikel/store' => ['controller' => 'Admin\ArticleCategory', 'action' => 'store'],
            'admin/kategori-artikel/edit' => ['controller' => 'Admin\ArticleCategory', 'action' => 'edit'],
            'admin/kategori-artikel/update' => ['controller' => 'Admin\ArticleCategory', 'action' => 'update'],
            'admin/kategori-artikel/delete' => ['controller' => 'Admin\ArticleCategory', 'action' => 'delete'],
            'admin/kategori-artikel/toggle-status' => ['controller' => 'Admin\ArticleCategory', 'action' => 'toggleStatus'],
            
            // Admin - Jemaat (Indonesian & English URLs)
            'admin/jemaat' => ['controller' => 'Admin\Member', 'action' => 'index'],
            'admin/jemaat/tambah' => ['controller' => 'Admin\Member', 'action' => 'create'],
            'admin/jemaat/simpan' => ['controller' => 'Admin\Member', 'action' => 'store'],
            'admin/jemaat/detail' => ['controller' => 'Admin\Member', 'action' => 'show'],
            'admin/jemaat/edit' => ['controller' => 'Admin\Member', 'action' => 'edit'],
            'admin/jemaat/update' => ['controller' => 'Admin\Member', 'action' => 'update'],
            'admin/jemaat/hapus' => ['controller' => 'Admin\Member', 'action' => 'delete'],
            // English aliases
            'admin/members' => ['controller' => 'Admin\Member', 'action' => 'index'],
            'admin/members/create' => ['controller' => 'Admin\Member', 'action' => 'create'],
            'admin/members/store' => ['controller' => 'Admin\Member', 'action' => 'store'],
            'admin/members/show' => ['controller' => 'Admin\Member', 'action' => 'show'],
            'admin/members/edit' => ['controller' => 'Admin\Member', 'action' => 'edit'],
            'admin/members/update' => ['controller' => 'Admin\Member', 'action' => 'update'],
            'admin/members/delete' => ['controller' => 'Admin\Member', 'action' => 'delete'],
            
            // Admin - Kegiatan/Event (Indonesian & English URLs)
            'admin/kegiatan' => ['controller' => 'Admin\Event', 'action' => 'index'],
            'admin/kegiatan/tambah' => ['controller' => 'Admin\Event', 'action' => 'create'],
            'admin/kegiatan/simpan' => ['controller' => 'Admin\Event', 'action' => 'store'],
            'admin/kegiatan/edit' => ['controller' => 'Admin\Event', 'action' => 'edit'],
            'admin/kegiatan/update' => ['controller' => 'Admin\Event', 'action' => 'update'],
            'admin/kegiatan/hapus' => ['controller' => 'Admin\Event', 'action' => 'delete'],
            // English aliases
            'admin/events' => ['controller' => 'Admin\Event', 'action' => 'index'],
            'admin/events/create' => ['controller' => 'Admin\Event', 'action' => 'create'],
            'admin/events/store' => ['controller' => 'Admin\Event', 'action' => 'store'],
            'admin/events/edit' => ['controller' => 'Admin\Event', 'action' => 'edit'],
            'admin/events/update' => ['controller' => 'Admin\Event', 'action' => 'update'],
            'admin/events/delete' => ['controller' => 'Admin\Event', 'action' => 'delete'],
            
            // Admin - Pesan Kontak (Indonesian & English URLs)
            'admin/pesan' => ['controller' => 'Admin\Message', 'action' => 'index'],
            'admin/pesan/baca' => ['controller' => 'Admin\Message', 'action' => 'read'],
            'admin/pesan/hapus' => ['controller' => 'Admin\Message', 'action' => 'delete'],
            // English aliases
            'admin/messages' => ['controller' => 'Admin\Message', 'action' => 'index'],
            'admin/messages/read' => ['controller' => 'Admin\Message', 'action' => 'read'],
            'admin/messages/delete' => ['controller' => 'Admin\Message', 'action' => 'delete'],
            
            // =====================================================
            // SUPER ADMIN ROUTES
            // =====================================================
            'admin/users' => ['controller' => 'Admin\User', 'action' => 'index'],
            'admin/users/tambah' => ['controller' => 'Admin\User', 'action' => 'create'],
            'admin/users/simpan' => ['controller' => 'Admin\User', 'action' => 'store'],
            'admin/users/edit' => ['controller' => 'Admin\User', 'action' => 'edit'],
            'admin/users/update' => ['controller' => 'Admin\User', 'action' => 'update'],
            'admin/users/hapus' => ['controller' => 'Admin\User', 'action' => 'delete'],
            'admin/users/toggle-status' => ['controller' => 'Admin\User', 'action' => 'toggleStatus'],
            
            // Admin - Pengaturan Website
            'admin/pengaturan' => ['controller' => 'Admin\Setting', 'action' => 'index'],
            'admin/pengaturan/update' => ['controller' => 'Admin\Setting', 'action' => 'update'],
            'admin/settings' => ['controller' => 'Admin\Setting', 'action' => 'index'],
            'admin/settings/update' => ['controller' => 'Admin\Setting', 'action' => 'update'],
            
            // Admin - Jadwal Pengkhotbah
            'admin/jadwal-pengkhotbah' => ['controller' => 'Admin\PreacherSchedule', 'action' => 'index'],
            'admin/jadwal-pengkhotbah/create' => ['controller' => 'Admin\PreacherSchedule', 'action' => 'create'],
            'admin/jadwal-pengkhotbah/store' => ['controller' => 'Admin\PreacherSchedule', 'action' => 'store'],
            'admin/jadwal-pengkhotbah/edit' => ['controller' => 'Admin\PreacherSchedule', 'action' => 'edit'],
            'admin/jadwal-pengkhotbah/update' => ['controller' => 'Admin\PreacherSchedule', 'action' => 'update'],
            'admin/jadwal-pengkhotbah/delete' => ['controller' => 'Admin\PreacherSchedule', 'action' => 'delete'],
            'admin/jadwal-pengkhotbah/generate' => ['controller' => 'Admin\PreacherSchedule', 'action' => 'generate'],
            'admin/jadwal-pengkhotbah/quick-update' => ['controller' => 'Admin\PreacherSchedule', 'action' => 'quickUpdate'],
            
            // Admin - Rekening Donasi
            'admin/rekening-donasi' => ['controller' => 'Admin\DonationAccount', 'action' => 'index'],
            'admin/rekening-donasi/create' => ['controller' => 'Admin\DonationAccount', 'action' => 'create'],
            'admin/rekening-donasi/store' => ['controller' => 'Admin\DonationAccount', 'action' => 'store'],
            'admin/rekening-donasi/edit' => ['controller' => 'Admin\DonationAccount', 'action' => 'edit'],
            'admin/rekening-donasi/update' => ['controller' => 'Admin\DonationAccount', 'action' => 'update'],
            'admin/rekening-donasi/delete' => ['controller' => 'Admin\DonationAccount', 'action' => 'delete'],
            'admin/rekening-donasi/toggle' => ['controller' => 'Admin\DonationAccount', 'action' => 'toggle'],
            'admin/rekening-donasi/update-qris' => ['controller' => 'Admin\DonationAccount', 'action' => 'updateQris'],
            
            // Admin - Log Aktivitas (Super Admin only)
            'admin/logs' => ['controller' => 'Admin\ActivityLog', 'action' => 'index'],
        ];
    }
    
    /**
     * Parse URL dari request
     * @return array
     */
    protected function parseUrl(): array
    {
        $url = $_GET['url'] ?? '';
        $url = rtrim($url, '/');
        $url = filter_var($url, FILTER_SANITIZE_URL);
        
        return $url ? explode('/', $url) : [];
    }
    
    /**
     * Jalankan aplikasi
     */
    public function run(): void
    {
        $urlParts = $this->parseUrl();
        $url = implode('/', $urlParts);
        
        // Cek apakah URL cocok dengan route yang terdaftar
        if (isset($this->routes[$url])) {
            $this->controller = $this->routes[$url]['controller'];
            $this->action = $this->routes[$url]['action'];
        } else {
            // Coba cari dengan parameter
            // Contoh: kegiatan/detail/5 -> kegiatan/detail dengan param [5]
            $routeFound = false;
            for ($i = count($urlParts); $i > 0; $i--) {
                $testRoute = implode('/', array_slice($urlParts, 0, $i));
                if (isset($this->routes[$testRoute])) {
                    $this->controller = $this->routes[$testRoute]['controller'];
                    $this->action = $this->routes[$testRoute]['action'];
                    $this->params = array_slice($urlParts, $i);
                    $routeFound = true;
                    break;
                }
            }
            
            // Handle dynamic routes untuk public pages: kegiatan/{slug} -> detail, artikel/{slug} -> read
            if ($routeFound && count($this->params) > 0) {
                $baseRoute = implode('/', array_slice($urlParts, 0, count($urlParts) - count($this->params)));
                
                // Jika base route adalah 'kegiatan' dan ada parameter, arahkan ke detail
                if ($baseRoute === 'kegiatan' && $this->action === 'index') {
                    $this->action = 'detail';
                }
                // Jika base route adalah 'artikel' dan ada parameter, arahkan ke read
                elseif ($baseRoute === 'artikel' && $this->action === 'index') {
                    $this->action = 'read';
                }
            }
            
            // Jika tidak ditemukan, gunakan default atau 404
            if (!$routeFound && !empty($url)) {
                $this->show404();
                return;
            }
        }
        
        // Buat nama class controller dengan namespace
        $controllerClass = $this->resolveControllerClass($this->controller);
        
        // Cek apakah controller class ada
        if (!class_exists($controllerClass)) {
            $this->show404();
            return;
        }
        
        // Instantiate controller
        $controllerInstance = new $controllerClass();
        
        // Cek apakah method/action ada
        if (!method_exists($controllerInstance, $this->action)) {
            $this->show404();
            return;
        }
        
        // Panggil method dengan parameter
        call_user_func_array([$controllerInstance, $this->action], $this->params);
    }
    
    /**
     * Resolve controller class name dengan namespace
     * @param string $controller
     * @return string
     */
    protected function resolveControllerClass(string $controller): string
    {
        // Jika controller punya subfolder (misal: Admin\Dashboard)
        if (strpos($controller, '\\') !== false) {
            return "App\\Controllers\\{$controller}Controller";
        }
        
        return "App\\Controllers\\{$controller}Controller";
    }
    
    /**
     * Tampilkan halaman 404
     */
    protected function show404(): void
    {
        http_response_code(404);
        require_once VIEW_PATH . 'errors/404.php';
        exit;
    }
}
