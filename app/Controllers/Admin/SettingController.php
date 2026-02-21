<?php
namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\Setting;
use App\Middleware\RoleMiddleware;

/**
 * =========================================================
 * Setting Controller (Admin)
 * =========================================================
 * 
 * Mengelola pengaturan website
 */
class SettingController extends Controller
{
    protected $layout = 'admin';
    private $settingModel;
    private $uploadPath;

    public function __construct()
    {
        RoleMiddleware::requireAdmin();
        $this->settingModel = new Setting();
        $this->uploadPath = PUBLIC_PATH . 'uploads' . DIRECTORY_SEPARATOR . 'settings' . DIRECTORY_SEPARATOR;

        // Buat folder upload jika belum ada
        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }
    }

    /**
     * Halaman pengaturan umum
     */
    public function index(): void
    {
        $settings = Setting::getAllAsArray();

        $this->view('admin/settings/index', [
            'title' => 'Pengaturan Website - ' . APP_NAME,
            'settings' => $settings,
        ]);
    }

    /**
     * Update pengaturan
     */
    public function update(): void
    {
        if (!$this->isPost()) {
            $this->redirect('admin/pengaturan');
            return;
        }

        $this->validateCsrf();

        // Handle file uploads
        $logoFilename = $this->handleFileUpload('site_logo', 'logo');
        $heroFilename = $this->handleFileUpload('hero_image', 'hero');
        $aboutFilename = $this->handleFileUpload('about_image', 'about');

        // Ambil semua data dari form
        $data = [
            // General
            'site_name' => $this->post('site_name') ?? '',
            'site_tagline' => $this->post('site_tagline') ?? '',
            'site_description' => $this->post('site_description') ?? '',
            'site_email' => $this->post('site_email') ?? '',
            'site_phone' => $this->post('site_phone') ?? '',
            'site_whatsapp' => $this->post('site_whatsapp') ?? '',
            'site_address' => $this->post('site_address') ?? '',
            'site_operational_hours' => $this->post('site_operational_hours') ?? '',
            'site_gmaps_embed' => $this->post('site_gmaps_embed') ?? '',

            // Hero Section
            'hero_title' => $this->post('hero_title') ?? '',
            'hero_subtitle' => $this->post('hero_subtitle') ?? '',
            'hero_verse' => $this->post('hero_verse') ?? '',
            'hero_verse_ref' => $this->post('hero_verse_ref') ?? '',

            // Social Media
            'site_facebook' => $this->post('site_facebook') ?? '',
            'site_instagram' => $this->post('site_instagram') ?? '',
            'site_youtube' => $this->post('site_youtube') ?? '',
            'site_tiktok' => $this->post('site_tiktok') ?? '',

            // Donation
            'donation_title' => $this->post('donation_title') ?? '',
            'donation_description' => $this->post('donation_description') ?? '',
            'donation_bank_name' => $this->post('donation_bank_name') ?? '',
            'donation_bank_account' => $this->post('donation_bank_account') ?? '',
            'donation_account_name' => $this->post('donation_account_name') ?? '',
            'donation_bank_name_2' => $this->post('donation_bank_name_2') ?? '',
            'donation_bank_account_2' => $this->post('donation_bank_account_2') ?? '',
            'donation_account_name_2' => $this->post('donation_account_name_2') ?? '',

            // About
            'about_vision' => $this->post('about_vision') ?? '',
            'about_mission' => $this->post('about_mission') ?? '',
            'about_history' => $this->post('about_history') ?? '',
            'about_pastor' => $this->post('about_pastor') ?? '',
        ];

        // Tambahkan file yang diupload jika ada
        if ($logoFilename) {
            $data['site_logo'] = $logoFilename;
        }
        if ($heroFilename) {
            $data['hero_image'] = $heroFilename;
        }
        if ($aboutFilename) {
            $data['about_image'] = $aboutFilename;
        }

        // Update ke database
        if ($this->settingModel->updateMultiple($data)) {
            setFlash('success', 'Pengaturan berhasil disimpan!');
        } else {
            setFlash('error', 'Gagal menyimpan pengaturan.');
        }

        $this->redirect('admin/pengaturan');
    }

    /**
     * Handle file upload
     * 
     * @param string $fieldName Nama field di form
     * @param string $prefix Prefix untuk nama file
     * @return string|null Nama file atau null jika tidak ada upload
     */
    private function handleFileUpload(string $fieldName, string $prefix): ?string
    {
        if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $file = $_FILES[$fieldName];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        // Validasi tipe file
        if (!in_array($file['type'], $allowedTypes)) {
            setFlash('error', 'Tipe file tidak diizinkan. Gunakan JPG, PNG, GIF, SVG, atau WebP.');
            return null;
        }

        // Validasi ukuran
        if ($file['size'] > $maxSize) {
            setFlash('error', 'Ukuran file terlalu besar. Maksimal 5MB.');
            return null;
        }

        // Generate nama file unik
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $prefix . '_' . time() . '.' . $extension;
        $destination = $this->uploadPath . $filename;

        // Hapus file lama jika ada
        $oldFile = Setting::get($fieldName);
        if ($oldFile && file_exists($this->uploadPath . $oldFile)) {
            unlink($this->uploadPath . $oldFile);
        }

        // Pindahkan file
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $filename;
        }

        setFlash('error', 'Gagal mengupload file.');
        return null;
    }
}
