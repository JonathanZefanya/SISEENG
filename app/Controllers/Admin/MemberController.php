<?php
namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\Member;
use App\Models\ActivityLog;
use App\Middleware\RoleMiddleware;
use Core\Security;

/**
 * =========================================================
 * Admin Member Controller
 * =========================================================
 * 
 * Controller untuk mengelola data jemaat
 */
class MemberController extends Controller
{
    protected $layout = 'admin';
    private $memberModel;
    
    public function __construct()
    {
        RoleMiddleware::requireAdmin();
        $this->memberModel = new Member();
    }
    
    /**
     * Daftar jemaat
     */
    public function index(): void
    {
        $page = (int) ($this->get('page') ?? 1);
        $search = $this->get('search');
        $status = $this->get('status');
        $gender = $this->get('gender');
        
        $members = $this->memberModel->getWithPagination($page, ITEMS_PER_PAGE, $search, $status, $gender);
        
        $this->view('admin/members/index', [
            'title' => 'Data Jemaat - ' . APP_NAME,
            'members' => $members['data'],
            'pagination' => $members,
            'search' => $search,
            'status' => $status,
            'gender' => $gender,
        ]);
    }
    
    /**
     * Detail jemaat
     */
    public function show(int $id = 0): void
    {
        $member = $this->memberModel->find($id);
        
        if (!$member) {
            setFlash('error', 'Data jemaat tidak ditemukan.');
            $this->redirect('admin/jemaat');
            return;
        }
        
        $this->view('admin/members/show', [
            'title' => 'Detail Jemaat - ' . APP_NAME,
            'member' => $member,
        ]);
    }
    
    /**
     * Form tambah jemaat
     */
    public function create(): void
    {
        $this->view('admin/members/create', [
            'title' => 'Tambah Jemaat - ' . APP_NAME,
        ]);
    }
    
    /**
     * Simpan jemaat baru
     */
    public function store(): void
    {
        if (!$this->isPost()) {
            $this->redirect('admin/jemaat');
            return;
        }
        
        $this->validateCsrf();
        
        $data = [
            'full_name' => $this->post('full_name'),
            'email' => $this->post('email'),
            'phone' => $this->post('phone'),
            'address' => $this->post('address'),
            'birth_date' => $this->post('birth_date') ?: null,
            'birth_place' => $this->post('birth_place'),
            'gender' => $this->post('gender'),
            'baptism_date' => $this->post('baptism_date') ?: null,
            'membership_date' => $this->post('membership_date') ?: date('Y-m-d'),
            'status' => 'active',
            'notes' => $this->post('notes'),
            'created_by' => auth('id'),
        ];
        
        // Validasi
        $errors = $this->validateMember($data);
        
        if (!empty($errors)) {
            setFlash('error', implode('<br>', $errors));
            $this->saveOldInput();
            $this->redirect('admin/jemaat/tambah');
            return;
        }
        
        try {
            $id = $this->memberModel->create($data);
            
            ActivityLog::log(auth('id'), 'create_member', "Menambah jemaat: {$data['full_name']}");
            
            setFlash('success', 'Data jemaat berhasil ditambahkan.');
            $this->clearOldInput();
        } catch (\Exception $e) {
            setFlash('error', 'Gagal menambahkan data jemaat.');
            $this->saveOldInput();
            $this->redirect('admin/jemaat/tambah');
            return;
        }
        
        $this->redirect('admin/jemaat');
    }
    
    /**
     * Form edit jemaat
     */
    public function edit(int $id = 0): void
    {
        $member = $this->memberModel->find($id);
        
        if (!$member) {
            setFlash('error', 'Data jemaat tidak ditemukan.');
            $this->redirect('admin/jemaat');
            return;
        }
        
        $this->view('admin/members/edit', [
            'title' => 'Edit Jemaat - ' . APP_NAME,
            'member' => $member,
        ]);
    }
    
    /**
     * Update jemaat
     */
    public function update(): void
    {
        if (!$this->isPost()) {
            $this->redirect('admin/jemaat');
            return;
        }
        
        $this->validateCsrf();
        
        $id = (int) $this->post('id');
        
        if (!$id || !$this->memberModel->find($id)) {
            setFlash('error', 'Data jemaat tidak ditemukan.');
            $this->redirect('admin/jemaat');
            return;
        }
        
        $data = [
            'full_name' => $this->post('full_name'),
            'email' => $this->post('email'),
            'phone' => $this->post('phone'),
            'address' => $this->post('address'),
            'birth_date' => $this->post('birth_date') ?: null,
            'birth_place' => $this->post('birth_place'),
            'gender' => $this->post('gender'),
            'baptism_date' => $this->post('baptism_date') ?: null,
            'membership_date' => $this->post('membership_date') ?: null,
            'status' => $this->post('status'),
            'notes' => $this->post('notes'),
        ];
        
        // Validasi
        $errors = $this->validateMember($data);
        
        if (!empty($errors)) {
            setFlash('error', implode('<br>', $errors));
            $this->saveOldInput();
            $this->redirect('admin/jemaat/edit/' . $id);
            return;
        }
        
        try {
            $this->memberModel->update($id, $data);
            
            ActivityLog::log(auth('id'), 'update_member', "Mengupdate jemaat: {$data['full_name']}");
            
            setFlash('success', 'Data jemaat berhasil diupdate.');
            $this->clearOldInput();
        } catch (\Exception $e) {
            setFlash('error', 'Gagal mengupdate data jemaat.');
            $this->saveOldInput();
            $this->redirect('admin/jemaat/edit/' . $id);
            return;
        }
        
        $this->redirect('admin/jemaat');
    }
    
    /**
     * Hapus jemaat
     */
    public function delete(int $id = 0): void
    {
        if (!$this->isPost()) {
            $this->redirect('admin/jemaat');
            return;
        }
        
        $this->validateCsrf();
        
        $member = $this->memberModel->find($id);
        
        if (!$member) {
            setFlash('error', 'Data jemaat tidak ditemukan.');
            $this->redirect('admin/jemaat');
            return;
        }
        
        try {
            $this->memberModel->delete($id);
            
            ActivityLog::log(auth('id'), 'delete_member', "Menghapus jemaat: {$member['full_name']}");
            
            setFlash('success', 'Data jemaat berhasil dihapus.');
        } catch (\Exception $e) {
            setFlash('error', 'Gagal menghapus data jemaat.');
        }
        
        $this->redirect('admin/jemaat');
    }
    
    /**
     * Validasi data jemaat
     */
    private function validateMember(array $data): array
    {
        $errors = [];
        
        if (empty($data['full_name'])) {
            $errors[] = 'Nama jemaat wajib diisi.';
        }
        
        if (!empty($data['email']) && !Security::validateEmail($data['email'])) {
            $errors[] = 'Format email tidak valid.';
        }
        
        if (!in_array($data['gender'], ['M', 'F', ''])) {
            $errors[] = 'Gender tidak valid.';
        }
        
        return $errors;
    }
}
