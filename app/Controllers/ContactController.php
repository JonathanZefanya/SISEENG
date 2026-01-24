<?php
namespace App\Controllers;

use Core\Controller;
use Core\Security;
use App\Models\ContactMessage;

/**
 * =========================================================
 * Contact Controller
 * =========================================================
 * 
 * Controller untuk halaman kontak
 */
class ContactController extends Controller
{
    protected $layout = 'public';
    private $messageModel;
    
    public function __construct()
    {
        $this->messageModel = new ContactMessage();
    }
    
    /**
     * Halaman kontak
     */
    public function index(): void
    {
        $this->view('public/contact', [
            'title' => 'Hubungi Kami - ' . APP_NAME,
        ]);
    }
    
    /**
     * Proses kirim pesan
     */
    public function send(): void
    {
        if (!$this->isPost()) {
            $this->redirect('kontak');
            return;
        }
        
        // Validasi CSRF
        $this->validateCsrf();
        
        // Ambil dan validasi input
        $data = [
            'name' => $this->post('name'),
            'email' => $this->post('email'),
            'phone' => $this->post('phone'),
            'subject' => $this->post('subject'),
            'message' => $this->post('message'),
            'ip_address' => Security::getClientIp(),
        ];
        
        // Validasi sederhana
        $errors = [];
        
        if (empty($data['name'])) {
            $errors[] = 'Nama wajib diisi.';
        }
        
        if (empty($data['email']) || !Security::validateEmail($data['email'])) {
            $errors[] = 'Email tidak valid.';
        }
        
        if (empty($data['message'])) {
            $errors[] = 'Pesan wajib diisi.';
        }
        
        if (!empty($errors)) {
            setFlash('error', implode('<br>', $errors));
            $this->saveOldInput();
            $this->redirect('kontak');
            return;
        }
        
        // Simpan pesan
        try {
            $this->messageModel->create($data);
            setFlash('success', 'Pesan Anda telah terkirim. Terima kasih telah menghubungi kami!');
            $this->clearOldInput();
        } catch (\Exception $e) {
            // Log error untuk debugging
            error_log('Contact form error: ' . $e->getMessage());
            setFlash('error', 'Terjadi kesalahan. Silakan coba lagi. ' . (ENVIRONMENT === 'development' ? $e->getMessage() : ''));
            $this->saveOldInput();
        }
        
        $this->redirect('kontak');
    }
}
