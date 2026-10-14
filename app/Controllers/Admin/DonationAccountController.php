<?php
namespace App\Controllers\Admin;

use Core\Controller;
use Core\Security;
use App\Models\DonationAccount;
use App\Models\ActivityLog;
use App\Middleware\RoleMiddleware;

/**
 * =========================================================
 * DonationAccount Controller (Admin)
 * =========================================================
 * 
 * Mengelola rekening bank menabur
 */
class DonationAccountController extends Controller
{
    protected $layout = 'admin';
    private $accountModel;
    private $uploadPath;
    
    public function __construct()
    {
        RoleMiddleware::requireAdmin();
        $this->accountModel = new DonationAccount();
        $this->uploadPath = PUBLIC_PATH . 'uploads' . DIRECTORY_SEPARATOR . 'donations' . DIRECTORY_SEPARATOR;
        
        // Buat folder upload jika belum ada
        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }
    }
    
    /**
     * Halaman daftar rekening
     */
    public function index(): void
    {
        $accounts = $this->accountModel->getAll();
        
        $this->view('admin/donation-accounts/index', [
            'title' => 'Kelola Rekening Menabur - ' . APP_NAME,
            'accounts' => $accounts
        ]);
    }
    
    /**
     * Halaman tambah rekening
     */
    public function create(): void
    {
        $this->view('admin/donation-accounts/form', [
            'title' => 'Tambah Rekening Menabur - ' . APP_NAME,
            'account' => null,
            'isEdit' => false
        ]);
    }
    
    /**
     * Simpan rekening baru
     */
    public function store(): void
    {
        if (!$this->isPost()) {
            $this->redirect('admin/rekening-menabur');
            return;
        }
        
        $this->validateCsrf();
        
        $data = [
            'bank_name' => trim($this->post('bank_name') ?? ''),
            'account_number' => trim($this->post('account_number') ?? ''),
            'account_name' => trim($this->post('account_name') ?? ''),
            'is_active' => $this->post('is_active') ? 1 : 0,
            'sort_order' => (int) ($this->post('sort_order') ?? $this->accountModel->getNextSortOrder())
        ];
        
        // Validasi
        $errors = $this->validate($data);
        if (!empty($errors)) {
            setFlash('error', implode('<br>', $errors));
            $this->redirect('admin/rekening-menabur/create');
            return;
        }
        
        // Handle logo upload
        $data['bank_logo'] = $this->handleLogoUpload();
        
        // Simpan
        $id = $this->accountModel->create($data);
        
        if ($id) {
            ActivityLog::log(auth('id'), 'create', "Menambahkan rekening {$data['bank_name']}", [], 'donation_accounts', (int)$id);
            setFlash('success', 'Rekening menabur berhasil ditambahkan!');
        } else {
            setFlash('error', 'Gagal menambahkan rekening menabur.');
        }
        
        $this->redirect('admin/rekening-menabur');
    }
    
    /**
     * Halaman edit rekening
     */
    public function edit(int $id): void
    {
        $account = $this->accountModel->getById($id);
        
        if (!$account) {
            setFlash('error', 'Rekening tidak ditemukan.');
            $this->redirect('admin/rekening-menabur');
            return;
        }
        
        $this->view('admin/donation-accounts/form', [
            'title' => 'Edit Rekening Menabur - ' . APP_NAME,
            'account' => $account,
            'isEdit' => true
        ]);
    }
    
    /**
     * Update rekening
     */
    public function update(int $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('admin/rekening-menabur');
            return;
        }
        
        $this->validateCsrf();
        
        $account = $this->accountModel->getById($id);
        if (!$account) {
            setFlash('error', 'Rekening tidak ditemukan.');
            $this->redirect('admin/rekening-menabur');
            return;
        }
        
        $data = [
            'bank_name' => trim($this->post('bank_name') ?? ''),
            'account_number' => trim($this->post('account_number') ?? ''),
            'account_name' => trim($this->post('account_name') ?? ''),
            'is_active' => $this->post('is_active') ? 1 : 0,
            'sort_order' => (int) ($this->post('sort_order') ?? 0),
            'bank_logo' => $account['bank_logo'] // Keep existing logo
        ];
        
        // Validasi
        $errors = $this->validate($data);
        if (!empty($errors)) {
            setFlash('error', implode('<br>', $errors));
            $this->redirect('admin/rekening-menabur/edit/' . $id);
            return;
        }
        
        // Handle logo upload
        $newLogo = $this->handleLogoUpload();
        if ($newLogo) {
            // Delete old logo
            if ($account['bank_logo'] && file_exists($this->uploadPath . $account['bank_logo'])) {
                unlink($this->uploadPath . $account['bank_logo']);
            }
            $data['bank_logo'] = $newLogo;
        }
        
        // Update
        if ($this->accountModel->updateAccount($id, $data)) {
            ActivityLog::log(auth('id'), 'update', "Mengubah rekening {$data['bank_name']}", [], 'donation_accounts', $id);
            setFlash('success', 'Rekening menabur berhasil diperbarui!');
        } else {
            setFlash('error', 'Gagal memperbarui rekening menabur.');
        }
        
        $this->redirect('admin/rekening-menabur');
    }
    
    /**
     * Hapus rekening
     */
    public function delete(int $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('admin/rekening-menabur');
            return;
        }
        
        $this->validateCsrf();
        
        $account = $this->accountModel->getById($id);
        if (!$account) {
            setFlash('error', 'Rekening tidak ditemukan.');
            $this->redirect('admin/rekening-menabur');
            return;
        }
        
        // Delete logo file
        if ($account['bank_logo'] && file_exists($this->uploadPath . $account['bank_logo'])) {
            unlink($this->uploadPath . $account['bank_logo']);
        }
        
        if ($this->accountModel->deleteAccount($id)) {
            ActivityLog::log(auth('id'), 'delete', "Menghapus rekening {$account['bank_name']}", [], 'donation_accounts', $id);
            setFlash('success', 'Rekening menabur berhasil dihapus!');
        } else {
            setFlash('error', 'Gagal menghapus rekening menabur.');
        }
        
        $this->redirect('admin/rekening-menabur');
    }
    
    /**
     * Toggle status aktif
     */
    public function toggle(int $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('admin/rekening-menabur');
            return;
        }
        
        $this->validateCsrf();
        
        $account = $this->accountModel->getById($id);
        if (!$account) {
            setFlash('error', 'Rekening tidak ditemukan.');
            $this->redirect('admin/rekening-menabur');
            return;
        }
        
        if ($this->accountModel->toggleActive($id)) {
            $status = $account['is_active'] ? 'dinonaktifkan' : 'diaktifkan';
            ActivityLog::log(auth('id'), 'update', "Status rekening {$account['bank_name']} {$status}", [], 'donation_accounts', $id);
            setFlash('success', "Rekening berhasil {$status}!");
        } else {
            setFlash('error', 'Gagal mengubah status rekening.');
        }
        
        $this->redirect('admin/rekening-menabur');
    }
    
    /**
     * Validasi data
     */
    private function validate(array $data): array
    {
        $errors = [];
        
        if (empty($data['bank_name'])) {
            $errors[] = 'Nama bank wajib diisi.';
        }
        
        if (empty($data['account_number'])) {
            $errors[] = 'Nomor rekening wajib diisi.';
        }
        
        if (empty($data['account_name'])) {
            $errors[] = 'Nama pemilik rekening wajib diisi.';
        }
        
        return $errors;
    }
    
    /**
     * Handle logo upload
     */
    private function handleLogoUpload(): ?string
    {
        if (!isset($_FILES['bank_logo']) || $_FILES['bank_logo']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        
        $file = $_FILES['bank_logo'];
        $maxSize = 2 * 1024 * 1024; // 2MB
        
        // Tipe & ekstensi ditentukan dari isi file, bukan dari data browser
        $ext = Security::uploadExtension($file['tmp_name']);
        if ($ext === null) {
            setFlash('warning', 'Format logo tidak valid. Gunakan JPG, PNG, GIF, atau WebP.');
            return null;
        }
        
        if ($file['size'] > $maxSize) {
            setFlash('warning', 'Ukuran logo terlalu besar. Maksimal 2MB.');
            return null;
        }
        
        $filename = 'bank_' . time() . '_' . uniqid() . '.' . $ext;
        
        if (move_uploaded_file($file['tmp_name'], $this->uploadPath . $filename)) {
            return $filename;
        }
        
        return null;
    }
    
    /**
     * Update QRIS
     */
    public function updateQris(): void
    {
        if (!$this->isPost()) {
            $this->redirect('admin/rekening-menabur');
            return;
        }
        
        $this->validateCsrf();
        
        // Check if delete QRIS
        if ($this->post('delete_qris')) {
            $currentQris = setting('donation_qris_image');
            if ($currentQris && file_exists($this->uploadPath . $currentQris)) {
                unlink($this->uploadPath . $currentQris);
            }
            \App\Models\Setting::set('donation_qris_image', '');
            \App\Models\Setting::set('donation_qris_name', '');
            ActivityLog::log(auth('id'), 'delete', "Menghapus gambar QRIS menabur", [], 'settings', null);
            setFlash('success', 'Gambar QRIS berhasil dihapus!');
            $this->redirect('admin/rekening-menabur');
            return;
        }
        
        // Handle QRIS image upload
        if (isset($_FILES['donation_qris_image']) && $_FILES['donation_qris_image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['donation_qris_image'];
            $maxSize = 2 * 1024 * 1024; // 2MB
            
            // Tipe & ekstensi ditentukan dari isi file, bukan dari data browser
            $ext = Security::uploadExtension($file['tmp_name']);
            if ($ext === null) {
                setFlash('error', 'Format gambar QRIS tidak valid. Gunakan JPG, PNG, GIF, atau WebP.');
                $this->redirect('admin/rekening-menabur');
                return;
            }
            
            if ($file['size'] > $maxSize) {
                setFlash('error', 'Ukuran gambar QRIS terlalu besar. Maksimal 2MB.');
                $this->redirect('admin/rekening-menabur');
                return;
            }
            
            // Delete old QRIS
            $currentQris = setting('donation_qris_image');
            if ($currentQris && file_exists($this->uploadPath . $currentQris)) {
                unlink($this->uploadPath . $currentQris);
            }
            
            $filename = 'qris_' . time() . '_' . uniqid() . '.' . $ext;
            
            if (move_uploaded_file($file['tmp_name'], $this->uploadPath . $filename)) {
                \App\Models\Setting::set('donation_qris_image', $filename);
            }
        }
        
        // Update QRIS name
        \App\Models\Setting::set('donation_qris_name', $this->post('donation_qris_name') ?? '');
        
        ActivityLog::log(auth('id'), 'update', "Memperbarui QRIS menabur", [], 'settings', null);
        setFlash('success', 'QRIS berhasil diperbarui!');
        $this->redirect('admin/rekening-menabur');
    }
}
