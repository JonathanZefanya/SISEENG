<?php
namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\ArticleCategory;
use App\Middleware\RoleMiddleware;

/**
 * =========================================================
 * Article Category Controller (Admin)
 * =========================================================
 * 
 * Mengelola kategori artikel
 */
class ArticleCategoryController extends Controller
{
    protected $layout = 'admin';
    private $categoryModel;
    
    public function __construct()
    {
        RoleMiddleware::requireAdmin();
        $this->categoryModel = new ArticleCategory();
    }
    
    /**
     * Halaman daftar kategori
     */
    public function index(): void
    {
        $categories = $this->categoryModel->getAllWithCount();
        
        $this->view('admin/article-categories/index', [
            'title' => 'Kategori Artikel - ' . APP_NAME,
            'categories' => $categories
        ]);
    }
    
    /**
     * Form tambah kategori
     */
    public function create(): void
    {
        $this->view('admin/article-categories/form', [
            'title' => 'Tambah Kategori - ' . APP_NAME,
            'category' => null,
            'isEdit' => false
        ]);
    }
    
    /**
     * Simpan kategori baru
     */
    public function store(): void
    {
        if (!$this->isPost()) {
            $this->redirect('admin/kategori-artikel');
            return;
        }
        
        $this->validateCsrf();
        
        $name = trim($this->post('name'));
        
        if (empty($name)) {
            setFlash('error', 'Nama kategori wajib diisi.');
            $this->redirect('admin/kategori-artikel/create');
            return;
        }
        
        $data = [
            'name' => $name,
            'slug' => $this->categoryModel->generateSlug($name),
            'description' => $this->post('description') ?: null,
            'color' => $this->post('color') ?: '#6c757d',
            'is_active' => $this->post('is_active') ? 1 : 0
        ];
        
        if ($this->categoryModel->create($data)) {
            setFlash('success', 'Kategori berhasil ditambahkan!');
        } else {
            setFlash('error', 'Gagal menambahkan kategori.');
        }
        
        $this->redirect('admin/kategori-artikel');
    }
    
    /**
     * Form edit kategori
     */
    public function edit(int $id): void
    {
        $category = $this->categoryModel->find($id);
        
        if (!$category) {
            setFlash('error', 'Kategori tidak ditemukan.');
            $this->redirect('admin/kategori-artikel');
            return;
        }
        
        $this->view('admin/article-categories/form', [
            'title' => 'Edit Kategori - ' . APP_NAME,
            'category' => $category,
            'isEdit' => true
        ]);
    }
    
    /**
     * Update kategori
     */
    public function update(int $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('admin/kategori-artikel');
            return;
        }
        
        $this->validateCsrf();
        
        $category = $this->categoryModel->find($id);
        if (!$category) {
            setFlash('error', 'Kategori tidak ditemukan.');
            $this->redirect('admin/kategori-artikel');
            return;
        }
        
        $name = trim($this->post('name'));
        
        if (empty($name)) {
            setFlash('error', 'Nama kategori wajib diisi.');
            $this->redirect("admin/kategori-artikel/edit/{$id}");
            return;
        }
        
        $data = [
            'name' => $name,
            'slug' => $this->categoryModel->generateSlug($name, $id),
            'description' => $this->post('description') ?: null,
            'color' => $this->post('color') ?: '#6c757d',
            'is_active' => $this->post('is_active') ? 1 : 0
        ];
        
        if ($this->categoryModel->update($id, $data)) {
            setFlash('success', 'Kategori berhasil diperbarui!');
        } else {
            setFlash('error', 'Gagal memperbarui kategori.');
        }
        
        $this->redirect('admin/kategori-artikel');
    }
    
    /**
     * Hapus kategori
     */
    public function delete(int $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('admin/kategori-artikel');
            return;
        }
        
        $this->validateCsrf();
        
        if ($this->categoryModel->delete($id)) {
            setFlash('success', 'Kategori berhasil dihapus!');
        } else {
            setFlash('error', 'Gagal menghapus kategori.');
        }
        
        $this->redirect('admin/kategori-artikel');
    }
    
    /**
     * Toggle status aktif
     */
    public function toggleStatus(int $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('admin/kategori-artikel');
            return;
        }
        
        $this->validateCsrf();
        
        if ($this->categoryModel->toggleActive($id)) {
            setFlash('success', 'Status kategori berhasil diubah!');
        } else {
            setFlash('error', 'Gagal mengubah status kategori.');
        }
        
        $this->redirect('admin/kategori-artikel');
    }
}
