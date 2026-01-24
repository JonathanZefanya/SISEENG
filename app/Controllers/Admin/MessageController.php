<?php
namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\ContactMessage;
use App\Models\ActivityLog;
use App\Middleware\RoleMiddleware;

/**
 * =========================================================
 * Admin Message Controller
 * =========================================================
 * 
 * Controller untuk mengelola pesan masuk
 */
class MessageController extends Controller
{
    protected $layout = 'admin';
    private $messageModel;
    
    public function __construct()
    {
        RoleMiddleware::requireAdmin();
        $this->messageModel = new ContactMessage();
    }
    
    /**
     * Daftar pesan
     */
    public function index(): void
    {
        $page = (int) ($this->get('page') ?? 1);
        $filter = $this->get('filter'); // 'unread' atau null untuk semua
        
        $isRead = null;
        if ($filter === 'unread') {
            $isRead = false;
        }
        
        $messages = $this->messageModel->getWithPagination($page, ITEMS_PER_PAGE, $isRead);
        
        $this->view('admin/messages/index', [
            'title' => 'Pesan Masuk - ' . APP_NAME,
            'messages' => $messages['data'],
            'pagination' => $messages,
            'filter' => $filter,
            'unreadCount' => $this->messageModel->countUnread(),
        ]);
    }
    
    /**
     * Baca pesan detail
     */
    public function read(int $id = 0): void
    {
        $message = $this->messageModel->find($id);
        
        if (!$message) {
            setFlash('error', 'Pesan tidak ditemukan.');
            $this->redirect('admin/pesan');
            return;
        }
        
        // Tandai sebagai sudah dibaca
        if (!$message['is_read']) {
            $this->messageModel->markAsRead($id);
        }
        
        $this->view('admin/messages/read', [
            'title' => 'Detail Pesan - ' . APP_NAME,
            'message' => $message,
        ]);
    }
    
    /**
     * Hapus pesan
     */
    public function delete(int $id = 0): void
    {
        if (!$this->isPost()) {
            $this->redirect('admin/pesan');
            return;
        }
        
        $this->validateCsrf();
        
        $message = $this->messageModel->find($id);
        
        if (!$message) {
            setFlash('error', 'Pesan tidak ditemukan.');
            $this->redirect('admin/pesan');
            return;
        }
        
        try {
            $this->messageModel->delete($id);
            
            ActivityLog::log(auth('id'), 'delete_message', "Menghapus pesan dari: {$message['name']}");
            
            setFlash('success', 'Pesan berhasil dihapus.');
        } catch (\Exception $e) {
            setFlash('error', 'Gagal menghapus pesan.');
        }
        
        $this->redirect('admin/pesan');
    }
}
