<?php
namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use App\Middleware\RoleMiddleware;
use Core\Security;

/**
 * =========================================================
 * Admin User Controller
 * =========================================================
 * 
 * Controller untuk mengelola user admin (SUPER ADMIN ONLY)
 */
class UserController extends Controller
{
    protected $layout = 'admin';
    private $userModel;
    
    public function __construct()
    {
        // Hanya Super Admin yang boleh akses
        RoleMiddleware::requireSuperAdmin();
        $this->userModel = new User();
    }
    
    /**
     * Daftar semua user
     */
    public function index(): void
    {
        $page = (int) ($this->get('page') ?? 1);
        $users = $this->userModel->getAllWithPagination($page);
        
        $this->view('admin/users/index', [
            'title' => 'Kelola Admin - ' . APP_NAME,
            'users' => $users['data'],
            'pagination' => $users,
        ]);
    }
    
    /**
     * Form tambah user
     */
    public function create(): void
    {
        $this->view('admin/users/create', [
            'title' => 'Tambah Admin - ' . APP_NAME,
        ]);
    }
    
    /**
     * Simpan user baru
     */
    public function store(): void
    {
        if (!$this->isPost()) {
            $this->redirect('admin/users');
            return;
        }
        
        $this->validateCsrf();
        
        // Ambil dan validasi data
        $data = [
            'name' => $this->post('name'),
            'email' => $this->post('email'),
            'password' => $this->post('password'),
            'role' => $this->post('role'),
            'status' => 'active',
        ];
        
        // Validasi
        $errors = $this->validateUserData($data);
        
        if (!empty($errors)) {
            setFlash('error', implode('<br>', $errors));
            $this->saveOldInput();
            $this->redirect('admin/users/tambah');
            return;
        }
        
        // Cek email sudah digunakan
        if ($this->userModel->emailExists($data['email'])) {
            setFlash('error', 'Email sudah digunakan oleh user lain.');
            $this->saveOldInput();
            $this->redirect('admin/users/tambah');
            return;
        }
        
        // Simpan user
        try {
            $userId = $this->userModel->createUser($data);
            
            // Log aktivitas
            ActivityLog::log(auth('id'), 'create_user', "Membuat user baru: {$data['email']}", [
                'user_id' => $userId
            ]);
            
            setFlash('success', 'User berhasil ditambahkan.');
            $this->clearOldInput();
        } catch (\Exception $e) {
            setFlash('error', 'Gagal menambahkan user: ' . $e->getMessage());
            $this->saveOldInput();
            $this->redirect('admin/users/tambah');
            return;
        }
        
        $this->redirect('admin/users');
    }
    
