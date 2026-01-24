<?php
namespace App\Models;

use Core\Model;
use Core\Database;

/**
 * =========================================================
 * Contact Message Model
 * =========================================================
 */
class ContactMessage extends Model
{
    protected $table = 'contact_messages';
    
    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'is_read',
        'ip_address'
    ];
    
    /**
     * Override hasColumn - contact_messages hanya punya created_at, tidak ada updated_at
     */
    protected function hasColumn(string $column): bool
    {
        return $column === 'created_at';
    }
    
    /**
     * Ambil pesan yang belum dibaca
     * 
     * @return array
     */
    public function getUnread(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_read = 0 ORDER BY created_at DESC";
        return Database::fetchAll($sql);
    }
    
    /**
     * Hitung pesan yang belum dibaca
     * 
     * @return int
     */
    public function countUnread(): int
    {
        return $this->count('is_read', 0);
    }
    
    /**
     * Tandai pesan sebagai sudah dibaca
     * 
     * @param int $id
     * @return bool
     */
    public function markAsRead(int $id): bool
    {
        $sql = "UPDATE {$this->table} SET is_read = 1, read_at = NOW() WHERE id = :id";
        return Database::query($sql, ['id' => $id])->rowCount() > 0;
    }
    
    /**
     * Ambil pesan dengan pagination
     * 
     * @param int $page
     * @param int $perPage
     * @param bool|null $isRead Filter status baca
     * @return array
     */
    public function getWithPagination(int $page = 1, int $perPage = ITEMS_PER_PAGE, ?bool $isRead = null): array
    {
        $offset = ($page - 1) * $perPage;
        $params = [];
        $whereClause = '';
        
        if ($isRead !== null) {
            $whereClause = 'WHERE is_read = :is_read';
            $params['is_read'] = $isRead ? 1 : 0;
        }
        
        // Count total
        $countSql = "SELECT COUNT(*) FROM {$this->table} {$whereClause}";
        $total = (int) Database::fetchColumn($countSql, $params);
        
        // Get data
        $sql = "SELECT * FROM {$this->table} {$whereClause} ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        
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
}
