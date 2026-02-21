<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\Event;

/**
 * =========================================================
 * Event Controller (Public)
 * =========================================================
 * 
 * Controller untuk halaman kegiatan publik
 */
class EventController extends Controller
{
    protected $layout = 'public';
    private $eventModel;

    public function __construct()
    {
        $this->eventModel = new Event();
    }

    /**
     * Daftar semua kegiatan (hanya yang published)
     */
    public function index(): void
    {
        $page = (int) ($this->get('page') ?? 1);
        $events = $this->eventModel->getPublished($page);

        $this->view('public/events/index', [
            'title' => 'Kegiatan - ' . APP_NAME,
            'events' => $events['data'],
            'pagination' => $events,
        ]);
    }

    /**
     * Detail kegiatan
     * 
     * @param string $param ID atau slug kegiatan
     */
    public function detail(string $param = ''): void
    {
        if (empty($param)) {
            $this->redirect('kegiatan');
            return;
        }

        // Coba cari berdasarkan ID atau slug
        if (is_numeric($param)) {
            $event = $this->eventModel->find((int) $param);
        } else {
            $event = $this->eventModel->findBySlug($param);
        }

        // Tidak ditemukan atau masih draft -> tampilkan 404
        if (!$event || $event['status'] !== 'published') {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'Tidak Ditemukan']);
            return;
        }

        $this->view('public/events/detail', [
            'title' => e($event['title']) . ' - ' . APP_NAME,
            'event' => $event,
        ]);
    }
}
