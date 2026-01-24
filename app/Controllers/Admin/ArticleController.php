<?php
namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\ActivityLog;
use App\Middleware\RoleMiddleware;

/**
 * =========================================================
 * Admin Article Controller
 * =========================================================
 * 
 * Controller untuk mengelola artikel/berita
 */
class ArticleController extends Controller
{
    protected $layout = 'admin';
    private $articleModel;
    private $categoryModel;
    
    public function __construct()
    {
        RoleMiddleware::requireAdmin();
        $this->articleModel = new Article();
        $this->categoryModel = new ArticleCategory();
    }
    
    /**
     * Daftar artikel
     */
    public function index(): void
    {
        $page = (int) ($this->get('page') ?? 1);
        $categoryId = $this->get('category') ? (int) $this->get('category') : null;
        
        $articles = $this->articleModel->getWithPagination($page, ITEMS_PER_PAGE, null, $categoryId);
        $categories = $this->categoryModel->getActive();
        
        $this->view('admin/articles/index', [
            'title' => 'Kelola Artikel - ' . APP_NAME,
            'articles' => $articles['data'],
            'pagination' => $articles,
            'categories' => $categories,
            'selectedCategory' => $categoryId
        ]);
    }
    
    /**
     * Form tambah artikel
     */
    public function create(): void
    {
        $categories = $this->categoryModel->getActive();
        
        $this->view('admin/articles/create', [
            'title' => 'Tambah Artikel - ' . APP_NAME,
            'categories' => $categories
        ]);
    }
    
    /**
     * Simpan artikel baru
     */
    public function store(): void
    {
        if (!$this->isPost()) {
            $this->redirect('admin/artikel');
            return;
        }
        
        $this->validateCsrf();
        
        $title = $this->post('title');
        
        $data = [
            'title' => $title,
            'slug' => $this->articleModel->generateSlug($title),
            'content' => $this->post('content'),
            'excerpt' => $this->post('excerpt'),
            'author_id' => auth('id'),
            'category_id' => $this->post('category_id') ?: null,
            'status' => $this->post('status'),
            'published_at' => $this->post('status') === 'published' ? date('Y-m-d H:i:s') : null,
        ];
        
        // Validasi
        $errors = $this->validateArticle($data);
        
        if (!empty($errors)) {
            setFlash('error', implode('<br>', $errors));
            $this->saveOldInput();
            $this->redirect('admin/artikel/tambah');
            return;
        }
        
        // Handle upload gambar
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imagePath = $this->uploadImage($_FILES['image']);
            if ($imagePath) {
                $data['image'] = $imagePath;
            }
        }
        
        try {
            $id = $this->articleModel->create($data);
            
            ActivityLog::log(auth('id'), 'create_article', "Menambah artikel: {$data['title']}");
            
            setFlash('success', 'Artikel berhasil ditambahkan.');
            $this->clearOldInput();
        } catch (\Exception $e) {
            setFlash('error', 'Gagal menambahkan artikel.');
            $this->saveOldInput();
            $this->redirect('admin/artikel/tambah');
            return;
        }
        
