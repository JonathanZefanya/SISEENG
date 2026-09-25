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
                <a href="<?= url() ?>" class="<?= $isActive('') ? 'active' : '' ?>">Beranda</a>
                <div class="dropdown">
                    <a href="#" class="<?= $aboutActive ? 'active' : '' ?>" data-bs-toggle="dropdown" aria-expanded="false">
                        Tentang <i class="bi bi-chevron-down small"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= url('tentang') ?>"><i class="bi bi-building me-2 text-primary"></i>Profil Gereja</a></li>
                        <li><a class="dropdown-item" href="<?= url('tentang/visi-misi') ?>"><i class="bi bi-eye me-2 text-primary"></i>Visi &amp; Misi</a></li>
                        <li><a class="dropdown-item" href="<?= url('tentang/sejarah') ?>"><i class="bi bi-book me-2 text-primary"></i>Sejarah</a></li>
                    </ul>
                </div>
                <a href="<?= url('jadwal-pengkhotbah') ?>" class="<?= $isActive('jadwal-pengkhotbah') ? 'active' : '' ?>">Jadwal</a>
                <a href="<?= url('kegiatan') ?>" class="<?= $isActive('kegiatan') ? 'active' : '' ?>">Kegiatan</a>
                <a href="<?= url('artikel') ?>" class="<?= $isActive('artikel') ? 'active' : '' ?>">Artikel</a>
                <a href="<?= url('kontak') ?>" class="<?= $isActive('kontak') ? 'active' : '' ?>">Kontak</a>
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
    <nav class="app-bottomnav d-lg-none" aria-label="Navigasi utama">
        <a href="<?= url() ?>" class="<?= $isActive('') ? 'active' : '' ?>">
            <i class="bi bi-house-door<?= $isActive('') ? '-fill' : '' ?>"></i>Beranda
        </a>
        <a href="<?= url('jadwal-pengkhotbah') ?>" class="<?= $isActive('jadwal-pengkhotbah') ? 'active' : '' ?>">
            <i class="bi bi-calendar-week<?= $isActive('jadwal-pengkhotbah') ? '-fill' : '' ?>"></i>Jadwal
        </a>
        <a href="<?= url('kegiatan') ?>" class="<?= $isActive('kegiatan') ? 'active' : '' ?>">
            <i class="bi bi-calendar-event<?= $isActive('kegiatan') ? '-fill' : '' ?>"></i>Kegiatan
        </a>
        <a href="<?= url('artikel') ?>" class="<?= $isActive('artikel') ? 'active' : '' ?>">
            <i class="bi bi-journal-text"></i>Artikel
        </a>
        <button type="button" class="<?= $moreActive ? 'active' : '' ?>" data-bs-toggle="offcanvas" data-bs-target="#moreSheet">
            <i class="bi bi-grid<?= $moreActive ? '-fill' : '' ?>"></i>Lainnya
        </button>
    </nav>

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