    /**
     * Form edit user
     */
    public function edit(int $id = 0): void
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            setFlash('error', 'User tidak ditemukan.');
            $this->redirect('admin/users');
            return;
        }
        
        $this->view('admin/users/edit', [
            'title' => 'Edit Admin - ' . APP_NAME,
            'user' => $user,
        ]);
    }
    
    /**
     * Update user
     */
    public function update(): void
    {
        if (!$this->isPost()) {
            $this->redirect('admin/users');
            return;
        }
        
        $this->validateCsrf();
        
        $id = (int) $this->post('id');
        
        if (!$id) {
            setFlash('error', 'ID user tidak valid.');
            $this->redirect('admin/users');
            return;
        }
        
        $existingUser = $this->userModel->find($id);
        
        if (!$existingUser) {
            setFlash('error', 'User tidak ditemukan.');
            $this->redirect('admin/users');
            return;
        }
        
        // Ambil data update
        $data = [
            'name' => $this->post('name'),
            'email' => $this->post('email'),
            'role' => $this->post('role'),
        ];
        
        // Password opsional saat update
        $password = $this->post('password');
        if (!empty($password)) {
            $data['password'] = $password;
        }
        
        // Validasi
        $errors = $this->validateUserData($data, $id, empty($password));
        
        if (!empty($errors)) {
            setFlash('error', implode('<br>', $errors));
            $this->saveOldInput();
            $this->redirect('admin/users/edit/' . $id);
            return;
        }
        
        // Cek email sudah digunakan user lain
        if ($this->userModel->emailExists($data['email'], $id)) {
            setFlash('error', 'Email sudah digunakan oleh user lain.');
            $this->saveOldInput();
            $this->redirect('admin/users/edit/' . $id);
            return;
        }
        
        // Update user
        try {
            $this->userModel->updateUser($id, $data);
            
            // Log aktivitas
            ActivityLog::log(auth('id'), 'update_user', "Mengupdate user: {$data['email']}", [
                'user_id' => $id
            ]);
            
            setFlash('success', 'User berhasil diupdate.');
            $this->clearOldInput();
        } catch (\Exception $e) {
            setFlash('error', 'Gagal mengupdate user.');
            $this->saveOldInput();
            $this->redirect('admin/users/edit/' . $id);
            return;
        }
        
        $this->redirect('admin/users');
    }
    
    /**
     * Hapus user
     */
    public function delete(int $id = 0): void
    {
        if (!$this->isPost()) {
            $this->redirect('admin/users');
            return;
        }
        
        $this->validateCsrf();
        
        $user = $this->userModel->find($id);
        
        if (!$user) {
            setFlash('error', 'User tidak ditemukan.');
            $this->redirect('admin/users');
            return;
        }
        
        // Tidak boleh hapus diri sendiri
        if ($user['id'] == auth('id')) {
            setFlash('error', 'Anda tidak dapat menghapus akun sendiri.');
            $this->redirect('admin/users');
            return;
        }
        
        try {
            $this->userModel->delete($id);
            
            // Log aktivitas
            ActivityLog::log(auth('id'), 'delete_user', "Menghapus user: {$user['email']}", [
                'deleted_user_id' => $id
            ]);
            
            setFlash('success', 'User berhasil dihapus.');
        } catch (\Exception $e) {
            setFlash('error', 'Gagal menghapus user.');
        }
        
        $this->redirect('admin/users');
    }
    
    /**
     * Toggle status user (active/blocked)
     */
    public function toggleStatus(int $id = 0): void
    {
        if (!$this->isPost()) {
            $this->redirect('admin/users');
            return;
        }
        
        $this->validateCsrf();
        
        $user = $this->userModel->find($id);
        
        if (!$user) {
            setFlash('error', 'User tidak ditemukan.');
            $this->redirect('admin/users');
            return;
        }
        
        // Tidak boleh blokir diri sendiri
        if ($user['id'] == auth('id')) {
            setFlash('error', 'Anda tidak dapat memblokir akun sendiri.');
            $this->redirect('admin/users');
            return;
        }
        
        $newStatus = $this->userModel->toggleStatus($id);
        
        $action = $newStatus === 'blocked' ? 'memblokir' : 'mengaktifkan';
        
        // Log aktivitas
        ActivityLog::log(auth('id'), 'toggle_user_status', ucfirst($action) . " user: {$user['email']}", [
            'user_id' => $id,
            'new_status' => $newStatus
        ]);
        
        setFlash('success', "Berhasil {$action} user.");
        $this->redirect('admin/users');
    }
    
    /**
     * Validasi data user
     */
    private function validateUserData(array $data, ?int $exceptId = null, bool $skipPassword = false): array
    {
        $errors = [];
        
        if (empty($data['name'])) {
            $errors[] = 'Nama wajib diisi.';
        }
        
        if (empty($data['email']) || !Security::validateEmail($data['email'])) {
            $errors[] = 'Email tidak valid.';
        }
        
        if (!$skipPassword && (empty($data['password']) || strlen($data['password']) < 8)) {
            $errors[] = 'Password minimal 8 karakter.';
        }
        
        if (!in_array($data['role'], [ROLE_ADMIN, ROLE_SUPER_ADMIN])) {
            $errors[] = 'Role tidak valid.';
        }
        
        return $errors;
    }
}
