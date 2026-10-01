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
        $when = (string) $this->get('waktu', '');
        if (!isset(Event::TIME_FILTERS[$when])) {
            $when = '';
        }
        $events = $this->eventModel->getPublished($page, ITEMS_PER_PAGE, $when);

        $this->view('public/events/index', [
            'title' => ($when ? Event::TIME_FILTERS[$when][0] . ' - ' : '') . 'Kegiatan - ' . APP_NAME,
            'events' => $events['data'],
            'pagination' => $events,
            'when' => $when,
            'timeCounts' => $this->eventModel->countPublishedByTime(),
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
            showError(404);
        }

        $this->view('public/events/detail', [
            'title' => e($event['title']) . ' - ' . APP_NAME,
            'event' => $event,
        ]);
    }
}
