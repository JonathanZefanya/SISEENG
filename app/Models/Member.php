<?php
namespace App\Models;

use Core\Model;
use Core\Database;

/**
 * =========================================================
 * Member Model (Data Jemaat)
 * =========================================================
 */
class Member extends Model
{
    protected $table = 'members';
    
    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'address',
        'birth_date',
        'birth_place',
        'gender',
        'baptism_date',
        'membership_date',
        'status',
        'notes',
        'created_by'
    ];
    
    /**
     * Ambil jemaat aktif
     * 
     * @return array
     */
    public function getActive(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE status = 'active' ORDER BY full_name ASC";
        return Database::fetchAll($sql);
    }
    
    /**
     * Cari jemaat
     * 
     * @param string $keyword
     * @return array
     */
    public function search(string $keyword): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE full_name LIKE :keyword 
                   OR email LIKE :keyword 
                   OR phone LIKE :keyword 
                ORDER BY full_name ASC";
        
        return Database::fetchAll($sql, ['keyword' => '%' . $keyword . '%']);
    }
    
    /**
     * Ambil jemaat dengan pagination
     * 
     * @param int $page
     * @param int $perPage
     * @param string|null $search
     * @param string|null $status
     * @param string|null $gender
     * @return array
     */
    public function getWithPagination(int $page = 1, int $perPage = ITEMS_PER_PAGE, ?string $search = null, ?string $status = null, ?string $gender = null): array
    {
        $offset = ($page - 1) * $perPage;
        $params = [];
        $conditions = [];
        
        if ($search) {
            $conditions[] = '(full_name LIKE :search1 OR email LIKE :search2 OR phone LIKE :search3)';
            $searchValue = '%' . $search . '%';
            $params['search1'] = $searchValue;
            $params['search2'] = $searchValue;
            $params['search3'] = $searchValue;
        }
        
        if ($status) {
            $conditions[] = 'status = :status';
            $params['status'] = $status;
        }
        
        if ($gender) {
            $conditions[] = 'gender = :gender';
            $params['gender'] = $gender;
        }
        
        $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
        
        // Count total
        $countSql = "SELECT COUNT(*) FROM {$this->table} {$whereClause}";
        $total = (int) Database::fetchColumn($countSql, $params);
        
        // Get data
        $sql = "SELECT * FROM {$this->table} {$whereClause} ORDER BY full_name ASC LIMIT :limit OFFSET :offset";
        
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
     * Hitung jemaat berdasarkan gender
     * 
     * @param string $gender
     * @return int
     */
    public function countByGender(string $gender): int
    {
        return $this->count('gender', $gender);
    }
    
    /**
     * Hitung total jemaat aktif
     * 
     * @return int
     */
    public function countActive(): int
    {
        return $this->count('status', 'active');
    }
    
    /**
     * Statistik jemaat
     * 
     * @return array
     */
    public function getStatistics(): array
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
                    SUM(CASE WHEN gender = 'male' THEN 1 ELSE 0 END) as male,
                    SUM(CASE WHEN gender = 'female' THEN 1 ELSE 0 END) as female,
                    SUM(CASE WHEN YEAR(join_date) = YEAR(NOW()) THEN 1 ELSE 0 END) as new_this_year
                FROM {$this->table}";
        
        return Database::fetch($sql);
    }
}
