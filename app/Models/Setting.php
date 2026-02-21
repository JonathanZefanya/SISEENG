<?php
namespace App\Models;

use Core\Model;
use Core\Database;

/**
 * =========================================================
 * Setting Model (Pengaturan Website)
 * =========================================================
 */
class Setting extends Model
{
    protected $table = 'settings';
    
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description'
    ];
    
    /**
     * Ambil nilai setting berdasarkan key
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        $sql = "SELECT value FROM settings WHERE `key` = :key LIMIT 1";
        $result = Database::fetch($sql, ['key' => $key]);
        
        return $result ? $result['value'] : $default;
    }
    
    /**
     * Set nilai setting
     * 
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public static function set(string $key, $value): bool
    {
        $db = Database::getInstance();
        
        try {
            $sql = "INSERT INTO settings (`key`, `value`, `updated_at`) 
                    VALUES (:key, :value, NOW()) 
                    ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = NOW()";
            
            $stmt = $db->prepare($sql);
            return $stmt->execute([
                'key' => $key,
                'value' => $value
            ]);
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Ambil semua settings berdasarkan group
     * 
     * @param string $group
     * @return array
     */
    public function getByGroup(string $group): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE `group` = :group ORDER BY id ASC";
        return Database::fetchAll($sql, ['group' => $group]);
    }
    
    /**
     * Ambil semua settings sebagai key-value array
     * 
     * @return array
     */
    public static function getAllAsArray(): array
    {
        $sql = "SELECT `key`, `value` FROM settings";
        $results = Database::fetchAll($sql);
        
        $settings = [];
        foreach ($results as $row) {
            $settings[$row['key']] = $row['value'];
        }
        
        return $settings;
    }
    
    /**
     * Ambil settings berdasarkan group sebagai key-value
     * 
     * @param string $group
     * @return array
     */
    public static function getGroupAsArray(string $group): array
    {
        $sql = "SELECT `key`, `value` FROM settings WHERE `group` = :group";
        $results = Database::fetchAll($sql, ['group' => $group]);
        
        $settings = [];
        foreach ($results as $row) {
            $settings[$row['key']] = $row['value'];
        }
        
        return $settings;
    }
    
    /**
     * Update multiple settings sekaligus
     * 
     * @param array $data Key-value pairs
     * @return bool
     */
    public function updateMultiple(array $data): bool
    {
        $db = Database::getInstance();
        
        try {
            $db->beginTransaction();
            
            // Gunakan INSERT ... ON DUPLICATE KEY UPDATE agar setting yang belum ada juga bisa disimpan
            $sql = "INSERT INTO {$this->table} (`key`, `value`, `updated_at`) 
                    VALUES (:key, :value, NOW()) 
                    ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = NOW()";
            $stmt = $db->prepare($sql);
            
            foreach ($data as $key => $value) {
                $stmt->execute(['key' => $key, 'value' => $value ?? '']);
            }
            
            $db->commit();
            return true;
        } catch (\Exception $e) {
            $db->rollBack();
            return false;
        }
    }
    
    /**
     * Cek apakah setting key exists
     * 
     * @param string $key
     * @return bool
     */
    public static function has(string $key): bool
    {
        $sql = "SELECT COUNT(*) FROM settings WHERE `key` = :key";
        return (int) Database::fetchColumn($sql, ['key' => $key]) > 0;
    }
}
