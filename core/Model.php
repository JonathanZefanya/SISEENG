<?php
namespace Core;

/**
 * =========================================================
 * Base Model Class
 * =========================================================
 * 
 * Model dasar yang diextend oleh semua model
 * Menyediakan method CRUD dasar dengan prepared statements
 */
abstract class Model
{
    /**
     * Nama tabel
     * @var string
     */
    protected $table;
    
    /**
     * Primary key
     * @var string
     */
    protected $primaryKey = 'id';
    
    /**
     * Kolom yang bisa diisi (mass assignment protection)
     * @var array
     */
    protected $fillable = [];
    
    /**
     * Instance database
     * @var \PDO
     */
    protected $db;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Ambil semua data
     * 
     * @param string $orderBy Kolom untuk sorting
     * @param string $order ASC atau DESC
     * @return array
     */
    public function all(string $orderBy = 'id', string $order = 'DESC'): array
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY {$orderBy} {$order}";
        return Database::fetchAll($sql);
    }
    
    /**
     * Ambil data dengan pagination
     * 
     * @param int $page Halaman saat ini
     * @param int $perPage Jumlah per halaman
     * @param string $orderBy Kolom untuk sorting
     * @param string $order ASC atau DESC
     * @return array
     */
    public function paginate(int $page = 1, int $perPage = ITEMS_PER_PAGE, string $orderBy = 'id', string $order = 'DESC'): array
    {
        $offset = ($page - 1) * $perPage;
        
        // Hitung total
        $total = $this->count();
        
        // Ambil data
        $sql = "SELECT * FROM {$this->table} ORDER BY {$orderBy} {$order} LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll();
        
        return [
            'data' => $data,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total)
        ];
    }
    
    /**
     * Cari data berdasarkan ID
     * 
     * @param int $id
     * @return array|false
     */
    public function find(int $id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
        return Database::fetch($sql, ['id' => $id]);
    }
    
    /**
     * Cari data berdasarkan kondisi
     * 
     * @param string $column
     * @param mixed $value
     * @return array|false
     */
    public function findBy(string $column, $value)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = :value LIMIT 1";
        return Database::fetch($sql, ['value' => $value]);
    }
    
    /**
     * Cari semua data berdasarkan kondisi
     * 
     * @param string $column
     * @param mixed $value
     * @return array
     */
    public function findAllBy(string $column, $value): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = :value";
        return Database::fetchAll($sql, ['value' => $value]);
    }
    
    /**
     * Cari data dengan multiple kondisi
     * 
     * @param array $conditions Array ['column' => 'value']
     * @return array|false
     */
    public function findWhere(array $conditions)
    {
        $whereClause = [];
        $params = [];
        
        foreach ($conditions as $column => $value) {
            $whereClause[] = "{$column} = :{$column}";
            $params[$column] = $value;
        }
        
        $sql = "SELECT * FROM {$this->table} WHERE " . implode(' AND ', $whereClause) . " LIMIT 1";
        return Database::fetch($sql, $params);
    }
    
    /**
     * Insert data baru
     * 
     * @param array $data
     * @return int|string Last insert ID
     */
    public function create(array $data)
    {
        // Filter hanya kolom yang boleh diisi
        $data = $this->filterFillable($data);
        
        // Tambahkan timestamp jika ada
        if ($this->hasColumn('created_at')) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }
        if ($this->hasColumn('updated_at')) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        return Database::insert($this->table, $data);
    }
    
    /**
     * Update data
     * 
     * @param int $id
     * @param array $data
     * @return int Jumlah baris yang diupdate
     */
    public function update(int $id, array $data): int
    {
        // Filter hanya kolom yang boleh diisi
        $data = $this->filterFillable($data);
        
        // Tambahkan timestamp jika ada
        if ($this->hasColumn('updated_at')) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        return Database::update($this->table, $data, "{$this->primaryKey} = :pk_id", ['pk_id' => $id]);
    }
    
    /**
     * Delete data
     * 
     * @param int $id
     * @return int Jumlah baris yang dihapus
     */
    public function delete(int $id): int
    {
        return Database::delete($this->table, "{$this->primaryKey} = :id", ['id' => $id]);
    }
    
    /**
     * Soft delete (set deleted_at)
     * 
     * @param int $id
     * @return int
     */
    public function softDelete(int $id): int
    {
        $sql = "UPDATE {$this->table} SET deleted_at = :deleted_at WHERE {$this->primaryKey} = :id";
        return Database::query($sql, [
            'deleted_at' => date('Y-m-d H:i:s'),
            'id' => $id
        ])->rowCount();
    }
    
    /**
     * Hitung total data
     * 
     * @param string|null $column
     * @param mixed $value
     * @return int
     */
    public function count(?string $column = null, $value = null): int
    {
        if ($column && $value !== null) {
            $sql = "SELECT COUNT(*) FROM {$this->table} WHERE {$column} = :value";
            return (int) Database::fetchColumn($sql, ['value' => $value]);
        }
        
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        return (int) Database::fetchColumn($sql);
    }
    
    /**
     * Cek apakah data exists
     * 
     * @param string $column
     * @param mixed $value
     * @param int|null $exceptId Kecuali ID ini
     * @return bool
     */
    public function exists(string $column, $value, ?int $exceptId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE {$column} = :value";
        $params = ['value' => $value];
        
        if ($exceptId !== null) {
            $sql .= " AND {$this->primaryKey} != :except_id";
            $params['except_id'] = $exceptId;
        }
        
        return (int) Database::fetchColumn($sql, $params) > 0;
    }
    
    /**
     * Filter data hanya kolom yang ada di fillable
     * 
     * @param array $data
     * @return array
     */
    protected function filterFillable(array $data): array
    {
        if (empty($this->fillable)) {
            return $data;
        }
        
        return array_intersect_key($data, array_flip($this->fillable));
    }
    
    /**
     * Cek apakah tabel memiliki kolom tertentu
     * Simplified version - bisa dioverride di child class
     * 
     * @param string $column
     * @return bool
     */
    protected function hasColumn(string $column): bool
    {
        // Daftar kolom timestamp yang umum digunakan
        $timestampColumns = ['created_at', 'updated_at', 'deleted_at'];
        return in_array($column, $timestampColumns);
    }
    
    /**
     * Raw query
     * 
     * @param string $sql
     * @param array $params
     * @return \PDOStatement
     */
    public function raw(string $sql, array $params = []): \PDOStatement
    {
        return Database::query($sql, $params);
    }
}
