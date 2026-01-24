<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($description ?? APP_NAME . ' - Gereja yang Mengasihi dan Melayani') ?>">
    <title><?= e($title ?? APP_NAME) ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Google Fonts - Untuk keterbacaan lansia -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Merriweather:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="<?= asset('css/style.css') ?>" rel="stylesheet">
</head>
<body>
    <!-- ===== NAVBAR ===== -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="<?= url() ?>">
                <?php if (setting('site_logo')): ?>
                <img src="<?= uploads('settings/' . setting('site_logo')) ?>" alt="<?= e(setting('site_name', APP_NAME)) ?>" style="height: 45px;" class="me-2">
                <?php else: ?>
                <i class="bi bi-brightness-high-fill text-primary me-2 fs-3"></i>
                <?php endif; ?>
                <span class="fw-bold text-primary"><?= e(setting('site_name', APP_NAME)) ?></span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link fs-5" href="<?= url() ?>">Beranda</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle fs-5" href="#" data-bs-toggle="dropdown">Tentang Kami</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item fs-6" href="<?= url('tentang') ?>">Profil Gereja</a></li>
                            <li><a class="dropdown-item fs-6" href="<?= url('tentang/visi-misi') ?>">Visi & Misi</a></li>
                            <li><a class="dropdown-item fs-6" href="<?= url('tentang/sejarah') ?>">Sejarah</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fs-5" href="<?= url('jadwal-pengkhotbah') ?>">Jadwal Pengkhotbah</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fs-5" href="<?= url('kegiatan') ?>">Kegiatan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fs-5" href="<?= url('artikel') ?>">Artikel</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fs-5" href="<?= url('donasi') ?>">Donasi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fs-5" href="<?= url('kontak') ?>">Kontak</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- ===== FLASH MESSAGES ===== -->
    <?php if (hasFlash('success')): ?>
    <div class="alert alert-success alert-dismissible fade show m-0 rounded-0" role="alert">
        <div class="container">
            <i class="bi bi-check-circle me-2"></i>
            <?= getFlash('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if (hasFlash('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show m-0 rounded-0" role="alert">
        <div class="container">
            <i class="bi bi-exclamation-circle me-2"></i>
            <?= getFlash('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- ===== MAIN CONTENT ===== -->
    <main>
        <?= $content ?>
    </main>
    
    <!-- ===== FOOTER ===== -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <h5 class="mb-3">
                        <i class="bi bi-brightness-high-fill me-2"></i>
                        <?= e(setting('site_name', APP_NAME)) ?>
                    </h5>
                    <p class="text-white-50 fs-5">
                        <?= e(setting('site_tagline', 'Gereja yang mengasihi Tuhan dan sesama, melayani dengan kasih dan kebenaran.')) ?>
                    </p>
                </div>
                
                <div class="col-lg-4">
                    <h5 class="mb-3">Tautan Cepat</h5>
                    <ul class="list-unstyled fs-5">
                        <li class="mb-2"><a href="<?= url() ?>" class="text-white-50 text-decoration-none">Beranda</a></li>
                        <li class="mb-2"><a href="<?= url('tentang') ?>" class="text-white-50 text-decoration-none">Tentang Kami</a></li>
                        <li class="mb-2"><a href="<?= url('kegiatan') ?>" class="text-white-50 text-decoration-none">Kegiatan</a></li>
                        <li class="mb-2"><a href="<?= url('kontak') ?>" class="text-white-50 text-decoration-none">Hubungi Kami</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-4">
                    <h5 class="mb-3">Kontak</h5>
                    <ul class="list-unstyled text-white-50 fs-5">
                        <li class="mb-2">
                            <i class="bi bi-geo-alt me-2"></i>
                            <?= e(setting('site_address', 'Jl. Gereja No. 123, Jakarta')) ?>
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-telephone me-2"></i>
                            <?= e(setting('site_phone', '(021) 1234-5678')) ?>
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-envelope me-2"></i>
                            <?= e(setting('site_email', 'info@gereja.com')) ?>
                        </li>
                    </ul>
                    
                    <div class="mt-3">
                        <?php if (setting('site_facebook')): ?>
                        <a href="<?= e(setting('site_facebook')) ?>" target="_blank" class="text-white me-3 fs-4"><i class="bi bi-facebook"></i></a>
                        <?php endif; ?>
                        <?php if (setting('site_instagram')): ?>
                        <a href="<?= e(setting('site_instagram')) ?>" target="_blank" class="text-white me-3 fs-4"><i class="bi bi-instagram"></i></a>
                        <?php endif; ?>
                        <?php if (setting('site_youtube')): ?>
                        <a href="<?= e(setting('site_youtube')) ?>" target="_blank" class="text-white me-3 fs-4"><i class="bi bi-youtube"></i></a>
                        <?php endif; ?>
                        <?php if (setting('site_tiktok')): ?>
                        <a href="<?= e(setting('site_tiktok')) ?>" target="_blank" class="text-white me-3 fs-4"><i class="bi bi-tiktok"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <hr class="my-4 border-secondary">
            
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p class="text-white-50 mb-0">
                        &copy; <?= date('Y') ?> <?= e(setting('site_name', APP_NAME)) ?>. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <a href="<?= url('auth/login') ?>" class="text-white-50 text-decoration-none small">
                        <i class="bi bi-shield-lock me-1"></i>Admin Login
                    </a>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?= asset('js/main.js') ?>"></script>
</body>
</html>
