<?php
namespace App\Models;

use Core\Model;
use Core\Database;

/**
 * =========================================================
 * Activity Log Model (Log Aktivitas)
 * =========================================================
 */
class ActivityLog extends Model
{
    protected $table = 'activity_logs';
    
    protected $fillable = [
        'user_id',
        'user_name',
        'action',
        'description',
        'module',
        'record_id',
        'old_data',
        'new_data',
        'ip_address',
        'user_agent'
    ];
    
    /**
     * Override hasColumn - activity_logs hanya punya created_at, tidak ada updated_at
     */
    protected function hasColumn(string $column): bool
    {
        return $column === 'created_at';
    }
    
    /**
     * Catat aktivitas
     * 
     * @param int $userId
     * @param string $action
     * @param string $description
     * @param array $data
     */
    public static function log(int $userId, string $action, string $description, array $data = [], ?string $module = null, ?int $recordId = null): void
    {
        $instance = new self();
        $instance->create([
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'module' => $module,
            'record_id' => $recordId,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'new_data' => !empty($data) ? json_encode($data) : null
        ]);
    }
    
    /**
     * Ambil log dengan pagination dan join user
     * 
     * @param int $page
     * @param int $perPage
     * @param int|null $userId Filter berdasarkan user
     * @return array
     */
    public function getWithPagination(int $page = 1, int $perPage = ITEMS_PER_PAGE, ?int $userId = null): array
    {
        $offset = ($page - 1) * $perPage;
        $params = [];
        $whereClause = '';
        
        if ($userId !== null) {
            $whereClause = 'WHERE al.user_id = :user_id';
            $params['user_id'] = $userId;
        }
        
        // Count total
        $countSql = "SELECT COUNT(*) FROM {$this->table} al {$whereClause}";
        $total = (int) Database::fetchColumn($countSql, $params);
        
        // Get data
        $sql = "SELECT al.*, u.name as user_name, u.email as user_email 
                FROM {$this->table} al 
                LEFT JOIN users u ON al.user_id = u.id 
                {$whereClause}
                ORDER BY al.created_at DESC 
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
     * Ambil log terbaru
     * 
     * @param int $limit
     * @return array
     */
    public function getRecent(int $limit = 10): array
    {
        $sql = "SELECT al.*, u.name as user_name 
                FROM {$this->table} al 
                LEFT JOIN users u ON al.user_id = u.id 
                ORDER BY al.created_at DESC 
                LIMIT :limit";
        
        $stmt = Database::getInstance()->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Hapus log yang lebih dari X hari
     * 
     * @param int $days
     * @return int
     */
    public function deleteOlderThan(int $days): int
    {
        $sql = "DELETE FROM {$this->table} WHERE created_at < DATE_SUB(NOW(), INTERVAL :days DAY)";
        return Database::query($sql, ['days' => $days])->rowCount();
    }
}
