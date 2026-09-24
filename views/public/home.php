<?php
$preachersByTime = $preachersByTime ?? [];
$hour = (int) date('G');
$greeting = $hour < 11 ? 'Selamat pagi' : ($hour < 15 ? 'Selamat siang' : ($hour < 18 ? 'Selamat sore' : 'Selamat malam'));
$firstService = $schedules[0] ?? null;
$firstPreacher = $firstService ? ($preachersByTime[$firstService['start_time']] ?? null) : null;
?>

<!-- ===== HERO ===== -->
<section class="home-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="greeting"><?= $greeting ?> 👋</div>
                <h1>
                    <?= e(setting('hero_title', 'Selamat Datang di')) ?>
                    <?= e(setting('hero_subtitle', APP_NAME)) ?>
                </h1>
                <p class="verse">
                    “<?= e(setting('hero_verse', 'Karena di mana dua atau tiga orang berkumpul dalam nama-Ku, di situ Aku ada di tengah-tengah mereka.')) ?>”
                    <small><?= e(setting('hero_verse_ref', 'Matius 18:20')) ?></small>
                </p>
            </div>
            <div class="col-lg-4 d-none d-lg-block text-end">
                <?php if (setting('hero_image')): ?>
                    <img src="<?= uploads('settings/' . setting('hero_image')) ?>" alt="<?= e(setting('site_name', 'Gereja')) ?>" class="home-hero-img">
                <?php else: ?>
                    <img src="<?= asset('images/hero-church.svg') ?>" alt="Gereja" class="home-hero-img">
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- ===== FLOATING CARD: ibadah berikutnya + menu layanan ===== -->
<div class="container">
    <div class="float-card">
        <?php if ($firstService): ?>
            <div class="next-service">
                <div class="ns-icon"><i class="bi bi-bell-fill"></i></div>
                <div class="flex-grow-1">
                    <div class="ns-label">Ibadah <?= !empty($nextSunday) ? 'Minggu, ' . formatDateIndo($nextSunday) : 'terdekat' ?></div>
                    <div class="ns-title"><?= e($firstService['title']) ?> · <?= date('H:i', strtotime($firstService['start_time'])) ?> WIB</div>
                    <div class="ns-meta">
                        <i class="bi bi-person-fill me-1"></i>
                        <?= $firstPreacher && $firstPreacher['preacher_name'] ? e($firstPreacher['preacher_name']) : 'Pengkhotbah belum ditentukan' ?>
                    </div>
                </div>
                <a href="#jadwal" class="btn btn-warning btn-sm">Lihat Jadwal</a>
            </div>
        <?php endif; ?>

        <div class="service-grid">
            <a href="#jadwal" class="service-item">
                <span class="service-icon tone-green"><i class="bi bi-calendar-check-fill"></i></span>Jadwal Ibadah
            </a>
            <a href="<?= url('jadwal-pengkhotbah') ?>" class="service-item">
                <span class="service-icon tone-teal"><i class="bi bi-person-video3"></i></span>Pengkhotbah
            </a>
            <a href="<?= url('kegiatan') ?>" class="service-item">
                <span class="service-icon tone-orange"><i class="bi bi-calendar-event-fill"></i></span>Kegiatan
            </a>
            <a href="<?= url('artikel') ?>" class="service-item">
                <span class="service-icon tone-blue"><i class="bi bi-journal-text"></i></span>Artikel
            </a>
            <a href="<?= url('donasi') ?>" class="service-item">
                <span class="service-icon tone-red"><i class="bi bi-heart-fill"></i></span>Donasi
            </a>
            <a href="<?= url('tentang') ?>" class="service-item">
                <span class="service-icon tone-purple"><i class="bi bi-building"></i></span>Tentang
            </a>
            <a href="<?= url('tentang/visi-misi') ?>" class="service-item">
                <span class="service-icon tone-pink"><i class="bi bi-eye-fill"></i></span>Visi Misi
            </a>
            <a href="<?= url('kontak') ?>" class="service-item">
                <span class="service-icon tone-dark"><i class="bi bi-chat-dots-fill"></i></span>Kontak
            </a>
        </div>
    </div>
