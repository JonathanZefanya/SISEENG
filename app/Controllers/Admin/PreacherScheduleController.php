<?php
namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\PreacherSchedule;
use App\Models\Schedule;
use App\Middleware\RoleMiddleware;

/**
 * =========================================================
 * Preacher Schedule Controller (Admin)
 * =========================================================
 * 
 * Mengelola jadwal pengkhotbah bulanan
 */
class PreacherScheduleController extends Controller
{
    protected $layout = 'admin';
    private $scheduleModel;
    private $ibadahModel;
    
    public function __construct()
    {
        RoleMiddleware::requireAdmin();
        $this->scheduleModel = new PreacherSchedule();
        $this->ibadahModel = new Schedule();
    }
    
    /**
     * Halaman daftar jadwal pengkhotbah
     */
    public function index(): void
    {
        $page = (int) ($this->get('page') ?? 1);
        $month = $this->get('month') ?? date('Y-m');
        $perPage = 30;
        
        $schedules = $this->scheduleModel->getAllPaginated($page, $perPage, $month);
        $total = $this->scheduleModel->countAll($month);
        $totalPages = ceil($total / $perPage);
        
        // Daftar pengkhotbah untuk autocomplete
        $preachers = $this->scheduleModel->getUniquePreachers();
        $services = $this->scheduleModel->getUniqueServices();
        
        $this->view('admin/preacher-schedules/index', [
            'title' => 'Jadwal Pengkhotbah - ' . APP_NAME,
            'schedules' => $schedules,
            'preachers' => $preachers,
            'services' => $services,
            'currentMonth' => $month,
            'pagination' => [
                'current' => $page,
                'total' => $totalPages,
                'count' => $total
            ]
        ]);
    }
    
    /**
     * Form tambah jadwal
     */
    public function create(): void
    {
        $preachers = $this->scheduleModel->getUniquePreachers();
        
        // Ambil jadwal ibadah dari tabel schedules
        $ibadahList = $this->ibadahModel->getActiveSchedules();
        
        $this->view('admin/preacher-schedules/form', [
            'title' => 'Tambah Jadwal Pengkhotbah - ' . APP_NAME,
            'schedule' => null,
            'preachers' => $preachers,
            'ibadahList' => $ibadahList,
            'isEdit' => false
        ]);
    }
    
    /**
     * Simpan jadwal baru
     */
    public function store(): void
    {
        if (!$this->isPost()) {
            $this->redirect('admin/jadwal-pengkhotbah');
            return;
        }
        
        $this->validateCsrf();
        
        $data = [
            'schedule_date' => $this->post('schedule_date'),
            'service_name' => $this->post('service_name'),
            'service_time' => $this->post('service_time'),
            'preacher_name' => $this->post('preacher_name') ?: null,
            'sermon_title' => $this->post('sermon_title') ?: null,
            'notes' => $this->post('notes') ?: null
        ];
        
        // Validasi
        if (empty($data['schedule_date']) || empty($data['service_name']) || empty($data['service_time'])) {
            setFlash('error', 'Tanggal, jenis ibadah, dan waktu wajib diisi.');
            $this->redirect('admin/jadwal-pengkhotbah/create');
            return;
        }
        
        // Validasi duplikat - cek apakah sudah ada jadwal dengan tanggal dan waktu yang sama
        $existing = $this->scheduleModel->findWhere([
            'schedule_date' => $data['schedule_date'],
            'service_time' => $data['service_time']
        ]);
        
        if ($existing) {
            setFlash('error', 'Jadwal untuk tanggal dan waktu tersebut sudah ada. Silakan edit jadwal yang sudah ada.');
            $this->redirect('admin/jadwal-pengkhotbah/create');
            return;
        }
        
        if ($this->scheduleModel->create($data)) {
            setFlash('success', 'Jadwal pengkhotbah berhasil ditambahkan!');
        } else {
            setFlash('error', 'Gagal menambahkan jadwal.');
        }
        
        $this->redirect('admin/jadwal-pengkhotbah');
    }
    
    /**
     * Form edit jadwal
     */
    public function edit(int $id): void
    {
        $schedule = $this->scheduleModel->find($id);
        
        if (!$schedule) {
            setFlash('error', 'Jadwal tidak ditemukan.');
            $this->redirect('admin/jadwal-pengkhotbah');
            return;
        }
        
        $preachers = $this->scheduleModel->getUniquePreachers();
        $ibadahList = $this->ibadahModel->getActiveSchedules();
        
        $this->view('admin/preacher-schedules/form', [
            'title' => 'Edit Jadwal Pengkhotbah - ' . APP_NAME,
            'schedule' => $schedule,
            'preachers' => $preachers,
            'ibadahList' => $ibadahList,
            'isEdit' => true
        ]);
    }
    
    /**
     * Update jadwal
     */
    public function update(int $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('admin/jadwal-pengkhotbah');
            return;
        }
        
        $this->validateCsrf();
        
        $schedule = $this->scheduleModel->find($id);
        if (!$schedule) {
            setFlash('error', 'Jadwal tidak ditemukan.');
            $this->redirect('admin/jadwal-pengkhotbah');
            return;
        }
        
