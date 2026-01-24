<?php
namespace App\Models;

use Core\Model;
use Core\Database;

/**
 * =========================================================
 * Schedule Model (Jadwal Ibadah)
 * =========================================================
 */
class Schedule extends Model
{
    protected $table = 'schedules';
    
    protected $fillable = [
        'title',
        'description',
        'day_of_week',
        'start_time',
        'end_time',
        'location',
        'is_active',
        'sort_order'
    ];
    
    /**
     * Ambil jadwal yang aktif
     * 
     * @return array
     */
    public function getActiveSchedules(): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE is_active = 1 
                ORDER BY FIELD(day_of_week, 'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'), 
                         start_time ASC";
        return Database::fetchAll($sql);
    }
    
    /**
     * Ambil jadwal berdasarkan hari
     * 
     * @param string $day
     * @return array
     */
    public function getByDay(string $day): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE day_of_week = :day AND is_active = 1 ORDER BY start_time ASC";
        return Database::fetchAll($sql, ['day' => $day]);
    }
    
    /**
     * Toggle status aktif jadwal
     * 
     * @param int $id
     * @return bool
     */
    public function toggleActive(int $id): bool
    {
        $schedule = $this->find($id);
        if (!$schedule) {
            return false;
        }
        
        $newStatus = $schedule['is_active'] ? 0 : 1;
        $this->update($id, ['is_active' => $newStatus]);
        
        return true;
    }
}