</div>

<!-- ===== JADWAL IBADAH ===== -->
<section id="jadwal">
    <div class="container">
        <div class="section-head">
            <div>
                <h2>Jadwal Ibadah</h2>
                <p>
                    Mari beribadah bersama kami
                    <?php if (!empty($nextSunday)): ?>
                        · pengkhotbah Minggu, <?= formatDateIndo($nextSunday) ?>
                    <?php endif; ?>
                </p>
            </div>
            <a href="<?= url('jadwal-pengkhotbah') ?>" class="see-all">Lihat bulanan <i class="bi bi-chevron-right"></i></a>
        </div>

        <?php if (empty($schedules)): ?>
            <div class="alert alert-info mb-0"><i class="bi bi-info-circle me-2"></i>Jadwal ibadah belum tersedia.</div>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($schedules as $schedule):
                    $preacher = $preachersByTime[$schedule['start_time']] ?? null;
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="service-card">
                            <div class="sc-time">
                                <b><?= date('H:i', strtotime($schedule['start_time'])) ?></b>
                                <span>WIB</span>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="sc-title"><?= e($schedule['title']) ?></div>
                                <div class="sc-meta">
                                    <i class="bi bi-calendar3 me-1"></i><?= e($schedule['day_of_week']) ?>
                                    <?php if ($schedule['end_time']): ?>
                                        · s/d <?= date('H:i', strtotime($schedule['end_time'])) ?>
                                    <?php endif; ?>
                                    <?php if ($schedule['location']): ?>
                                        · <i class="bi bi-geo-alt"></i> <?= e($schedule['location']) ?>
                                    <?php endif; ?>
                                </div>
                                <div class="sc-preacher">
                                    <?php if ($preacher && $preacher['preacher_name']): ?>
                                        <i class="bi bi-person-check-fill text-primary me-1"></i><?= e($preacher['preacher_name']) ?>
                                    <?php else: ?>
                                        <span class="text-muted fw-normal fst-italic"><i class="bi bi-person me-1"></i>Belum ditentukan</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- ===== KEGIATAN MENDATANG ===== -->
<section class="pt-0">
    <div class="container">
        <div class="section-head">
            <div>
                <h2>Kegiatan Mendatang</h2>
                <p>Jangan lewatkan acara-acara berikut</p>
            </div>
            <a href="<?= url('kegiatan') ?>" class="see-all">Lihat semua <i class="bi bi-chevron-right"></i></a>
        </div>

        <?php if (empty($events)): ?>
            <div class="alert alert-info mb-0"><i class="bi bi-calendar-x me-2"></i>Belum ada kegiatan yang dijadwalkan.</div>
        <?php else: ?>
            <div class="h-scroll">
                <?php foreach ($events as $event): ?>
                    <a href="<?= url('kegiatan/detail/' . $event['slug']) ?>" class="promo-card">
                        <div class="promo-media">
                            <?php if ($event['image']): ?>
                                <img src="<?= uploads($event['image']) ?>" alt="<?= e($event['title']) ?>" loading="lazy">
                            <?php else: ?>
                                <i class="bi bi-calendar-event"></i>
                            <?php endif; ?>
                            <div class="promo-date">
                                <b><?= date('d', strtotime($event['event_date'])) ?></b>
                                <span><?= formatDate($event['event_date'], 'M') ?></span>
                            </div>
                        </div>
                        <div class="promo-body">
                            <div class="promo-title"><?= e($event['title']) ?></div>
                            <div class="promo-meta">
                                <?php if ($event['event_time']): ?>
                                    <span><i class="bi bi-clock me-1"></i><?= e($event['event_time']) ?></span>
                                <?php endif; ?>
                                <?php if ($event['location']): ?>
                                    <span><i class="bi bi-geo-alt me-1"></i><?= e($event['location']) ?></span>
                                <?php endif; ?>
                            </div>
                            <p class="promo-excerpt"><?= e(truncate($event['description'], 90)) ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- ===== ARTIKEL TERBARU ===== -->
