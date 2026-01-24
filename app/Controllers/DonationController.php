<?php
namespace App\Controllers;

use Core\Controller;

/**
 * =========================================================
 * Donation Controller
 * =========================================================
 * 
 * Controller untuk halaman donasi
 */
class DonationController extends Controller
{
    protected $layout = 'public';
    
    /**
     * Halaman donasi
     */
    public function index(): void
    {
        $this->view('public/donation', [
            'title' => 'Donasi - ' . APP_NAME,
        ]);
    }
}
