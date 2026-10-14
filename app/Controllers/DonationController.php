<?php
namespace App\Controllers;

use Core\Controller;

/**
 * =========================================================
 * Donation Controller
 * =========================================================
 * 
 * Controller untuk halaman menabur
 */
class DonationController extends Controller
{
    protected $layout = 'public';
    
    /**
     * Halaman menabur
     */
    public function index(): void
    {
        $this->view('public/donation', [
            'title' => 'Menabur - ' . APP_NAME,
        ]);
    }
}