<section class="pt-0">
    <div class="container">
        <div class="section-head">
            <div>
                <h2>Artikel Terbaru</h2>
                <p>Renungan dan kabar dari gereja</p>
            </div>
            <a href="<?= url('artikel') ?>" class="see-all">Lihat semua <i class="bi bi-chevron-right"></i></a>
        </div>

        <?php if (empty($articles)): ?>
            <div class="alert alert-info mb-0"><i class="bi bi-file-text me-2"></i>Belum ada artikel yang dipublikasikan.</div>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($articles as $article): ?>
                    <div class="col-md-6 col-lg-4">
                        <a href="<?= url('artikel/baca/' . $article['slug']) ?>" class="list-card h-100">
                            <div class="list-card-thumb">
                                <?php if ($article['image']): ?>
                                    <img src="<?= uploads($article['image']) ?>" alt="<?= e($article['title']) ?>" loading="lazy">
                                <?php else: ?>
                                    <i class="bi bi-journal-text"></i>
                                <?php endif; ?>
                            </div>
                            <div class="min-w-0">
                                <div class="list-card-title"><?= e(truncate($article['title'], 70)) ?></div>
                                <div class="list-card-meta">
                                    <?= formatDate($article['published_at']) ?>
                                    <?php if ($article['author_name']): ?> · <?= e($article['author_name']) ?><?php endif; ?>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- ===== LOKASI & KONTAK ===== -->
<section class="pt-0">
    <div class="container">
        <div class="section-head">
            <div>
                <h2>Temukan Kami</h2>
                <p>Kami menantikan kehadiran Anda</p>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-5">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="info-row">
                            <div class="ir-icon"><i class="bi bi-geo-alt-fill"></i></div>
                            <div>
                                <div class="ir-label">Alamat</div>
                                <div class="ir-value"><?= e(setting('site_address', 'Jl. Gereja No. 123, Jakarta')) ?></div>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="ir-icon"><i class="bi bi-telephone-fill"></i></div>
                            <div>
                                <div class="ir-label">Telepon</div>
                                <div class="ir-value"><?= e(setting('site_phone', '(021) 1234-5678')) ?></div>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="ir-icon"><i class="bi bi-envelope-fill"></i></div>
                            <div>
                                <div class="ir-label">Email</div>
                                <div class="ir-value text-break"><?= e(setting('site_email', 'info@gereja.com')) ?></div>
                            </div>
                        </div>
                        <?php if (setting('site_whatsapp')): ?>
                            <div class="info-row">
                                <div class="ir-icon"><i class="bi bi-whatsapp"></i></div>
                                <div>
                                    <div class="ir-label">WhatsApp</div>
                                    <div class="ir-value"><?= e(setting('site_whatsapp')) ?></div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <a href="<?= url('kontak') ?>" class="btn btn-primary w-100 mt-3">
                            <i class="bi bi-chat-dots me-2"></i>Hubungi Kami
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="ratio ratio-4x3 rounded overflow-hidden card">
                    <?php $gmapsUrl = setting('site_gmaps_embed'); ?>
                    <?php if (!empty($gmapsUrl)): ?>
                        <iframe src="<?= e($gmapsUrl) ?>" style="border:0;" allowfullscreen loading="lazy"></iframe>
                    <?php else: ?>
                        <div class="d-flex align-items-center justify-content-center">
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
<section class="pt-0">
    <div class="container">
        <div class="cta-banner">
            <h2><?= e(setting('donation_title', 'Dukung Pelayanan Kami')) ?></h2>
            <p><?= e(setting('donation_description', 'Persembahan dan donasi Anda sangat berarti untuk mendukung pelayanan gereja dan membantu sesama.')) ?></p>
            <a href="<?= url('donasi') ?>" class="btn btn-warning">
                <i class="bi bi-heart-fill me-2"></i>Berikan Donasi
            </a>
        </div>
    </div>
</section>
