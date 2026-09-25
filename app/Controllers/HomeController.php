<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\Schedule;
use App\Models\Article;
use App\Models\Event;
use App\Models\PreacherSchedule;
use Core\YouTubeFeed;

/**
 * =========================================================
 * Home Controller
 * =========================================================
 * 
 * Controller untuk halaman depan/publik
 */
class HomeController extends Controller
{
    protected $layout = 'public';
    
    /**
     * Homepage
     */
    public function index(): void
    {
        $scheduleModel = new Schedule();
        $articleModel = new Article();
        $eventModel = new Event();
        $preacherModel = new PreacherSchedule();
        
        // Ambil pengkhotbah untuk minggu ini (hari Minggu terdekat)
        $nextSunday = $this->getNextSunday();
        $preachersThisWeek = $preacherModel->getByDate($nextSunday);
        
        // Konversi ke array dengan key service_time untuk lookup mudah
        $preachersByTime = [];
        foreach ($preachersThisWeek as $p) {
            $timeKey = $p['service_time'];
            $preachersByTime[$timeKey] = $p;
        }
        
        $this->view('public/home', [
            'title' => 'Selamat Datang - ' . APP_NAME,
            'schedules' => $scheduleModel->getActiveSchedules(),
            'articles' => $articleModel->getPublished(3),
            'events' => $eventModel->getUpcoming(3),
            'preachersByTime' => $preachersByTime,
            'nextSunday' => $nextSunday,
            // Kosong jika link YouTube belum diisi di Pengaturan -> section disembunyikan
            'videos' => YouTubeFeed::latest(setting('site_youtube'), 5),
            'youtubeUrl' => setting('site_youtube'),
        ]);
    }
    
    /**
     * Get next Sunday date (including today if it's Sunday)
     */
    private function getNextSunday(): string
    {
        $today = new \DateTime();
        $dayOfWeek = (int) $today->format('w'); // 0 = Sunday
        
        if ($dayOfWeek === 0) {
            return $today->format('Y-m-d');
        }
        
        $daysUntilSunday = 7 - $dayOfWeek;
        $today->modify("+{$daysUntilSunday} days");
        return $today->format('Y-m-d');
    }
    
    /**
     * Halaman jadwal pengkhotbah bulanan
     */
    public function preacherSchedule(): void
    {
        $preacherModel = new PreacherSchedule();
        
        // Ambil parameter bulan dari URL atau gunakan bulan ini
        $monthParam = $this->get('month');
        
        if ($monthParam && preg_match('/^\d{4}-\d{2}$/', $monthParam)) {
            $year = (int) substr($monthParam, 0, 4);
            $month = (int) substr($monthParam, 5, 2);
        } else {
            $month = (int) date('n');
            $year = (int) date('Y');
        }
        
        // Validasi bulan dan tahun
        if ($month < 1 || $month > 12) {
            $month = (int) date('n');
        }
        if ($year < 2020 || $year > 2030) {
            $year = (int) date('Y');
        }
        
        // Ambil jadwal bulanan (dikelompokkan per tanggal)
        $schedules = $preacherModel->getByMonthGrouped($month, $year);
        
        // Hitung bulan sebelum dan sesudah untuk navigasi
        $prevDate = new \DateTime("$year-$month-01");
        $prevDate->modify('-1 month');
        $prevMonth = $prevDate->format('Y-m');
        
        $nextDate = new \DateTime("$year-$month-01");
        $nextDate->modify('+1 month');
        $nextMonth = $nextDate->format('Y-m');
        
        $this->view('public/preacher-schedule', [
            'title' => 'Jadwal Pengkhotbah ' . getMonthName($month) . ' ' . $year . ' - ' . APP_NAME,
            'schedules' => $schedules,
            'month' => $month,
            'year' => $year,
            'monthName' => getMonthName($month),
            'prevMonth' => $prevMonth,
            'nextMonth' => $nextMonth,
        ]);
    }
}
