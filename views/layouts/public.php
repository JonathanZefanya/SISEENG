<?php
$siteName = setting('site_name', APP_NAME);
$route = trim($_GET['url'] ?? '', '/');

// Cek menu aktif berdasarkan segmen URL pertama
$isActive = function (string $path) use ($route): bool {
    if ($path === '') {
        return $route === '' || $route === 'home';
    }
    return $route === $path || strpos($route, $path . '/') === 0;
};
$aboutActive = $isActive('tentang');
$moreActive = $aboutActive || $isActive('donasi') || $isActive('kontak');

// Penanda menu aktif; dianimasikan bergeser antar halaman (View Transitions, lihat style.css)
$navPill = fn(bool $on) => $on ? '<span class="nav-pill" aria-hidden="true"></span>' : '';

// Bottom nav (mobile): menu aktif naik ke lingkaran di atas bar yang berlekuk
$bnItems = [
    ['href' => url(), 'active' => $isActive(''), 'icon' => 'house-door', 'iconActive' => 'house-door-fill', 'label' => 'Beranda'],
    ['href' => url('jadwal-pengkhotbah'), 'active' => $isActive('jadwal-pengkhotbah'), 'icon' => 'calendar-week', 'iconActive' => 'calendar-week-fill', 'label' => 'Jadwal'],
    ['href' => url('kegiatan'), 'active' => $isActive('kegiatan'), 'icon' => 'calendar-event', 'iconActive' => 'calendar-event-fill', 'label' => 'Kegiatan'],
    ['href' => url('artikel'), 'active' => $isActive('artikel'), 'icon' => 'journal-text', 'iconActive' => 'journal-richtext', 'label' => 'Artikel'],
    ['href' => null, 'active' => $moreActive, 'icon' => 'grid', 'iconActive' => 'grid-fill', 'label' => 'Lainnya'],
];
$bnIndex = -1;
foreach ($bnItems as $i => $item) {
    if ($item['active']) { $bnIndex = $i; break; }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="<?= themeColor() ?>">
    <meta name="description" content="<?= e($description ?? APP_NAME . ' - Gereja yang Mengasihi dan Melayani') ?>">
    <title><?= e($title ?? APP_NAME) ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link href="<?= asset('css/style.css') ?>" rel="stylesheet">
    <?= themeStyleTag() ?>
    <?= faviconTag() ?>
    <script>
        // Browser tanpa View Transitions lintas halaman memakai animasi fade sederhana (main.js)
        if (!('CSSViewTransitionRule' in window)) document.documentElement.classList.add('vt-fallback');
    </script>
</head>

<body class="has-bottomnav">
    <!-- ===== TOP APP BAR ===== -->
    <header class="app-topbar" id="appTopbar">
        <div class="container">
            <a class="app-brand" href="<?= url() ?>">
                <?php if (setting('site_logo')): ?>
                    <img src="<?= uploads('settings/' . setting('site_logo')) ?>" alt="<?= e($siteName) ?>">
                <?php else: ?>
                    <span class="app-brand-mark"><?= brandMark() ?></span>
                <?php endif; ?>
                <span class="app-brand-name"><?= e($siteName) ?></span>
            </a>

            <!-- Desktop navigation -->
            <nav class="app-nav d-none d-lg-flex">
                <a href="<?= url() ?>" class="<?= $isActive('') ? 'active' : '' ?>"><?= $navPill($isActive('')) ?>Beranda</a>
                <div class="dropdown">
                    <a href="#" class="<?= $aboutActive ? 'active' : '' ?>" data-bs-toggle="dropdown" aria-expanded="false">
                        <?= $navPill($aboutActive) ?>Tentang <i class="bi bi-chevron-down small"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= url('tentang') ?>"><i class="bi bi-building me-2 text-primary"></i>Profil Gereja</a></li>
                        <li><a class="dropdown-item" href="<?= url('tentang/visi-misi') ?>"><i class="bi bi-eye me-2 text-primary"></i>Visi &amp; Misi</a></li>
                        <li><a class="dropdown-item" href="<?= url('tentang/sejarah') ?>"><i class="bi bi-book me-2 text-primary"></i>Sejarah</a></li>
                    </ul>
                </div>
                <a href="<?= url('jadwal-pengkhotbah') ?>" class="<?= $isActive('jadwal-pengkhotbah') ? 'active' : '' ?>"><?= $navPill($isActive('jadwal-pengkhotbah')) ?>Jadwal</a>
                <a href="<?= url('kegiatan') ?>" class="<?= $isActive('kegiatan') ? 'active' : '' ?>"><?= $navPill($isActive('kegiatan')) ?>Kegiatan</a>
                <a href="<?= url('artikel') ?>" class="<?= $isActive('artikel') ? 'active' : '' ?>"><?= $navPill($isActive('artikel')) ?>Artikel</a>
                <a href="<?= url('kontak') ?>" class="<?= $isActive('kontak') ? 'active' : '' ?>"><?= $navPill($isActive('kontak')) ?>Kontak</a>
                <a href="<?= url('donasi') ?>" class="btn btn-primary btn-sm">
                    <i class="bi bi-heart-fill me-1"></i>Donasi
                </a>
            </nav>

            <!-- Mobile quick action -->
            <div class="app-topbar-actions d-lg-none">
                <a href="<?= url('kontak') ?>" class="icon-btn" aria-label="Kontak"><i class="bi bi-chat-dots"></i></a>
            </div>
        </div>
    </header>

    <!-- ===== FLASH MESSAGES ===== -->
    <?php if (hasFlash('success') || hasFlash('error')): ?>
        <div class="flash-stack">
            <?php if (hasFlash('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert" data-auto-dismiss="5000">
                    <i class="bi bi-check-circle-fill"></i>
                    <div><?= getFlash('success') ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            <?php endif; ?>
            <?php if (hasFlash('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert" data-auto-dismiss="6000">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <div><?= getFlash('error') ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- ===== MAIN CONTENT ===== -->
    <main>
        <?= $content ?>
    </main>

    <!-- ===== FOOTER ===== -->
    <footer class="app-footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="app-brand mb-3">
                        <span class="app-brand-mark"><?= brandMark() ?></span>
                        <span class="app-brand-name"><?= e($siteName) ?></span>
                    </div>
                    <p class="mb-3">
                        <?= e(setting('site_tagline', 'Gereja yang mengasihi Tuhan dan sesama, melayani dengan kasih dan kebenaran.')) ?>
                    </p>
                    <div class="social">
                        <?php foreach (['facebook', 'instagram', 'youtube', 'tiktok'] as $social): ?>
                            <?php if (setting('site_' . $social)): ?>
                                <a href="<?= e(setting('site_' . $social)) ?>" target="_blank" rel="noopener" aria-label="<?= ucfirst($social) ?>">
                                    <i class="bi bi-<?= $social ?>"></i>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <h6>Jelajahi</h6>
                    <ul>
                        <li><a href="<?= url('tentang') ?>">Tentang Kami</a></li>
                        <li><a href="<?= url('jadwal-pengkhotbah') ?>">Jadwal Pengkhotbah</a></li>
                        <li><a href="<?= url('kegiatan') ?>">Kegiatan</a></li>
                        <li><a href="<?= url('artikel') ?>">Artikel</a></li>
                        <li><a href="<?= url('donasi') ?>">Donasi</a></li>
                    </ul>
                </div>

                <div class="col-6 col-lg-4">
                    <h6>Kontak</h6>
                    <ul>
                        <li><i class="bi bi-geo-alt me-2 text-primary"></i><?= e(setting('site_address', 'Jl. Gereja No. 123, Jakarta')) ?></li>
                        <li><i class="bi bi-telephone me-2 text-primary"></i><?= e(setting('site_phone', '(021) 1234-5678')) ?></li>
                        <li><i class="bi bi-envelope me-2 text-primary"></i><?= e(setting('site_email', 'info@gereja.com')) ?></li>
                    </ul>
                </div>
            </div>

            <div class="copyright d-flex flex-column flex-md-row justify-content-between gap-2">
                <span>&copy; <?= date('Y') ?> <?= e($siteName) ?></span>
                <?php if (isLoggedIn()): ?>
                    <a href="<?= url('admin/dashboard') ?>"><i class="bi bi-person-circle me-1"></i><?= e(auth('name')) ?></a>
                <?php else: ?>
                    <a href="<?= url('auth/login') ?>"><i class="bi bi-shield-lock me-1"></i>Pengerja Panel</a>
                <?php endif; ?>
            </div>
        </div>
    </footer>

    <!-- ===== BOTTOM NAVIGATION (mobile) ===== -->
    <nav class="app-bottomnav d-lg-none<?= $bnIndex < 0 ? ' no-active' : '' ?>" id="appBottomnav" aria-label="Navigasi utama"
         style="--bn-count: <?= count($bnItems) ?>; --bn-i: <?= max($bnIndex, 0) ?>;" data-bn-index="<?= $bnIndex ?>">
        <span class="bn-bar" aria-hidden="true"></span>
        <?php if ($bnIndex >= 0): ?>
            <span class="bn-bubble" aria-hidden="true"><i class="bi bi-<?= $bnItems[$bnIndex]['iconActive'] ?>"></i></span>
        <?php endif; ?>

        <?php foreach ($bnItems as $item): ?>
            <?php $cls = $item['active'] ? 'active' : ''; ?>
            <?php if ($item['href']): ?>
                <a href="<?= $item['href'] ?>" class="<?= $cls ?>"<?= $item['active'] ? ' aria-current="page"' : '' ?>>
            <?php else: ?>
                <button type="button" class="<?= $cls ?>" data-bs-toggle="offcanvas" data-bs-target="#moreSheet">
            <?php endif; ?>
                    <i class="bi bi-<?= $item['icon'] ?>"></i><span><?= $item['label'] ?></span>
            <?= $item['href'] ? '</a>' : '</button>' ?>
        <?php endforeach; ?>
    </nav>
    <script>
        // Lingkaran & lekukan bergeser dari menu halaman sebelumnya ke menu halaman ini
        (function () {
            var nav = document.getElementById('appBottomnav');
            var cur = +nav.dataset.bnIndex, prev = null;
            try { prev = sessionStorage.getItem('bnIndex'); sessionStorage.setItem('bnIndex', cur); } catch (e) {}
            if (prev === null || +prev === cur || +prev < 0 || cur < 0) return;
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

            nav.style.setProperty('--bn-pos', prev);
            nav.classList.add('bn-moving');
            requestAnimationFrame(function () {
                requestAnimationFrame(function () {
                    nav.classList.add('bn-animate');
                    nav.style.setProperty('--bn-pos', cur);
                });
            });
            setTimeout(function () { nav.classList.remove('bn-moving'); }, 700);
        })();
    </script>

    <!-- Bottom sheet "Lainnya" -->
    <div class="offcanvas offcanvas-bottom app-sheet" tabindex="-1" id="moreSheet" aria-labelledby="moreSheetLabel">
        <div class="sheet-handle"></div>
        <div class="offcanvas-header pb-2">
            <h5 class="offcanvas-title fw-bold" id="moreSheetLabel">Menu Lainnya</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
        </div>
        <div class="offcanvas-body pt-2 pb-4">
            <div class="service-grid">
                <a href="<?= url('tentang') ?>" class="service-item">
                    <span class="service-icon tone-green"><i class="bi bi-building"></i></span>Profil
                </a>
                <a href="<?= url('tentang/visi-misi') ?>" class="service-item">
                    <span class="service-icon tone-blue"><i class="bi bi-eye"></i></span>Visi &amp; Misi
                </a>
                <a href="<?= url('tentang/sejarah') ?>" class="service-item">
                    <span class="service-icon tone-orange"><i class="bi bi-book"></i></span>Sejarah
                </a>
                <a href="<?= url('donasi') ?>" class="service-item">
                    <span class="service-icon tone-red"><i class="bi bi-heart-fill"></i></span>Donasi
                </a>
                <a href="<?= url('kontak') ?>" class="service-item">
                    <span class="service-icon tone-teal"><i class="bi bi-chat-dots-fill"></i></span>Kontak
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= asset('js/main.js') ?>"></script>
</body>

</html>
