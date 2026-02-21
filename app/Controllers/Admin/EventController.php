<?php
namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\Event;
use App\Models\ActivityLog;
use App\Middleware\RoleMiddleware;

/**
 * =========================================================
 * Admin Event Controller
 * =========================================================
 * 
 * Controller untuk mengelola kegiatan/event
 */
class EventController extends Controller
{
    protected $layout = 'admin';
    private $eventModel;

    public function __construct()
    {
        RoleMiddleware::requireAdmin();
        $this->eventModel = new Event();
    }

    /**
     * Daftar kegiatan
     */
    public function index(): void
    {
        $page = (int) ($this->get('page') ?? 1);
        $events = $this->eventModel->getWithPagination($page);

        $this->view('admin/events/index', [
            'title' => 'Kelola Kegiatan - ' . APP_NAME,
            'events' => $events['data'],
            'pagination' => $events,
        ]);
    }

    /**
     * Form tambah kegiatan
     */
    public function create(): void
    {
        $this->view('admin/events/create', [
            'title' => 'Tambah Kegiatan - ' . APP_NAME,
        ]);
    }

    /**
     * Simpan kegiatan baru
     */
    public function store(): void
    {
        if (!$this->isPost()) {
            $this->redirect('admin/kegiatan');
            return;
        }

        $this->validateCsrf();

        $title = $this->post('title');

        $data = [
            'title' => $title,
            'slug' => $this->eventModel->generateSlug($title),
            'description' => $this->post('description'),
            'event_date' => $this->post('event_date'),
            'event_time' => $this->post('event_time'),
            'location' => $this->post('location'),
            'status' => $this->post('status'),
            'created_by' => auth('id'),
        ];

        // Validasi
        $errors = $this->validateEvent($data);

        if (!empty($errors)) {
            setFlash('error', implode('<br>', $errors));
            $this->saveOldInput();
            $this->redirect('admin/kegiatan/tambah');
            return;
        }

        // Handle upload gambar
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imagePath = $this->uploadImage($_FILES['image']);
            if ($imagePath) {
                $data['image'] = $imagePath;
            }
        }

        try {
            $id = $this->eventModel->create($data);

            ActivityLog::log(auth('id'), 'create_event', "Menambah kegiatan: {$data['title']}");

            setFlash('success', 'Kegiatan berhasil ditambahkan.');
            $this->clearOldInput();
        } catch (\Exception $e) {
            setFlash('error', 'Gagal menambahkan kegiatan.');
            $this->saveOldInput();
            $this->redirect('admin/kegiatan/tambah');
            return;
        }

        $this->redirect('admin/kegiatan');
    }

    /**
     * Form edit kegiatan
     */
    public function edit(int $id = 0): void
    {
        $event = $this->eventModel->find($id);

        if (!$event) {
            setFlash('error', 'Kegiatan tidak ditemukan.');
            $this->redirect('admin/kegiatan');
            return;
        }

        $this->view('admin/events/edit', [
            'title' => 'Edit Kegiatan - ' . APP_NAME,
            'event' => $event,
        ]);
    }

    /**
     * Update kegiatan
     */
    public function update(): void
    {
        if (!$this->isPost()) {
            $this->redirect('admin/kegiatan');
            return;
        }

        $this->validateCsrf();

        $id = (int) $this->post('id');
        $event = $this->eventModel->find($id);

        if (!$event) {
            setFlash('error', 'Kegiatan tidak ditemukan.');
            $this->redirect('admin/kegiatan');
            return;
        }

        $title = $this->post('title');

        $data = [
            'title' => $title,
            'slug' => $this->eventModel->generateSlug($title, $id),
            'description' => $this->post('description'),
            'event_date' => $this->post('event_date'),
            'event_time' => $this->post('event_time'),
            'location' => $this->post('location'),
            'status' => $this->post('status'),
        ];

        // Validasi
        $errors = $this->validateEvent($data);

        if (!empty($errors)) {
            setFlash('error', implode('<br>', $errors));
            $this->saveOldInput();
            $this->redirect('admin/kegiatan/edit/' . $id);
            return;
        }

        // Handle upload gambar
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imagePath = $this->uploadImage($_FILES['image']);
            if ($imagePath) {
                // Hapus gambar lama
                if ($event['image'] && file_exists(PUBLIC_PATH . 'uploads/' . $event['image'])) {
                    unlink(PUBLIC_PATH . 'uploads/' . $event['image']);
                }
                $data['image'] = $imagePath;
            }
        }

        try {
            $this->eventModel->update($id, $data);

            ActivityLog::log(auth('id'), 'update_event', "Mengupdate kegiatan: {$data['title']}");

            setFlash('success', 'Kegiatan berhasil diupdate.');
            $this->clearOldInput();
        } catch (\Exception $e) {
            setFlash('error', 'Gagal mengupdate kegiatan.');
            $this->saveOldInput();
            $this->redirect('admin/kegiatan/edit/' . $id);
            return;
        }

        $this->redirect('admin/kegiatan');
    }

    /**
     * Hapus kegiatan
     */
    public function delete(int $id = 0): void
    {
        if (!$this->isPost()) {
            $this->redirect('admin/kegiatan');
            return;
        }

        $this->validateCsrf();

        $event = $this->eventModel->find($id);

        if (!$event) {
            setFlash('error', 'Kegiatan tidak ditemukan.');
            $this->redirect('admin/kegiatan');
            return;
        }

        try {
            // Hapus gambar
            if ($event['image'] && file_exists(PUBLIC_PATH . 'uploads/' . $event['image'])) {
                unlink(PUBLIC_PATH . 'uploads/' . $event['image']);
            }

            $this->eventModel->delete($id);

            ActivityLog::log(auth('id'), 'delete_event', "Menghapus kegiatan: {$event['title']}");

            setFlash('success', 'Kegiatan berhasil dihapus.');
        } catch (\Exception $e) {
            setFlash('error', 'Gagal menghapus kegiatan.');
        }

        $this->redirect('admin/kegiatan');
    }

    /**
     * Upload gambar
     */
    private function uploadImage(array $file): ?string
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024;

        if (!in_array($file['type'], $allowedTypes)) {
            setFlash('warning', 'Format gambar tidak didukung.');
            return null;
        }

        if ($file['size'] > $maxSize) {
            setFlash('warning', 'Ukuran gambar terlalu besar (maks 5MB).');
            return null;
        }

        $uploadDir = 'events/';
        $fullPath = PUBLIC_PATH . 'uploads/' . $uploadDir;

        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0755, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('event_') . '.' . $extension;

        if (move_uploaded_file($file['tmp_name'], $fullPath . $filename)) {
            return $uploadDir . $filename; // simpan: 'events/file.jpg'
        }

        return null;
    }

    /**
     * Validasi data kegiatan
     */
    private function validateEvent(array $data): array
    {
        $errors = [];

        if (empty($data['title'])) {
            $errors[] = 'Judul kegiatan wajib diisi.';
        }

        if (empty($data['description'])) {
            $errors[] = 'Deskripsi kegiatan wajib diisi.';
        }

        if (empty($data['event_date'])) {
            $errors[] = 'Tanggal kegiatan wajib diisi.';
        }

        $validStatuses = ['draft', 'published'];
        if (empty($data['status']) || !in_array($data['status'], $validStatuses)) {
            $errors[] = 'Status tidak valid. Pilih: draft atau published.';
        }

        return $errors;
    }
}
