<?php
namespace Core;

/**
 * =========================================================
 * Database Class
 * =========================================================
 * 
 * Wrapper untuk koneksi database PDO
 * Menggunakan Singleton pattern untuk efisiensi koneksi
 * 
 * KEAMANAN:
 * - Menggunakan PDO dengan prepared statements
 * - Emulated prepares dimatikan untuk keamanan ekstra
 * - Exception mode untuk error handling yang baik
 */
class Database
{
    /**
     * Instance PDO
     * @var \PDO|null
     */
    private static $instance = null;
    
    /**
     * Konfigurasi koneksi
     * @var array
     */
    private static $config = [];
    
    /**
     * Private constructor - mencegah instantiasi langsung
     */
    private function __construct() {}
    
    /**
     * Mencegah cloning
     */
    private function __clone() {}
    
    /**
     * Mencegah unserialization
     */
    public function __wakeup() {
        throw new \Exception("Cannot unserialize singleton");
    }
    
    /**
     * Mendapatkan instance koneksi database
     * @return \PDO Instance PDO
     */
    public static function getInstance(): \PDO
    {
        if (self::$instance === null) {
            self::connect();
        }
        
        return self::$instance;
    }
    
    /**
     * Membuat koneksi ke database
     */
    private static function connect(): void
    {
        try {
            // DSN (Data Source Name)
            $dsn = sprintf(
                "mysql:host=%s;dbname=%s;charset=%s",
                DB_HOST,
                DB_NAME,
                DB_CHARSET
            );
            
            // Opsi PDO untuk keamanan dan performa
            $options = [
                // Gunakan exception untuk error handling
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                
                // Gunakan prepared statements native (bukan emulated)
                // Ini PENTING untuk keamanan SQL Injection
                \PDO::ATTR_EMULATE_PREPARES => false,
                
                // Return associative array secara default
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                
                // Jangan persistent connection (lebih aman)
                \PDO::ATTR_PERSISTENT => false,
                
                // Set charset
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ];
            
            // Buat koneksi
            self::$instance = new \PDO($dsn, DB_USER, DB_PASS, $options);
            
        } catch (\PDOException $e) {
            // Log error (dalam production, jangan tampilkan detail)
            if (ENVIRONMENT === 'development') {
                die("Database Connection Error: " . $e->getMessage());
            } else {
                error_log("Database Connection Error: " . $e->getMessage());
                die("Terjadi kesalahan pada sistem. Silakan hubungi administrator.");
            }
        }
    }
    
    /**
     * Menjalankan query dengan prepared statement
     * 
     * @param string $sql Query SQL dengan placeholder
     * @param array $params Parameter untuk binding
     * @return \PDOStatement
     */
    public static function query(string $sql, array $params = []): \PDOStatement
    {
        $db = self::getInstance();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    /**
     * Mengambil satu baris hasil query
     * 
     * @param string $sql Query SQL
     * @param array $params Parameter
     * @return array|false
     */
    public static function fetch(string $sql, array $params = [])
    {
        return self::query($sql, $params)->fetch();
    }
    
    /**
     * Mengambil semua baris hasil query
     * 
     * @param string $sql Query SQL
     * @param array $params Parameter
     * @return array
     */
    public static function fetchAll(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }
    
    /**
     * Mengambil satu kolom dari baris pertama
     * 
     * @param string $sql Query SQL
     * @param array $params Parameter
     * @return mixed
     */
    public static function fetchColumn(string $sql, array $params = [])
    {
        return self::query($sql, $params)->fetchColumn();
    }
    
    /**
     * Insert data dan return last insert ID
     * 
     * @param string $table Nama tabel
     * @param array $data Data yang akan diinsert
     * @return int|string Last insert ID
     */
    public static function insert(string $table, array $data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        
        self::query($sql, $data);
        
        return self::getInstance()->lastInsertId();
    }
    
    /**
     * Update data
     * 
     * @param string $table Nama tabel
     * @param array $data Data yang akan diupdate
     * @param string $where Kondisi WHERE
     * @param array $whereParams Parameter untuk WHERE
     * @return int Jumlah baris yang terpengaruh
     */
    public static function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $setClause = [];
        foreach (array_keys($data) as $column) {
            $setClause[] = "{$column} = :{$column}";
        }
        $setClause = implode(', ', $setClause);
        
        $sql = "UPDATE {$table} SET {$setClause} WHERE {$where}";
        
        $params = array_merge($data, $whereParams);
        
        return self::query($sql, $params)->rowCount();
    }
    
    /**
     * Delete data
     * 
     * @param string $table Nama tabel
     * @param string $where Kondisi WHERE
     * @param array $params Parameter
     * @return int Jumlah baris yang dihapus
     */
    public static function delete(string $table, string $where, array $params = []): int
    {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        return self::query($sql, $params)->rowCount();
    }
    
    /**
     * Memulai transaksi
     */
    public static function beginTransaction(): void
    {
        self::getInstance()->beginTransaction();
    }
    
    /**
     * Commit transaksi
     */
    public static function commit(): void
    {
        self::getInstance()->commit();
    }
    
    /**
     * Rollback transaksi
     */
    public static function rollback(): void
    {
        self::getInstance()->rollBack();
    }
    
    /**
     * Menutup koneksi
     */
    public static function close(): void
    {
        self::$instance = null;
    }
}
