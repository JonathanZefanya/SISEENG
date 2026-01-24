<?php
namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\Schedule;
use App\Models\ActivityLog;
use App\Middleware\RoleMiddleware;

/**
 * =========================================================
 * Admin Schedule Controller
 * =========================================================
 * 
 * Controller untuk mengelola jadwal ibadah
 */
class ScheduleController extends Controller
{
    protected $layout = 'admin';
    private $scheduleModel;
    
    public function __construct()
    {
        RoleMiddleware::requireAdmin();
        $this->scheduleModel = new Schedule();
    }
    
    /**
     * Daftar jadwal
     */
    public function index(): void
    {
        $schedules = $this->scheduleModel->all('day_of_week', 'ASC');
        
        $this->view('admin/schedules/index', [
            'title' => 'Jadwal Ibadah - ' . APP_NAME,
            'schedules' => $schedules,
        ]);
    }
    
    /**
     * Form tambah jadwal
     */
    public function create(): void
    {
        $this->view('admin/schedules/create', [
            'title' => 'Tambah Jadwal - ' . APP_NAME,
        ]);
    }
    
    /**
     * Simpan jadwal baru
     */
    public function store(): void
    {
        if (!$this->isPost()) {
            $this->redirect('admin/jadwal');
            return;
        }
        
        $this->validateCsrf();
        
        $data = [
            'title' => $this->post('title'),
            'day' => $this->post('day'),
            'time_start' => $this->post('time_start'),
            'time_end' => $this->post('time_end'),
            'location' => $this->post('location'),
            'description' => $this->post('description'),
            'is_active' => $this->post('is_active') ? 1 : 0,
        ];
        
        // Validasi
        $errors = $this->validateSchedule($data);
        
        if (!empty($errors)) {
            setFlash('error', implode('<br>', $errors));
            $this->saveOldInput();
            $this->redirect('admin/jadwal/tambah');
            return;
        }
        
        try {
            $id = $this->scheduleModel->create($data);
            
            ActivityLog::log(auth('id'), 'create_schedule', "Menambah jadwal: {$data['title']}");
            
            setFlash('success', 'Jadwal berhasil ditambahkan.');
            $this->clearOldInput();
        } catch (\Exception $e) {
            setFlash('error', 'Gagal menambahkan jadwal.');
            $this->saveOldInput();
            $this->redirect('admin/jadwal/tambah');
            return;
        }
        
        $this->redirect('admin/jadwal');
    }
    
    /**
     * Form edit jadwal
     */
    public function edit(int $id = 0): void
    {
        $schedule = $this->scheduleModel->find($id);
        
        if (!$schedule) {
            setFlash('error', 'Jadwal tidak ditemukan.');
            $this->redirect('admin/jadwal');
            return;
        }
        
        $this->view('admin/schedules/edit', [
            'title' => 'Edit Jadwal - ' . APP_NAME,
            'schedule' => $schedule,
        ]);
    }
    
    /**
     * Update jadwal
     */
    public function update(): void
    {
        if (!$this->isPost()) {
            $this->redirect('admin/jadwal');
            return;
        }
        
        $this->validateCsrf();
        
        $id = (int) $this->post('id');
        
        if (!$id || !$this->scheduleModel->find($id)) {
            setFlash('error', 'Jadwal tidak ditemukan.');
            $this->redirect('admin/jadwal');
            return;
        }
        
        $data = [
            'title' => $this->post('title'),
            'day' => $this->post('day'),
            'time_start' => $this->post('time_start'),
            'time_end' => $this->post('time_end'),
            'location' => $this->post('location'),
            'description' => $this->post('description'),
            'is_active' => $this->post('is_active') ? 1 : 0,
        ];
        
        // Validasi
        $errors = $this->validateSchedule($data);
        
        if (!empty($errors)) {
            setFlash('error', implode('<br>', $errors));
            $this->saveOldInput();
            $this->redirect('admin/jadwal/edit/' . $id);
            return;
        }
        
        try {
            $this->scheduleModel->update($id, $data);
            
            ActivityLog::log(auth('id'), 'update_schedule', "Mengupdate jadwal: {$data['title']}");
            
            setFlash('success', 'Jadwal berhasil diupdate.');
            $this->clearOldInput();
        } catch (\Exception $e) {
            setFlash('error', 'Gagal mengupdate jadwal.');
            $this->saveOldInput();
            $this->redirect('admin/jadwal/edit/' . $id);
            return;
        }
        
        $this->redirect('admin/jadwal');
    }
    
    /**
     * Hapus jadwal
     */
    public function delete(int $id = 0): void
    {
        if (!$this->isPost()) {
            $this->redirect('admin/jadwal');
            return;
        }
        
        $this->validateCsrf();
        
        $schedule = $this->scheduleModel->find($id);
        
        if (!$schedule) {
            setFlash('error', 'Jadwal tidak ditemukan.');
            $this->redirect('admin/jadwal');
            return;
        }
        
        try {
            $this->scheduleModel->delete($id);
            
            ActivityLog::log(auth('id'), 'delete_schedule', "Menghapus jadwal: {$schedule['title']}");
            
            setFlash('success', 'Jadwal berhasil dihapus.');
        } catch (\Exception $e) {
            setFlash('error', 'Gagal menghapus jadwal.');
        }
        
        $this->redirect('admin/jadwal');
    }
    
    /**
     * Validasi data jadwal
     */
    private function validateSchedule(array $data): array
    {
        $errors = [];
        $validDays = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        
        if (empty($data['title'])) {
            $errors[] = 'Judul jadwal wajib diisi.';
        }
        
        if (!in_array($data['day'], $validDays)) {
            $errors[] = 'Hari tidak valid.';
        }
        
        if (empty($data['time_start'])) {
            $errors[] = 'Waktu mulai wajib diisi.';
        }
        
        return $errors;
    }
}