        $data = [
            'schedule_date' => $this->post('schedule_date'),
            'service_name' => $this->post('service_name'),
            'service_time' => $this->post('service_time'),
            'preacher_name' => $this->post('preacher_name') ?: null,
            'sermon_title' => $this->post('sermon_title') ?: null,
            'notes' => $this->post('notes') ?: null
        ];
        
        // Validasi
        if (empty($data['schedule_date']) || empty($data['service_name']) || empty($data['service_time'])) {
            setFlash('error', 'Tanggal, jenis ibadah, dan waktu wajib diisi.');
            $this->redirect("admin/jadwal-pengkhotbah/edit/{$id}");
            return;
        }
        
        // Validasi duplikat - cek apakah ada jadwal lain dengan tanggal dan waktu yang sama
        $existing = $this->scheduleModel->findWhere([
            'schedule_date' => $data['schedule_date'],
            'service_time' => $data['service_time']
        ]);
        
        if ($existing && $existing['id'] != $id) {
            setFlash('error', 'Jadwal untuk tanggal dan waktu tersebut sudah ada.');
            $this->redirect("admin/jadwal-pengkhotbah/edit/{$id}");
            return;
        }
        
        if ($this->scheduleModel->update($id, $data)) {
            setFlash('success', 'Jadwal pengkhotbah berhasil diperbarui!');
        } else {
            setFlash('error', 'Gagal memperbarui jadwal.');
        }
        
        $this->redirect('admin/jadwal-pengkhotbah');
    }
    
    /**
     * Hapus jadwal
     */
    public function delete(int $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('admin/jadwal-pengkhotbah');
            return;
        }
        
        $this->validateCsrf();
        
        if ($this->scheduleModel->delete($id)) {
            setFlash('success', 'Jadwal pengkhotbah berhasil dihapus!');
        } else {
            setFlash('error', 'Gagal menghapus jadwal.');
        }
        
        $this->redirect('admin/jadwal-pengkhotbah');
    }
    
    /**
     * Generate jadwal untuk 1 bulan
     */
    public function generate(): void
    {
        if (!$this->isPost()) {
            $this->redirect('admin/jadwal-pengkhotbah');
            return;
        }
        
        $this->validateCsrf();
        
        $month = (int) $this->post('month');
        $year = (int) $this->post('year');
        
        if ($month < 1 || $month > 12 || $year < 2020) {
            setFlash('error', 'Bulan atau tahun tidak valid.');
            $this->redirect('admin/jadwal-pengkhotbah');
            return;
        }
        
        // Ambil jadwal ibadah dari tabel schedules untuk dijadikan template
        $ibadahList = $this->ibadahModel->getActiveSchedules();
        
        // Konversi ke template
        $templates = [];
        $dayMap = [
            'Minggu' => 0, 'Senin' => 1, 'Selasa' => 2, 'Rabu' => 3,
            'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6
        ];
        
        foreach ($ibadahList as $ibadah) {
            $dayNum = $dayMap[$ibadah['day_of_week']] ?? null;
            if ($dayNum !== null) {
                $templates[] = [
                    'day' => $dayNum,
                    'service_name' => $ibadah['title'],
                    'service_time' => $ibadah['start_time']
                ];
            }
        }
        
        if (empty($templates)) {
            setFlash('error', 'Tidak ada jadwal ibadah yang terdaftar. Silakan tambahkan jadwal ibadah terlebih dahulu.');
            $this->redirect('admin/jadwal-pengkhotbah');
            return;
        }
        
        $count = $this->scheduleModel->generateMonthlySchedule($month, $year, $templates);
        
        if ($count > 0) {
            setFlash('success', "Berhasil membuat {$count} jadwal untuk bulan " . $this->getMonthName($month) . " {$year}");
        } else {
            setFlash('info', 'Tidak ada jadwal baru yang dibuat. Jadwal mungkin sudah ada.');
        }
        
        $this->redirect('admin/jadwal-pengkhotbah?month=' . $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT));
    }
    
    /**
     * Quick update pengkhotbah (AJAX)
     */
    public function quickUpdate(): void
    {
        if (!$this->isPost() || !$this->isAjax()) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
            return;
        }
        
        $id = (int) $this->post('id');
        $preacher = $this->post('preacher_name');
        $sermon = $this->post('sermon_title');
        
        $schedule = $this->scheduleModel->find($id);
        if (!$schedule) {
            $this->json(['success' => false, 'message' => 'Jadwal tidak ditemukan'], 404);
            return;
        }
        
        $data = [
            'preacher_name' => $preacher ?: null,
            'sermon_title' => $sermon ?: null
        ];
        
        if ($this->scheduleModel->update($id, $data)) {
            $this->json(['success' => true, 'message' => 'Berhasil diperbarui']);
        } else {
            $this->json(['success' => false, 'message' => 'Gagal memperbarui'], 500);
        }
    }
    
    /**
     * Get month name in Indonesian
     */
    private function getMonthName(int $month): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return $months[$month] ?? '';
    }
}
