<?php
namespace App\Models;

use Core\Model;
use Core\Database;

/**
 * =========================================================
 * Event Model (Kegiatan)
 * =========================================================
 */
class Event extends Model
{
    protected $table = 'events';
    
    protected $fillable = [
        'title',
        'slug',
        'description',
        'image',
        'event_date',
        'event_time',
        'location',
        'status',
        'created_by'
    ];
    
    /**
     * Ambil event yang akan datang
     * 
     * @param int $limit
     * @return array
     */
    public function getUpcoming(int $limit = 5): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE event_date >= CURDATE() AND status = 'published'
                ORDER BY event_date ASC, event_time ASC 
                LIMIT :limit";
        
        $stmt = Database::getInstance()->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Ambil semua event dengan pagination
     * 
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getWithPagination(int $page = 1, int $perPage = ITEMS_PER_PAGE): array
    {
        return $this->paginate($page, $perPage, 'event_date', 'DESC');
    }
    
    /**
     * Cari event berdasarkan slug
     * 
     * @param string $slug
     * @return array|false
     */
    public function findBySlug(string $slug)
    {
        return $this->findBy('slug', $slug);
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
     * Ambil event berdasarkan bulan
     * 
     * @param int $month
     * @param int $year
     * @return array
     */
    public function getByMonth(int $month, int $year): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE MONTH(event_date) = :month 
                AND YEAR(event_date) = :year 
                AND status = 'published'
                ORDER BY event_date ASC";
        
        return Database::fetchAll($sql, ['month' => $month, 'year' => $year]);
    }
}
