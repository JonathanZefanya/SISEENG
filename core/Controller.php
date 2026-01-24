<?php
namespace Core;

/**
 * =========================================================
 * Base Controller Class
 * =========================================================
 * 
 * Controller dasar yang diextend oleh semua controller
 * Menyediakan method umum seperti rendering view, redirect, dll
 */
abstract class Controller
{
    /**
     * Data yang akan dikirim ke view
     * @var array
     */
    protected $data = [];
    
    /**
     * Layout yang digunakan
     * @var string
     */
    protected $layout = 'main';
    
    /**
     * Render view dengan layout
     * 
     * @param string $view Path ke view (relatif dari views/)
     * @param array $data Data yang dikirim ke view
     * @param string|null $layout Layout yang digunakan (null = tanpa layout)
     */
    protected function view(string $view, array $data = [], ?string $layout = null): void
    {
        // Gabungkan data
        $data = array_merge($this->data, $data);
        
        // Extract data menjadi variabel
        extract($data);
        
        // Mulai output buffering
        ob_start();
        
        // Include view file
        $viewPath = VIEW_PATH . str_replace('.', '/', $view) . '.php';
        
        if (!file_exists($viewPath)) {
            throw new \Exception("View file not found: {$viewPath}");
        }
        
        require $viewPath;
        
        // Ambil content
        $content = ob_get_clean();
        
        // Jika menggunakan layout
        $layoutToUse = $layout ?? $this->layout;
        
        if ($layoutToUse) {
            $layoutPath = VIEW_PATH . 'layouts/' . $layoutToUse . '.php';
            
            if (file_exists($layoutPath)) {
                require $layoutPath;
            } else {
                echo $content;
            }
        } else {
            echo $content;
        }
    }
    
    /**
     * Render view tanpa layout
     * 
     * @param string $view
     * @param array $data
     */
    protected function partial(string $view, array $data = []): void
    {
        $this->view($view, $data, null);
    }
    
    /**
     * Set data untuk view
     * 
     * @param string|array $key
     * @param mixed $value
     */
    protected function setData($key, $value = null): void
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }
    }
    
    /**
     * Redirect ke URL lain
     * 
     * @param string $url
     */
    protected function redirect(string $url): void
    {
        redirect($url);
    }
    
    /**
     * Redirect kembali ke halaman sebelumnya
     */
    protected function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? APP_URL;
        header("Location: {$referer}");
        exit;
    }
    
    /**
     * Response JSON
     * 
     * @param mixed $data
     * @param int $statusCode
     */
    protected function json($data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Validasi CSRF token
     * Jika tidak valid, redirect dengan pesan error
     */
    protected function validateCsrf(): void
    {
        if (!Security::verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid. Silakan coba lagi.');
            $this->back();
        }
    }
    
    /**
     * Cek apakah request adalah POST
     * 
     * @return bool
     */
    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    /**
     * Cek apakah request adalah GET
     * 
     * @return bool
     */
    protected function isGet(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }
    
    /**
     * Cek apakah request adalah AJAX
     * 
     * @return bool
     */
    protected function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    /**
     * Dapatkan data POST yang sudah di-sanitasi
     * 
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    protected function post(?string $key = null, $default = null)
    {
        $data = Security::cleanInput($_POST);
        
        if ($key === null) {
            return $data;
        }
        
        return $data[$key] ?? $default;
    }
    
    /**
     * Dapatkan data GET yang sudah di-sanitasi
     * 
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    protected function get(?string $key = null, $default = null)
    {
        $data = Security::cleanInput($_GET);
        
        if ($key === null) {
            return $data;
        }
        
        return $data[$key] ?? $default;
    }
    
    /**
     * Simpan old input untuk repopulate form
     */
    protected function saveOldInput(): void
    {
        Session::set('old_input', $_POST);
    }
    
    /**
     * Hapus old input
     */
    protected function clearOldInput(): void
    {
        Session::remove('old_input');
    }
}
