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

    /** Tipe file yang diizinkan (MIME hasil deteksi isi file => ekstensi yang disimpan) */
    private const IMAGE_TYPES = [
        'image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif',
        'image/svg+xml' => 'svg', 'image/webp' => 'webp',
    ];
    private const VIDEO_TYPES = ['video/mp4' => 'mp4', 'video/webm' => 'webm'];
    private const VIDEO_MAX_SIZE = 20 * 1024 * 1024; // 20MB

    /** ID tab di halaman pengaturan (lihat views/admin/settings/index.php) */
    private const TABS = ['general', 'hero', 'contact', 'donation', 'social', 'about'];

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
            'activeTab' => $this->validTab($this->get('tab')),
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

        // Jika total upload melebihi post_max_size, PHP mengosongkan $_POST (termasuk token CSRF)
        if (empty($_POST) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
            setFlash('error', 'Ukuran file yang diupload terlalu besar. Maksimal video ' . round(self::VIDEO_MAX_SIZE / 1024 / 1024) . 'MB.');
            $this->redirect('admin/pengaturan');
            return;
        }

        $this->validateCsrf();

        // Handle file uploads
        $logoFilename = $this->handleFileUpload('site_logo', 'logo');
        $heroFilename = $this->handleFileUpload('hero_image', 'hero');
        $aboutFilename = $this->handleFileUpload('about_image', 'about');
        $heroVideoFilename = $this->handleFileUpload('hero_video', 'hero_video', self::VIDEO_TYPES, self::VIDEO_MAX_SIZE);

        // Ambil semua data dari form
        $data = [
            // General
            'site_name' => $this->post('site_name') ?? '',
            'site_tagline' => $this->post('site_tagline') ?? '',
            'site_description' => $this->post('site_description') ?? '',
            'site_keywords' => $this->normalizeKeywords($this->post('site_keywords') ?? ''),
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

        // Warna tema: hanya terima format #rrggbb
        $themeColor = strtolower(trim($this->post('theme_color') ?? ''));
        if (preg_match('/^#[0-9a-f]{6}$/', $themeColor)) {
            $data['theme_color'] = $themeColor;
        }

        // Video latar hero (opsional)
        if ($heroVideoFilename) {
            $data['hero_video'] = $heroVideoFilename;
        } elseif ($this->post('hero_video_remove')) {
            $this->deleteUploadedFile(Setting::get('hero_video'));
            $data['hero_video'] = '';
        }

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

        // Kembali ke tab yang sedang dibuka saat menyimpan
        $this->redirect('admin/pengaturan?tab=' . $this->validTab($this->post('active_tab')));
    }

    /**
     * Rapikan keyword SEO: pisah koma, trim, buang duplikat & kosong,
     * maks 30 keyword @ 50 karakter. Hasil: "a, b, c"
     */
    private function normalizeKeywords(string $keywords): string
    {
        $result = [];
        foreach (explode(',', $keywords) as $keyword) {
            $keyword = mb_substr(trim(preg_replace('/\s+/u', ' ', $keyword)), 0, 50);
            $key = mb_strtolower($keyword);
            if ($keyword !== '' && !isset($result[$key])) {
                $result[$key] = $keyword;
            }
        }

        return implode(', ', array_slice(array_values($result), 0, 30));
    }

    /**
     * Pastikan tab ada di daftar, jika tidak kembali ke tab "Umum"
     */
    private function validTab($tab): string
    {
        return in_array($tab, self::TABS, true) ? $tab : 'general';
    }

    /**
     * Handle file upload
     * 
     * @param string $fieldName Nama field di form
     * @param string $prefix Prefix untuk nama file
     * @param array $allowedTypes MIME => ekstensi yang diizinkan
     * @param int $maxSize Ukuran maksimal (byte)
     * @return string|null Nama file atau null jika tidak ada upload
     */
    private function handleFileUpload(string $fieldName, string $prefix, array $allowedTypes = self::IMAGE_TYPES, int $maxSize = 5 * 1024 * 1024): ?string
    {
        if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $file = $_FILES[$fieldName];
        $maxMb = round($maxSize / 1024 / 1024);

        if (in_array($file['error'], [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)) {
            setFlash('error', "Ukuran file terlalu besar. Maksimal {$maxMb}MB.");
            return null;
        }
        if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
            setFlash('error', 'Gagal mengupload file.');
            return null;
        }

        // Validasi ukuran
        if ($file['size'] > $maxSize) {
            setFlash('error', "Ukuran file terlalu besar. Maksimal {$maxMb}MB.");
            return null;
        }

        // Validasi tipe dari ISI file (bukan dari $_FILES['type'] / nama file yang dikirim browser),
        // dan ekstensi ditentukan server agar file seperti "shell.php" tidak bisa tersimpan
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        if (!isset($allowedTypes[$mime])) {
            $allowed = strtoupper(implode(', ', array_unique($allowedTypes)));
            setFlash('error', "Tipe file tidak diizinkan. Gunakan {$allowed}.");
            return null;
        }

        // Generate nama file unik
        $filename = $prefix . '_' . time() . '.' . $allowedTypes[$mime];

        // Pindahkan file
        if (!move_uploaded_file($file['tmp_name'], $this->uploadPath . $filename)) {
            setFlash('error', 'Gagal mengupload file.');
            return null;
        }

        // Hapus file lama setelah file baru berhasil disimpan
        $this->deleteUploadedFile(Setting::get($fieldName));

        return $filename;
    }

    /**
     * Hapus file lama di folder upload pengaturan
     */
    private function deleteUploadedFile(?string $filename): void
    {
        $filename = basename((string) $filename);
        if ($filename !== '' && is_file($this->uploadPath . $filename)) {
            unlink($this->uploadPath . $filename);
        }
    }
}
