<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;

/**
 * =========================================================
 * Article Controller (Public)
 * =========================================================
 * 
 * Controller untuk halaman artikel publik
 */
class ArticleController extends Controller
{
    protected $layout = 'public';
    private $articleModel;
    private $categoryModel;
    
    public function __construct()
    {
        $this->articleModel = new Article();
        $this->categoryModel = new ArticleCategory();
    }
    
    /**
     * Daftar semua artikel
     */
    public function index(): void
    {
        $page = (int) ($this->get('page') ?? 1);
        $categorySlug = $this->get('kategori') ?? null;
        $categoryId = null;
        $selectedCategory = null;
        
        // Jika ada filter kategori
        if ($categorySlug) {
            $selectedCategory = $this->categoryModel->findBySlug($categorySlug);
            if ($selectedCategory) {
                $categoryId = (int) $selectedCategory['id'];
            }
        }
        
        $articles = $this->articleModel->getWithPagination($page, ITEMS_PER_PAGE, 'published', $categoryId);
        $categories = $this->categoryModel->getActive();
        
        $this->view('public/articles/index', [
            'title' => ($selectedCategory ? e($selectedCategory['name']) . ' - ' : '') . 'Artikel - ' . APP_NAME,
            'articles' => $articles['data'],
            'pagination' => $articles,
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'categorySlug' => $categorySlug
        ]);
    }
    
    /**
     * Baca artikel
     * 
     * @param string $param ID atau slug artikel
     */
    public function read(string $param = ''): void
    {
        if (empty($param)) {
            $this->redirect('artikel');
            return;
        }
        
        // Coba cari berdasarkan ID atau slug
        if (is_numeric($param)) {
            $article = $this->articleModel->find((int) $param);
        } else {
            $article = $this->articleModel->findBySlug($param);
        }
        
        if (!$article || $article['status'] !== 'published') {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'Tidak Ditemukan']);
            return;
        }
        
        $this->view('public/articles/read', [
            'title' => e($article['title']) . ' - ' . APP_NAME,
            'article' => $article,
        ]);
    }
}
