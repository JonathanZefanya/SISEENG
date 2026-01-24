<?php
namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\User;
use App\Models\Article;
use App\Models\Member;
use App\Models\ContactMessage;
use App\Models\Event;
use App\Models\ActivityLog;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;

/**
 * =========================================================
 * Admin Dashboard Controller
 * =========================================================
 * 
 * Controller untuk dashboard admin
 */
class DashboardController extends Controller
{
    protected $layout = 'admin';
    
    public function __construct()
    {
        // Pastikan user sudah login dan adalah admin
        RoleMiddleware::requireAdmin();
    }
    
    /**
     * Dashboard utama
     */
    public function index(): void
    {
        // Ambil statistik
        $memberModel = new Member();
        $articleModel = new Article();
        $messageModel = new ContactMessage();
        $eventModel = new Event();
        
        $stats = [
            'total_members' => $memberModel->countActive(),
            'total_articles' => $articleModel->count(),
            'unread_messages' => $messageModel->countUnread(),
            'upcoming_events' => count($eventModel->getUpcoming(10)),
        ];
        
        // Ambil data untuk dashboard
        $recentMessages = $messageModel->getUnread();
        $upcomingEvents = $eventModel->getUpcoming(5);
        
        // Ambil log aktivitas terbaru (hanya untuk super admin)
        $recentLogs = [];
        if (isSuperAdmin()) {
            $logModel = new ActivityLog();
            $recentLogs = $logModel->getRecent(10);
        }
        
        $this->view('admin/dashboard', [
            'title' => 'Dashboard - ' . APP_NAME,
            'stats' => $stats,
            'recentMessages' => array_slice($recentMessages, 0, 5),
            'upcomingEvents' => $upcomingEvents,
            'recentLogs' => $recentLogs,
        ]);
    }
}
