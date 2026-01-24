<?php
namespace App\Models;

use Core\Model;
use Core\Database;

/**
 * =========================================================
 * Article Category Model (Kategori Artikel)
 * =========================================================
 */
class ArticleCategory extends Model
{
    protected $table = 'article_categories';
    
    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'is_active'
    ];
    
    /**
     * Ambil semua kategori aktif
     * 
     * @return array
     */
    public function getActive(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY name ASC";
        return Database::fetchAll($sql);
    }
    
    /**
     * Ambil semua kategori dengan jumlah artikel
     * 
     * @return array
     */
    public function getAllWithCount(): array
    {
        $sql = "SELECT c.*, 
                       (SELECT COUNT(*) FROM articles a WHERE a.category_id = c.id) as article_count
                FROM {$this->table} c 
                ORDER BY c.name ASC";
        return Database::fetchAll($sql);
    }
    
    /**
     * Cari kategori berdasarkan slug
     * 
     * @param string $slug
     * @return array|null
     */
    public function findBySlug(string $slug): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE slug = :slug LIMIT 1";
        $result = Database::fetch($sql, ['slug' => $slug]);
        return $result ?: null;
    }
    
    /**
     * Generate slug unik
     * 
     * @param string $name
     * @param int|null $exceptId
     * @return string
     */
    public function generateSlug(string $name, ?int $exceptId = null): string
    {
        $slug = slugify($name);
        $originalSlug = $slug;
        $counter = 1;
        
        while ($this->slugExists($slug, $exceptId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    /**
     * Cek apakah slug sudah ada
     * 
     * @param string $slug
     * @param int|null $exceptId
     * @return bool
     */
    private function slugExists(string $slug, ?int $exceptId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE slug = :slug";
        $params = ['slug' => $slug];
        
        if ($exceptId) {
            $sql .= " AND id != :id";
            $params['id'] = $exceptId;
        }
        
        return (int) Database::fetchColumn($sql, $params) > 0;
    }
    
    /**
     * Toggle status aktif
     * 
     * @param int $id
     * @return bool
     */
    public function toggleActive(int $id): bool
    {
        $category = $this->find($id);
        if (!$category) {
            return false;
        }
        
        $newStatus = $category['is_active'] ? 0 : 1;
        return $this->update($id, ['is_active' => $newStatus]);
    }
}
