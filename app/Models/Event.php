<?php
namespace App\Models;

use Core\Model;
use Core\Database;

/**
 * =========================================================
 * Event Model (Kegiatan)
 * =========================================================
 */
class Event extends Model
{
    protected $table = 'events';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'image',
        'event_date',
        'event_time',
        'location',
        'status',
        'created_by'
    ];

    /**
     * Ambil event yang akan datang
     * 
     * @param int $limit
     * @return array
     */
    public function getUpcoming(int $limit = 5): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE event_date >= CURDATE() AND status = 'published'
                ORDER BY event_date ASC, event_time ASC 
                LIMIT :limit";

        $stmt = Database::getInstance()->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Ambil semua event untuk admin (semua status) dengan pagination
     * 
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getWithPagination(int $page = 1, int $perPage = ITEMS_PER_PAGE): array
    {
        return $this->paginate($page, $perPage, 'event_date', 'DESC');
    }

    /**
     * Filter waktu di halaman publik: kunci URL => [label, kondisi SQL, urutan]
     * Kegiatan beberapa hari dianggap berlangsung sampai end_date.
     */
    const TIME_FILTERS = [
        'mendatang' => ['Akan datang', "COALESCE(end_date, event_date) >= CURDATE()", 'event_date ASC, event_time ASC'],
        'bulan-ini' => ['Bulan ini', "event_date BETWEEN DATE_FORMAT(CURDATE(), '%Y-%m-01') AND LAST_DAY(CURDATE())", 'event_date ASC, event_time ASC'],
        'selesai' => ['Sudah lewat', "COALESCE(end_date, event_date) < CURDATE()", 'event_date DESC, event_time DESC'],
    ];

    /**
     * Jumlah kegiatan terbit per filter waktu (untuk chip filter)
     *
     * @return array ['semua' => n, 'mendatang' => n, ...]
     */
    public function countPublishedByTime(): array
    {
        $select = ["COUNT(*) AS semua"];
        foreach (self::TIME_FILTERS as $key => [, $condition]) {
            $select[] = "SUM({$condition}) AS `{$key}`";
        }
        $row = Database::fetch("SELECT " . implode(', ', $select) . " FROM {$this->table} WHERE status = 'published'");
        return array_map('intval', $row ?: []);
    }

    /**
     * Ambil event yang sudah dipublikasikan untuk halaman publik
     * 
     * @param int $page
     * @param int $perPage
     * @param string $when Kunci TIME_FILTERS, kosong = semua
     * @return array
     */
    public function getPublished(int $page = 1, int $perPage = ITEMS_PER_PAGE, string $when = ''): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $where = "status = 'published'";
        $order = 'event_date DESC';
        if (isset(self::TIME_FILTERS[$when])) {
            $where .= ' AND ' . self::TIME_FILTERS[$when][1];
            $order = self::TIME_FILTERS[$when][2];
        }

        $total = (int) Database::fetchColumn(
            "SELECT COUNT(*) FROM {$this->table} WHERE {$where}"
        );

        $sql = "SELECT * FROM {$this->table}
                WHERE {$where}
                ORDER BY {$order}
                LIMIT :limit OFFSET :offset";

        $stmt = Database::getInstance()->prepare($sql);
        $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll();

        return [
            'data' => $data,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => $total > 0 ? (int) ceil($total / $perPage) : 1,
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total),
        ];
    }

    /**
     * Cari event berdasarkan slug
     * 
     * @param string $slug
     * @return array|false
     */
    public function findBySlug(string $slug)
    {
        return $this->findBy('slug', $slug);
    }

    /**
     * Generate slug unik
     * 
     * @param string $title
     * @param int|null $exceptId
     * @return string
     */
    public function generateSlug(string $title, ?int $exceptId = null): string
    {
        $slug = slugify($title);
        $originalSlug = $slug;
        $counter = 1;

        while ($this->exists('slug', $slug, $exceptId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Ambil event berdasarkan bulan
     * 
     * @param int $month
     * @param int $year
     * @return array
     */
    public function getByMonth(int $month, int $year): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE MONTH(event_date) = :month 
                AND YEAR(event_date) = :year 
                AND status = 'published'
                ORDER BY event_date ASC";

        return Database::fetchAll($sql, ['month' => $month, 'year' => $year]);
    }
}
