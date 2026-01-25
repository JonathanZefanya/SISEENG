<!-- ===== HERO SECTION ===== -->
<section class="hero-section bg-primary text-white py-5" style="position: relative; z-index: 1;">
    <div class="container">
        <div class="row align-items-center min-vh-50">
            <div class="col-lg-6 py-5">
                <h1 class="display-4 fw-bold mb-4">
                    <?= e(setting('hero_title', 'Selamat Datang di')) ?><br>
                    <span class="text-warning"><?= e(setting('hero_subtitle', APP_NAME)) ?></span>
                </h1>
                <p class="lead fs-4 mb-4">
                    "<?= e(setting('hero_verse', 'Karena di mana dua atau tiga orang berkumpul dalam nama-Ku, di situ Aku ada di tengah-tengah mereka.')) ?>"
                    <br><small class="text-white-50">- <?= e(setting('hero_verse_ref', 'Matius 18:20')) ?></small>
                </p>
                <div class="d-flex gap-3 flex-wrap" style="position: relative; z-index: 10;">
                    <a href="<?= url('tentang') ?>" class="btn btn-warning btn-lg px-4 py-3 fs-5" style="position: relative; z-index: 10;">
                        <i class="bi bi-info-circle me-2"></i>Tentang Kami
                    </a>
                    <a href="<?= url('kontak') ?>" class="btn btn-outline-light btn-lg px-4 py-3 fs-5" style="position: relative; z-index: 10;">
                        <i class="bi bi-envelope me-2"></i>Hubungi Kami
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center py-5">
                <?php if (setting('hero_image')): ?>
                <img src="<?= uploads('settings/' . setting('hero_image')) ?>" alt="<?= e(setting('site_name', 'Church')) ?>" class="img-fluid" style="max-height: 400px;">
                <?php else: ?>
                <img src="<?= asset('images/hero-church.svg') ?>" alt="Church" class="img-fluid" style="max-height: 400px;">
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- ===== JADWAL IBADAH ===== -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold text-primary">Jadwal Ibadah</h2>
            <p class="lead text-muted">Mari beribadah bersama kami</p>
            <?php if (!empty($nextSunday)): ?>
            <p class="text-muted small">
                <i class="bi bi-calendar-event me-1"></i>
                Pengkhotbah untuk Minggu, <?= formatDateIndo($nextSunday) ?>
            </p>
            <?php endif; ?>
        </div>
        
        <?php $preachersByTime = $preachersByTime ?? []; ?>
        
        <div class="row g-4 justify-content-center">
            <?php if (empty($schedules)): ?>
            <div class="col-12">
                <div class="alert alert-info text-center fs-5">
                    <i class="bi bi-info-circle me-2"></i>
                    Jadwal ibadah belum tersedia.
                </div>
            </div>
            <?php else: ?>
                <?php foreach ($schedules as $schedule): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body text-center p-4">
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="bi bi-calendar-check fs-4"></i>
                            </div>
                            <h4 class="card-title fw-bold"><?= e($schedule['title']) ?></h4>
                            <p class="text-primary fs-5 fw-semibold mb-2">
                                <i class="bi bi-calendar3 me-2"></i><?= e($schedule['day_of_week']) ?>
                            </p>
                            <p class="text-muted fs-5 mb-2">
                                <i class="bi bi-clock me-2"></i>
                                <?= date('H:i', strtotime($schedule['start_time'])) ?><?= $schedule['end_time'] ? ' - ' . date('H:i', strtotime($schedule['end_time'])) : '' ?> WIB
                            </p>
                            <?php 
                            // Cari pengkhotbah berdasarkan waktu ibadah
                            $preacher = $preachersByTime[$schedule['start_time']] ?? null;
                            ?>
                            <p class="mb-2">
                                <i class="bi bi-person-fill me-2 text-primary"></i>
                                <?php if ($preacher && $preacher['preacher_name']): ?>
                                <strong class="text-dark"><?= e($preacher['preacher_name']) ?></strong>
                                <?php else: ?>
                                <span class="text-muted fst-italic">Belum Ditentukan</span>
                                <?php endif; ?>
                            </p>
                            <?php if ($schedule['location']): ?>
                            <p class="text-muted fs-6 mb-0">
                                <i class="bi bi-geo-alt me-2"></i><?= e($schedule['location']) ?>
                            </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <!-- Link ke jadwal pengkhotbah bulanan -->
        <div class="text-center mt-4">
            <a href="<?= url('jadwal-pengkhotbah') ?>" class="btn btn-outline-primary btn-lg">
                <i class="bi bi-calendar-week me-2"></i>Lihat Jadwal Pengkhotbah Bulanan
            </a>
        </div>
    </div>
</section>

