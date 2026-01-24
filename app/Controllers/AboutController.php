<?php
namespace App\Controllers;

use Core\Controller;

/**
 * =========================================================
 * About Controller
 * =========================================================
 * 
 * Controller untuk halaman Tentang Kami
 */
class AboutController extends Controller
{
    protected $layout = 'public';
    
    /**
     * Halaman Tentang Kami utama
     */
    public function index(): void
    {
        $this->view('public/about/index', [
            'title' => 'Tentang Kami - ' . APP_NAME,
        ]);
    }
    
    /**
     * Halaman Visi Misi
     */
    public function visiMisi(): void
    {
        $this->view('public/about/visi-misi', [
            'title' => 'Visi & Misi - ' . APP_NAME,
        ]);
    }
    
    /**
     * Halaman Sejarah
     */
    public function sejarah(): void
    {
        $this->view('public/about/sejarah', [
            'title' => 'Sejarah Gereja - ' . APP_NAME,
        ]);
    }
}
