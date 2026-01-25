<?php
namespace App\Models;

use Core\Model;
use Core\Database;

/**
 * =========================================================
 * DonationAccount Model (Rekening Donasi)
 * =========================================================
 * 
 * Model untuk mengelola rekening bank donasi
 */
class DonationAccount extends Model
{
    protected $table = 'donation_accounts';
    
    protected $fillable = [
        'bank_name',
        'account_number',
        'account_name',
        'bank_logo',
        'is_active',
        'sort_order'
    ];
    
    /**
     * Ambil semua rekening aktif
     * 
     * @return array
     */
    public function getActive(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY sort_order ASC, id ASC";
        return Database::fetchAll($sql);
    }
    
    /**
     * Ambil semua rekening
     * 
     * @return array
     */
    public function getAll(): array
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY sort_order ASC, id ASC";
        return Database::fetchAll($sql);
    }
    
    /**
     * Ambil rekening berdasarkan ID
     * 
     * @param int $id
     * @return array|null
     */
    public function getById(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        return Database::fetch($sql, ['id' => $id]);
    }
    
    /**
     * Simpan rekening baru
     * 
     * @param array $data
     * @return int|false
     */
    public function create(array $data)
    {
        $sql = "INSERT INTO {$this->table} (bank_name, account_number, account_name, bank_logo, is_active, sort_order, created_at) 
                VALUES (:bank_name, :account_number, :account_name, :bank_logo, :is_active, :sort_order, NOW())";
        
        $params = [
            'bank_name' => $data['bank_name'],
            'account_number' => $data['account_number'],
            'account_name' => $data['account_name'],
            'bank_logo' => $data['bank_logo'] ?? null,
            'is_active' => $data['is_active'] ?? 1,
            'sort_order' => $data['sort_order'] ?? 0
        ];
        
        if (Database::execute($sql, $params)) {
            return Database::lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Update rekening
     * 
     * @param int $id
     * @param array $data
     * @return int
     */
    public function updateAccount(int $id, array $data): bool
    {
        $sql = "UPDATE {$this->table} SET 
                bank_name = :bank_name,
                account_number = :account_number,
                account_name = :account_name,
                bank_logo = :bank_logo,
                is_active = :is_active,
                sort_order = :sort_order,
                updated_at = NOW()
                WHERE id = :id";
        
        $params = [
            'id' => $id,
            'bank_name' => $data['bank_name'],
            'account_number' => $data['account_number'],
            'account_name' => $data['account_name'],
            'bank_logo' => $data['bank_logo'] ?? null,
            'is_active' => $data['is_active'] ?? 1,
            'sort_order' => $data['sort_order'] ?? 0
        ];
        
        return Database::execute($sql, $params);
    }
    
    /**
     * Hapus rekening
     * 
     * @param int $id
     * @return bool
     */
    public function deleteAccount(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        return Database::execute($sql, ['id' => $id]);
    }
    
    /**
     * Toggle status aktif
     * 
     * @param int $id
     * @return bool
     */
    public function toggleActive(int $id): bool
    {
        $sql = "UPDATE {$this->table} SET is_active = NOT is_active, updated_at = NOW() WHERE id = :id";
        return Database::execute($sql, ['id' => $id]);
    }
    
    /**
     * Hitung total rekening
     * 
     * @return int
     */
    public function countAll(): int
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $result = Database::fetch($sql);
        return (int) ($result['total'] ?? 0);
    }
    
    /**
     * Get next sort order
     * 
     * @return int
     */
    public function getNextSortOrder(): int
    {
        $sql = "SELECT MAX(sort_order) as max_order FROM {$this->table}";
        $result = Database::fetch($sql);
        return (int) ($result['max_order'] ?? 0) + 1;
    }
}
