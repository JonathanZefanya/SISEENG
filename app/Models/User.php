<?php
namespace App\Models;

use Core\Model;
use Core\Database;
use Core\Security;

/**
 * =========================================================
 * User Model
 * =========================================================
 * 
 * Mengelola data pengguna/admin sistem
 */
class User extends Model
{
    protected $table = 'users';
    
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'last_login',
        'avatar'
    ];
    
    /**
     * Cari user berdasarkan email
     * 
     * @param string $email
     * @return array|false
     */
    public function findByEmail(string $email)
    {
        return $this->findBy('email', $email);
    }
    
    /**
     * Autentikasi user
     * 
     * @param string $email
     * @param string $password
     * @return array|false User data atau false jika gagal
     */
    public function authenticate(string $email, string $password)
    {
        $user = $this->findByEmail($email);
        
        if (!$user) {
            return false;
        }
        
        // Cek apakah user aktif
        if ($user['status'] !== 'active') {
            return false;
        }
        
        // Verifikasi password
        if (!Security::verifyPassword($password, $user['password'])) {
            return false;
        }
        
        // Cek apakah password perlu di-rehash
        if (Security::needsRehash($user['password'])) {
            $newHash = Security::hashPassword($password);
            $this->update($user['id'], ['password' => $newHash]);
        }
        
        // Update last login
        $this->updateLastLogin($user['id']);
        
        // Hapus password dari data yang dikembalikan
        unset($user['password']);
        
        return $user;
    }
    
    /**
     * Buat user baru
     * 
     * @param array $data
     * @return int|string User ID
     */
    public function createUser(array $data)
    {
        // Hash password
        if (isset($data['password'])) {
            $data['password'] = Security::hashPassword($data['password']);
        }
        
        // Set default values
        $data['status'] = $data['status'] ?? 'active';
        $data['role'] = $data['role'] ?? ROLE_ADMIN;
        
        return $this->create($data);
    }
    
    /**
     * Update user
     * 
     * @param int $id
     * @param array $data
     * @return int
     */
    public function updateUser(int $id, array $data): int
    {
        // Hash password jika diupdate
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Security::hashPassword($data['password']);
        } else {
            unset($data['password']);
        }
        
        return $this->update($id, $data);
    }
    
    /**
     * Update last login timestamp
     * 
     * @param int $id
     */
    public function updateLastLogin(int $id): void
    {
        $sql = "UPDATE {$this->table} SET last_login = NOW() WHERE id = :id";
        Database::query($sql, ['id' => $id]);
    }
    
    /**
     * Toggle status user (active/blocked)
     * 
     * @param int $id
     * @return string New status
     */
    public function toggleStatus(int $id): string
    {
        $user = $this->find($id);
        
        if (!$user) {
            return '';
        }
        
        $newStatus = $user['status'] === 'active' ? 'blocked' : 'active';
        
        $sql = "UPDATE {$this->table} SET status = :status WHERE id = :id";
        Database::query($sql, ['status' => $newStatus, 'id' => $id]);
        
        return $newStatus;
    }
    
    /**
     * Ambil semua admin (exclude super admin)
     * 
     * @return array
     */
    public function getAllAdmins(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE role = :role ORDER BY created_at DESC";
        return Database::fetchAll($sql, ['role' => ROLE_ADMIN]);
    }
    
    /**
     * Ambil semua user dengan pagination
     * 
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getAllWithPagination(int $page = 1, int $perPage = ITEMS_PER_PAGE): array
    {
        $offset = ($page - 1) * $perPage;
        
        // Exclude super admin dari list
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        $total = (int) Database::fetchColumn($sql);
        
        $sql = "SELECT id, name, email, role, status, last_login, created_at 
                FROM {$this->table} 
                ORDER BY created_at DESC 
                LIMIT :limit OFFSET :offset";
        
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
            'last_page' => ceil($total / $perPage)
        ];
    }
    
    /**
     * Cek apakah email sudah digunakan
     * 
     * @param string $email
     * @param int|null $exceptId
     * @return bool
     */
    public function emailExists(string $email, ?int $exceptId = null): bool
    {
        return $this->exists('email', $email, $exceptId);
    }
    
    /**
     * Hitung user berdasarkan role
     * 
     * @param string $role
     * @return int
     */
    public function countByRole(string $role): int
    {
        return $this->count('role', $role);
    }
}
