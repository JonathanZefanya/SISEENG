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
        
        $this->view('admin/logs/index', [
            'title' => 'Log Aktivitas - ' . APP_NAME,
            'logs' => $logs['data'],
            'pagination' => $logs,
            'filteredUserId' => $userId,
        ]);
    }
}
