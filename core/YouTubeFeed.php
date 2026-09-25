<?php
namespace Core;

/**
 * =========================================================
 * YouTube Feed
 * =========================================================
 *
 * Mengambil video terbaru sebuah channel YouTube tanpa API key,
 * lewat feed RSS publik: youtube.com/feeds/videos.xml?channel_id=UC...
 *
 * Link channel boleh berbentuk:
 *   https://www.youtube.com/@handle
 *   https://www.youtube.com/channel/UCxxxxxxxxxxxxxxxxxxxxxx
 *   https://www.youtube.com/c/nama  |  https://www.youtube.com/user/nama
 *
 * Hasil disimpan ke file cache agar homepage tidak menghubungi
 * YouTube di setiap kunjungan. Jika YouTube gagal diakses, cache
 * lama tetap dipakai.
 */
class YouTubeFeed
{
    private const CACHE_TTL = 3 * 3600;   // Muat ulang tiap 3 jam
    private const RETRY_TTL = 5 * 60;     // Jika gagal, coba lagi setelah 5 menit
    private const TIMEOUT = 5;            // Detik, agar homepage tidak menggantung
    private const FEED_ATTEMPTS = 4;

    /**
     * Ambil video terbaru (tanpa Shorts)
     * @param string $channelUrl Link channel dari pengaturan
     * @param int $limit Jumlah video
     * @return array Daftar video: id, title, url, thumbnail, published, views
     */
    public static function latest(string $channelUrl, int $limit = 5): array
    {
        $channelUrl = trim($channelUrl);
        if ($channelUrl === '' || !preg_match('~^https?://(www\.|m\.)?youtube\.com/~i', $channelUrl)) {
            return [];
        }

        $cacheFile = self::cacheDir() . 'youtube_' . md5($channelUrl) . '.json';
        $cache = is_file($cacheFile) ? json_decode((string) file_get_contents($cacheFile), true) : null;

        $age = is_array($cache) ? time() - ($cache['fetched_at'] ?? 0) : PHP_INT_MAX;
        $ttl = !empty($cache['failed']) ? self::RETRY_TTL : self::CACHE_TTL;

        if ($age < $ttl) {
            return array_slice($cache['videos'] ?? [], 0, $limit);
        }

        $channelId = $cache['channel_id'] ?? self::resolveChannelId($channelUrl);
        $videos = $channelId ? self::fetchFeed($channelId) : null;

        if ($videos === null) {
            // Gagal: pakai data lama (jika ada) dan tandai untuk dicoba lagi nanti
            $cache = [
                'channel_id' => $channelId,
                'videos' => $cache['videos'] ?? [],
                'fetched_at' => time(),
                'failed' => true,
            ];
        } else {
            $cache = ['channel_id' => $channelId, 'videos' => $videos, 'fetched_at' => time()];
        }

        @file_put_contents($cacheFile, json_encode($cache), LOCK_EX);

        return array_slice($cache['videos'], 0, $limit);
    }

    /**
     * Cari channel ID (UC...) dari link channel
     */
    private static function resolveChannelId(string $url): ?string
    {
        if (preg_match('~/channel/(UC[\w-]{22})~', $url, $m)) {
            return $m[1];
        }

        $html = self::httpGet($url);
        if ($html === null) {
            return null;
        }

        $patterns = [
            '~<link rel="canonical" href="https://www\.youtube\.com/channel/(UC[\w-]{22})"~',
            '~"externalId":"(UC[\w-]{22})"~',
            '~<meta itemprop="identifier" content="(UC[\w-]{22})"~',
            '~"channelId":"(UC[\w-]{22})"~',
        ];
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $html, $m)) {
                return $m[1];
            }
        }

        return null;
    }

    /**
     * Baca feed RSS channel
     * @return array|null null jika gagal
     */
    private static function fetchFeed(string $channelId): ?array
    {
        // Feed RSS YouTube kadang membalas 404/500 secara acak, jadi coba beberapa kali
        $url = 'https://www.youtube.com/feeds/videos.xml?channel_id=' . urlencode($channelId);
        $start = microtime(true);
        $xml = null;
        for ($try = 0; $try < self::FEED_ATTEMPTS && $xml === null; $try++) {
            if ($try > 0) {
                if (microtime(true) - $start > self::TIMEOUT) {
                    break;
                }
                usleep(300000);
            }
            $xml = self::httpGet($url);
        }
        if ($xml === null) {
            return null;
        }

        $prev = libxml_use_internal_errors(true);
        $feed = simplexml_load_string($xml);
        libxml_use_internal_errors($prev);
        if ($feed === false) {
            return null;
        }

        $videos = [];
        foreach ($feed->entry as $entry) {
            $link = (string) $entry->link['href'];

            // Lewati Shorts
            if (strpos($link, '/shorts/') !== false) {
                continue;
            }

            $yt = $entry->children('http://www.youtube.com/xml/schemas/2015');
            $media = $entry->children('http://search.yahoo.com/mrss/');
            $id = (string) $yt->videoId;
            if ($id === '') {
                continue;
            }

            $views = null;
            if (isset($media->group->community->statistics)) {
                $views = (int) $media->group->community->statistics->attributes()['views'];
            }

            $videos[] = [
                'id' => $id,
                'title' => (string) $entry->title,
                'url' => 'https://www.youtube.com/watch?v=' . $id,
                'thumbnail' => 'https://i.ytimg.com/vi/' . $id . '/hqdefault.jpg',
                'published' => (string) $entry->published,
                'views' => $views,
            ];
        }

        return $videos;
    }

    private static function httpGet(string $url): ?string
    {
        $headers = [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0 Safari/537.36',
            'Accept-Language: id,en;q=0.8',
            'Cookie: CONSENT=YES+1', // Lewati halaman persetujuan cookie YouTube
        ];

        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS => 3,
                CURLOPT_CONNECTTIMEOUT => self::TIMEOUT,
                CURLOPT_TIMEOUT => self::TIMEOUT,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_ENCODING => '',
            ]);
            $body = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            return ($body !== false && $status === 200) ? $body : null;
        }

        $context = stream_context_create(['http' => [
            'timeout' => self::TIMEOUT,
            'header' => implode("\r\n", $headers),
            'ignore_errors' => false,
        ]]);
        $body = @file_get_contents($url, false, $context);

        return $body === false ? null : $body;
    }

    private static function cacheDir(): string
    {
        $dir = BASE_PATH . 'storage' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR;
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        return is_writable($dir) ? $dir : sys_get_temp_dir() . DIRECTORY_SEPARATOR;
    }
}
