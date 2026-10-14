<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\Article;
use App\Models\Event;

/**
 * =========================================================
 * SEO Controller
 * =========================================================
 *
 * sitemap.xml & robots.txt untuk mesin pencari (Google, Bing, dll).
 * Dibuat dinamis agar domain mengikuti APP_URL dan daftar halaman
 * selalu sesuai artikel & kegiatan yang sudah terbit.
 */
class SeoController extends Controller
{
    protected $layout = null;

    /**
     * Halaman statis publik: route => [changefreq, priority]
     */
    private const STATIC_PAGES = [
        ''                   => ['daily', '1.0'],
        'jadwal-pengkhotbah' => ['weekly', '0.8'],
        'kegiatan'           => ['daily', '0.8'],
        'artikel'            => ['daily', '0.8'],
        'tentang'            => ['monthly', '0.6'],
        'tentang/visi-misi'  => ['yearly', '0.5'],
        'tentang/sejarah'    => ['yearly', '0.5'],
        'menabur'             => ['monthly', '0.5'],
        'kontak'             => ['yearly', '0.5'],
    ];

    /**
     * sitemap.xml
     */
    public function sitemap(): void
    {
        $urls = [];
        foreach (self::STATIC_PAGES as $route => [$freq, $priority]) {
            $urls[] = ['loc' => url($route), 'changefreq' => $freq, 'priority' => $priority];
        }

        foreach ((new Article())->getForSitemap() as $row) {
            $urls[] = ['loc' => url('artikel/' . $row['slug']), 'lastmod' => $row['lastmod'], 'priority' => '0.7'];
        }

        foreach ((new Event())->getForSitemap() as $row) {
            $urls[] = ['loc' => url('kegiatan/' . $row['slug']), 'lastmod' => $row['lastmod'], 'priority' => '0.6'];
        }

        header('Content-Type: application/xml; charset=utf-8');
        header('X-Robots-Tag: noindex');

        $x = fn($v) => htmlspecialchars((string) $v, ENT_XML1 | ENT_QUOTES, 'UTF-8');

        echo '<?xml version="1.0" encoding="UTF-8"?>', "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', "\n";
        foreach ($urls as $u) {
            echo "  <url>\n    <loc>", $x($u['loc']), "</loc>\n";
            if (!empty($u['lastmod'])) {
                echo '    <lastmod>', $x(date('c', strtotime($u['lastmod']))), "</lastmod>\n";
            }
            if (!empty($u['changefreq'])) {
                echo '    <changefreq>', $u['changefreq'], "</changefreq>\n";
            }
            echo '    <priority>', $u['priority'], "</priority>\n  </url>\n";
        }
        echo "</urlset>\n";
    }

    /**
     * robots.txt
     * Catatan: mesin pencari hanya membaca robots.txt di root domain,
     * jadi ini efektif jika website dipasang di root (bukan subfolder).
     */
    public function robots(): void
    {
        $base = rtrim((string) parse_url(APP_URL, PHP_URL_PATH), '/');

        header('Content-Type: text/plain; charset=utf-8');

        echo "User-agent: *\n";
        echo "Disallow: {$base}/admin\n";
        echo "Disallow: {$base}/auth\n";
        echo "Allow: {$base}/\n\n";
        echo 'Sitemap: ', url('sitemap.xml'), "\n";
    }
}
