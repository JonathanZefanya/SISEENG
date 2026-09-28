<?php
namespace Core;

/**
 * =========================================================
 * HtmlSanitizer Class
 * =========================================================
 *
 * Membersihkan HTML dari rich text editor dengan sistem whitelist.
 * Semua tag/atribut yang tidak dikenal dibuang, sehingga konten
 * aman ditampilkan tanpa di-escape (mencegah stored XSS).
 *
 * - Tag berbahaya (script, iframe, style, dll) dibuang beserta isinya
 * - Tag lain yang tidak di-whitelist dibuka (isi teksnya tetap)
 * - Semua atribut dibuang, kecuali href pada <a> dan class perataan teks
 * - href hanya boleh http(s), mailto, tel, atau URL relatif
 */
class HtmlSanitizer
{
    /** Tag yang diizinkan */
    private const ALLOWED_TAGS = [
        'p', 'br', 'strong', 'b', 'em', 'i', 'u', 's',
        'h2', 'h3', 'h4', 'blockquote', 'ul', 'ol', 'li', 'a',
    ];

    /** Tag yang dibuang beserta seluruh isinya */
    private const DROP_TAGS = [
        'script', 'style', 'iframe', 'frame', 'frameset', 'object', 'embed',
        'applet', 'svg', 'math', 'template', 'noscript', 'textarea', 'select',
        'option', 'button', 'input', 'form', 'head', 'title', 'meta', 'link',
        'base', 'video', 'audio', 'source', 'track', 'canvas', 'img', 'picture',
    ];

    /** Tag yang boleh memiliki class perataan teks */
    private const ALIGNABLE_TAGS = ['p', 'h2', 'h3', 'h4', 'li', 'blockquote'];

    /** Skema URL yang diizinkan pada href */
    private const ALLOWED_SCHEMES = ['http', 'https', 'mailto', 'tel'];

    /**
     * Bersihkan HTML
     * @param string|null $html HTML mentah dari editor
     * @return string HTML yang aman
     */
    public static function clean(?string $html): string
    {
        $html = trim(str_replace(chr(0), '', (string) $html));
        if ($html === '') {
            return '';
        }

        $doc = new \DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $doc->loadHTML(
            '<!DOCTYPE html><html><head><meta charset="utf-8"></head><body><div>' . $html . '</div></body></html>',
            LIBXML_NONET
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $doc->getElementsByTagName('body')->item(0)?->firstChild;
        if (!$root) {
            return '';
        }

        self::cleanChildren($root);

        $output = '';
        foreach ($root->childNodes as $child) {
            $output .= $doc->saveHTML($child);
        }

        return trim($output);
    }

    /**
     * Cek apakah HTML tidak memiliki teks yang terlihat (mis. "<p><br></p>")
     * @param string|null $html
     * @return bool
     */
    public static function isEmpty(?string $html): bool
    {
        $text = html_entity_decode(strip_tags((string) $html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return trim(str_replace("\u{00A0}", ' ', $text)) === '';
    }

    /**
     * Bersihkan seluruh anak dari sebuah node secara rekursif
     */
    private static function cleanChildren(\DOMNode $parent): void
    {
        // Salin dulu karena childNodes berubah saat node dihapus/dipindah
        foreach (iterator_to_array($parent->childNodes) as $node) {
            if ($node instanceof \DOMText && !($node instanceof \DOMCdataSection)) {
                continue;
            }

            if (!($node instanceof \DOMElement)) {
                // Komentar, CDATA, processing instruction, dll
                $parent->removeChild($node);
                continue;
            }

            $tag = strtolower($node->nodeName);

            if (in_array($tag, self::DROP_TAGS, true)) {
                $parent->removeChild($node);
                continue;
            }

            self::cleanChildren($node);

            if (!in_array($tag, self::ALLOWED_TAGS, true)) {
                // Buka tag: pindahkan isinya ke parent, lalu hapus tag-nya
                while ($node->firstChild) {
                    $parent->insertBefore($node->firstChild, $node);
                }
                $parent->removeChild($node);
                continue;
            }

            self::cleanAttributes($node, $tag);
        }
    }

    /**
     * Buang semua atribut kecuali yang di-whitelist
     */
    private static function cleanAttributes(\DOMElement $el, string $tag): void
    {
        $href = $el->getAttribute('href');
        $alignClass = self::extractAlignClass($el->getAttribute('class'));

        foreach (iterator_to_array($el->attributes) as $attr) {
            $el->removeAttribute($attr->nodeName);
        }

        if ($tag === 'a') {
            $safeHref = self::sanitizeUrl($href);
            if ($safeHref !== null) {
                $el->setAttribute('href', $safeHref);
                $el->setAttribute('target', '_blank');
                $el->setAttribute('rel', 'noopener noreferrer nofollow');
            }
        }

        if ($alignClass !== null && in_array($tag, self::ALIGNABLE_TAGS, true)) {
            $el->setAttribute('class', $alignClass);
        }
    }

    /**
     * Ambil class perataan teks dari Quill (ql-align-center/right/justify)
     */
    private static function extractAlignClass(string $class): ?string
    {
        if (preg_match('/(?:^|\s)(ql-align-(?:center|right|justify))(?:\s|$)/', $class, $m)) {
            return $m[1];
        }
        return null;
    }

    /**
     * Validasi URL: hanya skema aman atau URL relatif
     * @return string|null URL yang aman, atau null jika ditolak
     */
    private static function sanitizeUrl(string $url): ?string
    {
        // Browser mengabaikan whitespace & karakter kontrol di dalam skema
        // (mis. "java\tscript:"), jadi buang sebelum memeriksa skema
        $url = trim($url);
        $check = preg_replace('/[\x00-\x20\x7F]+/', '', $url);

        if ($check === '') {
            return null;
        }

        if (preg_match('/^([a-z][a-z0-9+.\-]*):/i', $check, $m)) {
            if (!in_array(strtolower($m[1]), self::ALLOWED_SCHEMES, true)) {
                return null;
            }
        } elseif (str_contains(strtolower(explode('/', $check, 2)[0]), ':')) {
            // Bentuk aneh lain yang mengandung ":" sebelum path
            return null;
        }

        return $url;
    }
}