        $this->redirect('admin/artikel');
    }
    
    /**
     * Form edit artikel
     */
    public function edit(int $id = 0): void
    {
        $article = $this->articleModel->find($id);
        
        if (!$article) {
            setFlash('error', 'Artikel tidak ditemukan.');
            $this->redirect('admin/artikel');
            return;
        }
        
        $categories = $this->categoryModel->getActive();
        
        $this->view('admin/articles/edit', [
            'title' => 'Edit Artikel - ' . APP_NAME,
            'article' => $article,
            'categories' => $categories
        ]);
    }
    
    /**
     * Update artikel
     */
    public function update(): void
    {
        if (!$this->isPost()) {
            $this->redirect('admin/artikel');
            return;
        }
        
        $this->validateCsrf();
        
        $id = (int) $this->post('id');
        $article = $this->articleModel->find($id);
        
        if (!$article) {
            setFlash('error', 'Artikel tidak ditemukan.');
            $this->redirect('admin/artikel');
            return;
        }
        
        $title = $this->post('title');
        
        $data = [
            'title' => $title,
            'slug' => $this->articleModel->generateSlug($title, $id),
            'content' => $this->post('content'),
            'excerpt' => $this->post('excerpt'),
            'category_id' => $this->post('category_id') ?: null,
            'status' => $this->post('status'),
        ];
        
        // Update published_at jika status berubah ke published
        if ($data['status'] === 'published' && $article['status'] !== 'published') {
            $data['published_at'] = date('Y-m-d H:i:s');
        }
        
        // Validasi
        $errors = $this->validateArticle($data);
        
        if (!empty($errors)) {
            setFlash('error', implode('<br>', $errors));
            $this->saveOldInput();
            $this->redirect('admin/artikel/edit/' . $id);
            return;
        }
        
        // Handle upload gambar
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imagePath = $this->uploadImage($_FILES['image']);
            if ($imagePath) {
                // Hapus gambar lama
                if ($article['image'] && file_exists(PUBLIC_PATH . $article['image'])) {
                    unlink(PUBLIC_PATH . $article['image']);
                }
                $data['image'] = $imagePath;
            }
        }
        
        try {
            $this->articleModel->update($id, $data);
            
            ActivityLog::log(auth('id'), 'update_article', "Mengupdate artikel: {$data['title']}");
            
            setFlash('success', 'Artikel berhasil diupdate.');
            $this->clearOldInput();
        } catch (\Exception $e) {
            setFlash('error', 'Gagal mengupdate artikel.');
            $this->saveOldInput();
            $this->redirect('admin/artikel/edit/' . $id);
            return;
        }
        
        $this->redirect('admin/artikel');
    }
    
    /**
     * Hapus artikel
     */
    public function delete(int $id = 0): void
    {
        if (!$this->isPost()) {
            $this->redirect('admin/artikel');
            return;
        }
        
        $this->validateCsrf();
        
        $article = $this->articleModel->find($id);
        
        if (!$article) {
            setFlash('error', 'Artikel tidak ditemukan.');
            $this->redirect('admin/artikel');
            return;
        }
        
        try {
            // Hapus gambar
            if ($article['image'] && file_exists(PUBLIC_PATH . $article['image'])) {
                unlink(PUBLIC_PATH . $article['image']);
            }
            
            $this->articleModel->delete($id);
            
            ActivityLog::log(auth('id'), 'delete_article', "Menghapus artikel: {$article['title']}");
            
            setFlash('success', 'Artikel berhasil dihapus.');
        } catch (\Exception $e) {
            setFlash('error', 'Gagal menghapus artikel.');
        }
        
        $this->redirect('admin/artikel');
    }
    
    /**
     * Upload gambar
     */
    private function uploadImage(array $file): ?string
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($file['type'], $allowedTypes)) {
            setFlash('warning', 'Format gambar tidak didukung.');
            return null;
        }
        
        if ($file['size'] > $maxSize) {
            setFlash('warning', 'Ukuran gambar terlalu besar (maks 5MB).');
            return null;
        }
        
        $uploadDir = 'uploads/articles/';
        $fullPath = PUBLIC_PATH . $uploadDir;
        
        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0755, true);
        }
        
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('article_') . '.' . $extension;
        
        if (move_uploaded_file($file['tmp_name'], $fullPath . $filename)) {
            return $uploadDir . $filename;
        }
        
        return null;
    }
    
    /**
     * Validasi data artikel
     */
    private function validateArticle(array $data): array
    {
        $errors = [];
        
        if (empty($data['title'])) {
            $errors[] = 'Judul artikel wajib diisi.';
        }
        
        if (empty($data['content'])) {
            $errors[] = 'Konten artikel wajib diisi.';
        }
        
        if (!in_array($data['status'], ['draft', 'published'])) {
            $errors[] = 'Status tidak valid.';
        }
        
        return $errors;
    }
}
