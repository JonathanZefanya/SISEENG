<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\Setting;

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
     * Ambil semua setting about dari database
     */
    private function getAboutSettings(): array
    {
        try {
            $all = Setting::getAllAsArray();
        } catch (\Exception $e) {
            $all = [];
        }

        return [
            'image' => $all['about_image'] ?? '',
            'vision' => $all['about_vision'] ?? 'Menjadi gereja yang membawa transformasi bagi masyarakat melalui kasih Kristus.',
            'mission' => $all['about_mission'] ?? "Menyebarkan Injil kepada semua orang\nMembina jemaat dalam iman dan kasih\nMelayani sesama dengan tulus\nMembangun komunitas yang saling mendukung",
            'history' => $all['about_history'] ?? 'Gereja kami didirikan dengan visi untuk menjangkau masyarakat dan menjadi berkat bagi banyak orang.',
            'pastor' => $all['about_pastor'] ?? '',
            'description' => $all['site_description'] ?? '',
            'address' => $all['site_address'] ?? '',
            'phone' => $all['site_phone'] ?? '',
            'email' => $all['site_email'] ?? '',
            'whatsapp' => $all['site_whatsapp'] ?? '',
        ];
    }

    /**
     * Halaman Tentang Kami utama
     */
    public function index(): void
    {
        $this->view('public/about/index', [
            'title' => 'Tentang Kami - ' . APP_NAME,
            'about' => $this->getAboutSettings(),
        ]);
    }

    /**
     * Halaman Visi Misi
     */
    public function visiMisi(): void
    {
        $this->view('public/about/visi-misi', [
            'title' => 'Visi & Misi - ' . APP_NAME,
            'about' => $this->getAboutSettings(),
        ]);
    }

    /**
     * Halaman Sejarah
     */
    public function sejarah(): void
    {
        $this->view('public/about/sejarah', [
            'title' => 'Sejarah Gereja - ' . APP_NAME,
            'about' => $this->getAboutSettings(),
        ]);
    }
}