<!-- ===== KEGIATAN MENDATANG ===== -->
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="display-6 fw-bold text-primary mb-0">Kegiatan Mendatang</h2>
            </div>
            <a href="<?= url('kegiatan') ?>" class="btn btn-outline-primary btn-lg">
                Lihat Semua <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        
        <div class="row g-4">
            <?php if (empty($events)): ?>
            <div class="col-12">
                <div class="alert alert-info text-center fs-5">
                    <i class="bi bi-calendar-x me-2"></i>
                    Belum ada kegiatan yang dijadwalkan.
                </div>
            </div>
            <?php else: ?>
                <?php foreach ($events as $event): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0 overflow-hidden">
                        <?php if ($event['image']): ?>
                        <img src="<?= url($event['image']) ?>" class="card-img-top" alt="<?= e($event['title']) ?>" style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                        <div class="bg-primary text-white d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="bi bi-calendar-event display-1"></i>
                        </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-primary me-2">
                                    <i class="bi bi-calendar me-1"></i>
                                    <?= formatDate($event['event_date']) ?>
                                </span>
                                <?php if ($event['event_time']): ?>
                                <span class="badge bg-secondary">
                                    <i class="bi bi-clock me-1"></i>
                                    <?= e($event['event_time']) ?>
                                </span>
                                <?php endif; ?>
                            </div>
                            <h5 class="card-title fw-bold"><?= e($event['title']) ?></h5>
                            <p class="card-text text-muted">
                                <?= e(truncate($event['description'], 100)) ?>
                            </p>
                            <?php if ($event['location']): ?>
                            <p class="text-muted small mb-0">
                                <i class="bi bi-geo-alt me-1"></i><?= e($event['location']) ?>
                            </p>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer bg-white border-0 pt-0">
                            <a href="<?= url('kegiatan/detail/' . $event['slug']) ?>" class="btn btn-primary w-100">
                                Selengkapnya <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ===== ARTIKEL TERBARU ===== -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="display-6 fw-bold text-primary mb-0">Artikel Terbaru</h2>
            </div>
            <a href="<?= url('artikel') ?>" class="btn btn-outline-primary btn-lg">
                Lihat Semua <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        
        <div class="row g-4">
            <?php if (empty($articles)): ?>
            <div class="col-12">
                <div class="alert alert-info text-center fs-5">
                    <i class="bi bi-file-text me-2"></i>
                    Belum ada artikel yang dipublikasikan.
                </div>
            </div>
            <?php else: ?>
                <?php foreach ($articles as $article): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0 overflow-hidden">
                        <?php if ($article['image']): ?>
                        <img src="<?= url($article['image']) ?>" class="card-img-top" alt="<?= e($article['title']) ?>" style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                        <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="bi bi-file-earmark-text display-1"></i>
                        </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <small class="text-muted">
                                <i class="bi bi-calendar me-1"></i>
                                <?= formatDate($article['published_at']) ?>
                                <?php if ($article['author_name']): ?>
                                <span class="ms-2">
                                    <i class="bi bi-person me-1"></i><?= e($article['author_name']) ?>
                                </span>
                                <?php endif; ?>
                            </small>
                            <h5 class="card-title fw-bold mt-2"><?= e($article['title']) ?></h5>
                            <p class="card-text text-muted">
                                <?= e($article['excerpt'] ?: truncate(strip_tags($article['content']), 100)) ?>
                            </p>
                        </div>
                        <div class="card-footer bg-white border-0 pt-0">
                            <a href="<?= url('artikel/baca/' . $article['slug']) ?>" class="btn btn-outline-primary w-100">
                                Baca Selengkapnya <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ===== LOKASI & KONTAK ===== -->
<section class="py-5">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-lg-6">
                <h2 class="display-6 fw-bold text-primary mb-4">Temukan Kami</h2>
                <div class="d-flex mb-4">
                    <div class="flex-shrink-0">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-geo-alt fs-5"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="fw-bold">Alamat</h5>
                        <p class="text-muted fs-5 mb-0"><?= e(setting('site_address', 'Jl. Gereja No. 123, Jakarta')) ?></p>
                    </div>
                </div>
                
                <div class="d-flex mb-4">
                    <div class="flex-shrink-0">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-telephone fs-5"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="fw-bold">Telepon</h5>
                        <p class="text-muted fs-5 mb-0"><?= e(setting('site_phone', '(021) 1234-5678')) ?></p>
                    </div>
                </div>
                
                <div class="d-flex mb-4">
                    <div class="flex-shrink-0">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-envelope fs-5"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="fw-bold">Email</h5>
                        <p class="text-muted fs-5 mb-0"><?= e(setting('site_email', 'info@gereja.com')) ?></p>
                    </div>
                </div>
                
                <?php if (setting('site_whatsapp')): ?>
                <div class="d-flex mb-4">
                    <div class="flex-shrink-0">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-whatsapp fs-5"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="fw-bold">WhatsApp</h5>
                        <p class="text-muted fs-5 mb-0"><?= e(setting('site_whatsapp')) ?></p>
                    </div>
                </div>
                <?php endif; ?>
                
                <a href="<?= url('kontak') ?>" class="btn btn-primary btn-lg px-4 py-3 fs-5">
                    <i class="bi bi-chat-dots me-2"></i>Hubungi Kami
                </a>
            </div>
            
            <div class="col-lg-6">
                <!-- Embed Google Maps -->
                <div class="ratio ratio-4x3 rounded shadow overflow-hidden">
                    <?php 
                    $gmapsUrl = setting('site_gmaps_embed');
                    if (!empty($gmapsUrl)): 
                    ?>
                    <iframe 
                        src="<?= e($gmapsUrl) ?>" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy">
                    </iframe>
                    <?php else: ?>
                    <div class="d-flex align-items-center justify-content-center bg-light">
                        <div class="text-center text-muted">
                            <i class="bi bi-geo-alt" style="font-size: 3rem;"></i>
                            <p class="mb-0 mt-2">Lokasi belum tersedia</p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== CTA DONASI ===== -->
<section class="py-5 bg-primary text-white">
    <div class="container text-center">
        <h2 class="display-6 fw-bold mb-4"><?= e(setting('donation_title', 'Dukung Pelayanan Kami')) ?></h2>
        <p class="lead fs-4 mb-4">
            <?= e(setting('donation_description', 'Persembahan dan donasi Anda sangat berarti untuk mendukung pelayanan gereja dan membantu sesama.')) ?>
        </p>
        <a href="<?= url('donasi') ?>" class="btn btn-warning btn-lg px-5 py-3 fs-5">
            <i class="bi bi-heart-fill me-2"></i>Berikan Donasi
        </a>
    </div>
</section>
