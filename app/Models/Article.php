<?php
namespace App\Models;

use Core\Model;
use Core\Database;

/**
 * =========================================================
 * Article Model (Artikel/Berita)
 * =========================================================
 */
class Article extends Model
{
    protected $table = 'articles';
    
    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'image',
        'author_id',
        'category_id',
        'status',
        'published_at'
    ];
    
    /**
     * Ambil artikel yang dipublikasikan
     * 
     * @param int $limit
     * @return array
     */
    public function getPublished(int $limit = 10): array
    {
        $sql = "SELECT a.*, u.name as author_name, c.name as category_name, c.slug as category_slug, c.color as category_color
                FROM {$this->table} a 
                LEFT JOIN users u ON a.author_id = u.id 
                LEFT JOIN article_categories c ON a.category_id = c.id
                WHERE a.status = 'published' AND a.published_at <= NOW()
                ORDER BY a.published_at DESC 
                LIMIT :limit";
        
        $stmt = Database::getInstance()->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Ambil artikel dengan pagination
     * 
     * @param int $page
     * @param int $perPage
     * @param string|null $status Filter status
     * @param int|null $categoryId Filter kategori
     * @return array
     */
    public function getWithPagination(int $page = 1, int $perPage = ITEMS_PER_PAGE, ?string $status = null, ?int $categoryId = null): array
    {
        $offset = ($page - 1) * $perPage;
        $params = [];
        $conditions = [];
        
        if ($status) {
            $conditions[] = 'a.status = :status';
            $params['status'] = $status;
        }
        
        if ($categoryId) {
            $conditions[] = 'a.category_id = :category_id';
            $params['category_id'] = $categoryId;
        }
        
        $whereClause = count($conditions) > 0 ? 'WHERE ' . implode(' AND ', $conditions) : '';
        
        // Count total
        $countSql = "SELECT COUNT(*) FROM {$this->table} a {$whereClause}";
        $total = (int) Database::fetchColumn($countSql, $params);
        
        // Get data
        $sql = "SELECT a.*, u.name as author_name, c.name as category_name, c.slug as category_slug, c.color as category_color
                FROM {$this->table} a 
                LEFT JOIN users u ON a.author_id = u.id 
                LEFT JOIN article_categories c ON a.category_id = c.id
                {$whereClause}
                ORDER BY a.created_at DESC 
                LIMIT :limit OFFSET :offset";
        
        $stmt = Database::getInstance()->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll();
        
        return [
            'data' => $data,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage)
        ];
    }
    
    /**
     * Cari artikel berdasarkan slug
     * 
     * @param string $slug
     * @return array|false
     */
    public function findBySlug(string $slug)
    {
        $sql = "SELECT a.*, u.name as author_name, c.name as category_name, c.slug as category_slug, c.color as category_color
                FROM {$this->table} a 
                LEFT JOIN users u ON a.author_id = u.id 
                LEFT JOIN article_categories c ON a.category_id = c.id
                WHERE a.slug = :slug 
                LIMIT 1";
        return Database::fetch($sql, ['slug' => $slug]);
    }
    
    /**
     * Ambil artikel berdasarkan kategori
     * 
     * @param int $categoryId
     * @param int $limit
     * @return array
     */
    public function getByCategory(int $categoryId, int $limit = 10): array
    {
        $sql = "SELECT a.*, u.name as author_name, c.name as category_name, c.slug as category_slug, c.color as category_color
                FROM {$this->table} a 
                LEFT JOIN users u ON a.author_id = u.id 
                LEFT JOIN article_categories c ON a.category_id = c.id
                WHERE a.category_id = :category_id AND a.status = 'published' AND a.published_at <= NOW()
                ORDER BY a.published_at DESC 
                LIMIT :limit";
        
        $stmt = Database::getInstance()->prepare($sql);
        $stmt->bindValue(':category_id', $categoryId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Generate slug unik
     * 
     * @param string $title
     * @param int|null $exceptId
     * @return string
     */
    public function generateSlug(string $title, ?int $exceptId = null): string
    {
        $slug = slugify($title);
        $originalSlug = $slug;
        $counter = 1;
        
        while ($this->exists('slug', $slug, $exceptId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    /**
     * Hitung artikel berdasarkan status
     * 
     * @param string $status
     * @return int
     */
    public function countByStatus(string $status): int
    {
        return $this->count('status', $status);
    }
}
