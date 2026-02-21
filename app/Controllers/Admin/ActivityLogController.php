<?php
namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\ActivityLog;
use App\Middleware\RoleMiddleware;

/**
 * =========================================================
 * Admin Activity Log Controller
 * =========================================================
 * 
 * Controller untuk melihat log aktivitas (SUPER ADMIN ONLY)
 */
class ActivityLogController extends Controller
{
    protected $layout = 'admin';
    private $logModel;

    public function __construct()
    {
        // Hanya Super Admin yang boleh akses
        RoleMiddleware::requireSuperAdmin();
        $this->logModel = new ActivityLog();
    }

    /**
     * Daftar log aktivitas
     */
    public function index(): void
    {
        $page = (int) ($this->get('page') ?? 1);
        $userId = $this->get('user_id') ? (int) $this->get('user_id') : null;

        $logs = $this->logModel->getWithPagination($page, ITEMS_PER_PAGE, $userId);
        $totalLogs = $this->logModel->count();

        $this->view('admin/logs/index', [
            'title' => 'Log Aktivitas - ' . APP_NAME,
            'logs' => $logs['data'],
            'pagination' => $logs,
            'filteredUserId' => $userId,
            'totalLogs' => $totalLogs,
        ]);
    }

    /**
     * Hapus SEMUA log aktivitas
     */
    public function clear(): void
    {
        if (!$this->isPost()) {
            $this->redirect('admin/logs');
            return;
        }

        $this->validateCsrf();

        $deleted = $this->logModel->clearAll();
        setFlash('success', "Berhasil menghapus {$deleted} log aktivitas.");
        $this->redirect('admin/logs');
    }

    /**
     * Hapus log aktivitas lebih dari 30 hari
     */
    public function clearOld(): void
    {
        if (!$this->isPost()) {
            $this->redirect('admin/logs');
            return;
        }

        $this->validateCsrf();

        $days = (int) ($this->post('days') ?? 30);
        $days = max(1, min(365, $days)); // clamp 1-365
        $deleted = $this->logModel->deleteOlderThan($days);
        setFlash('success', "Berhasil menghapus {$deleted} log aktivitas yang lebih dari {$days} hari.");
        $this->redirect('admin/logs');
    }
}
