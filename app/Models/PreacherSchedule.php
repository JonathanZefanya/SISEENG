<?php
namespace App\Models;

use Core\Model;
use Core\Database;

/**
 * =========================================================
 * Preacher Schedule Model (Jadwal Pengkhotbah)
 * =========================================================
 */
class PreacherSchedule extends Model
{
    protected $table = 'preacher_schedules';
    
    protected $fillable = [
        'schedule_date',
        'service_name',
        'service_time',
        'preacher_name',
        'sermon_title',
        'notes'
    ];
    
    /**
     * Ambil jadwal berdasarkan bulan dan tahun
     * 
     * @param int $month
     * @param int $year
     * @return array
     */
    public function getByMonth(int $month, int $year): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE MONTH(schedule_date) = :month 
                AND YEAR(schedule_date) = :year 
                ORDER BY schedule_date ASC, service_time ASC";
        
        return Database::fetchAll($sql, [
            'month' => $month,
            'year' => $year
        ]);
    }
    
    /**
     * Ambil jadwal bulan ini
     * 
     * @return array
     */
    public function getCurrentMonth(): array
    {
        return $this->getByMonth((int) date('n'), (int) date('Y'));
    }
    
    /**
     * Ambil jadwal berdasarkan tanggal spesifik
     * 
     * @param string $date Format Y-m-d
     * @return array
     */
    public function getByDate(string $date): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE schedule_date = :date 
                ORDER BY service_time ASC";
        
        return Database::fetchAll($sql, ['date' => $date]);
    }
    
    /**
     * Ambil jadwal yang akan datang (dari hari ini)
     * 
     * @param int $limit
     * @return array
     */
    public function getUpcoming(int $limit = 10): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE schedule_date >= CURDATE()
                ORDER BY schedule_date ASC, service_time ASC 
                LIMIT :limit";
        
        return Database::fetchAll($sql, ['limit' => $limit]);
    }
    
    /**
     * Ambil semua jadwal dengan pagination
     * 
     * @param int $page
     * @param int $perPage
     * @param string|null $month Format: YYYY-MM
     * @return array
     */
    public function getAllPaginated(int $page = 1, int $perPage = 20, ?string $month = null): array
    {
        $offset = ($page - 1) * $perPage;
        $params = ['limit' => $perPage, 'offset' => $offset];
        
        $whereClause = "";
        if ($month) {
            $whereClause = "WHERE DATE_FORMAT(schedule_date, '%Y-%m') = :month";
            $params['month'] = $month;
        }
        
        $sql = "SELECT * FROM {$this->table} 
                {$whereClause}
                ORDER BY schedule_date DESC, service_time ASC 
                LIMIT :limit OFFSET :offset";
        
        return Database::fetchAll($sql, $params);
    }
    
    /**
     * Hitung total jadwal
     * 
     * @param string|null $month
     * @return int
     */
    public function countAll(?string $month = null): int
    {
        $params = [];
        $whereClause = "";
        
        if ($month) {
            $whereClause = "WHERE DATE_FORMAT(schedule_date, '%Y-%m') = :month";
            $params['month'] = $month;
        }
        
        $sql = "SELECT COUNT(*) FROM {$this->table} {$whereClause}";
        return (int) Database::fetchColumn($sql, $params);
    }
    
    /**
     * Ambil daftar pengkhotbah unik
     * 
     * @return array
     */
    public function getUniquePreachers(): array
    {
        $sql = "SELECT DISTINCT preacher_name FROM {$this->table} 
                WHERE preacher_name IS NOT NULL AND preacher_name != ''
                ORDER BY preacher_name ASC";
        
        $results = Database::fetchAll($sql);
        return array_column($results, 'preacher_name');
    }
    
    /**
     * Ambil daftar jenis ibadah unik
     * 
     * @return array
     */
    public function getUniqueServices(): array
    {
        $sql = "SELECT DISTINCT service_name FROM {$this->table} 
                ORDER BY service_name ASC";
        
        $results = Database::fetchAll($sql);
        return array_column($results, 'service_name');
    }
    
    /**
     * Generate jadwal kosong untuk 1 bulan berdasarkan template
     * 
     * @param int $month
     * @param int $year
     * @param array $templates Array of ['day' => 0-6 (0=Sunday), 'service_name' => '', 'service_time' => '']
     * @return int Number of schedules created
     */
    public function generateMonthlySchedule(int $month, int $year, array $templates): int
    {
        $count = 0;
        $startDate = new \DateTime("$year-$month-01");
        $endDate = (clone $startDate)->modify('last day of this month');
        
        while ($startDate <= $endDate) {
            $dayOfWeek = (int) $startDate->format('w'); // 0 = Sunday
            
            foreach ($templates as $template) {
                if ($template['day'] == $dayOfWeek) {
                    // Cek apakah sudah ada jadwal untuk tanggal dan waktu ini
                    $existing = $this->findWhere([
                        'schedule_date' => $startDate->format('Y-m-d'),
                        'service_time' => $template['service_time']
                    ]);
                    
                    if (!$existing) {
                        $this->create([
                            'schedule_date' => $startDate->format('Y-m-d'),
                            'service_name' => $template['service_name'],
                            'service_time' => $template['service_time'],
                            'preacher_name' => null,
                            'sermon_title' => null
                        ]);
                        $count++;
                    }
                }
            }
            
            $startDate->modify('+1 day');
        }
        
        return $count;
    }
    
    /**
     * Cari jadwal berdasarkan kondisi
     * 
     * @param array $conditions
     * @return array|null
     */
    public function findWhere(array $conditions): ?array
    {
        $where = [];
        $params = [];
        
        foreach ($conditions as $key => $value) {
            $where[] = "`{$key}` = :{$key}";
            $params[$key] = $value;
        }
        
        $sql = "SELECT * FROM {$this->table} WHERE " . implode(' AND ', $where) . " LIMIT 1";
        return Database::fetch($sql, $params);
    }
    
    /**
     * Jadwal dikelompokkan berdasarkan tanggal
     * 
     * @param int $month
     * @param int $year
     * @return array
     */
    public function getByMonthGrouped(int $month, int $year): array
    {
        $schedules = $this->getByMonth($month, $year);
        $grouped = [];
        
        foreach ($schedules as $schedule) {
            $date = $schedule['schedule_date'];
            if (!isset($grouped[$date])) {
                $grouped[$date] = [];
            }
            $grouped[$date][] = $schedule;
        }
        
        return $grouped;
    }
}
