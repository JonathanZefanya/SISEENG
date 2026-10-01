<?php
use App\Models\Event;

$timeIcons = ['mendatang' => 'calendar-plus', 'bulan-ini' => 'calendar-month', 'selesai' => 'calendar-check'];
$pageUrl = fn(int $page) => url('kegiatan?' . http_build_query(array_filter(['waktu' => $when, 'page' => $page > 1 ? $page : null])));
?>
<section class="page-hero">
    <div class="container">
        <h1>Kegiatan &amp; Acara</h1>
        <p>Ikuti berbagai kegiatan menarik di gereja kami</p>
    </div>
</section>

<!-- Filter waktu: satu baris chip, bisa digeser, menempel di bawah topbar -->
<section class="filter-bar">
    <div class="container">
        <nav class="filter-scroll" aria-label="Filter waktu kegiatan">
            <a href="<?= url('kegiatan') ?>" class="filter-chip <?= $when === '' ? 'active' : '' ?>" <?= $when === '' ? 'aria-current="page"' : '' ?>>
                <i class="bi bi-grid"></i>Semua <span class="count"><?= (int) ($timeCounts['semua'] ?? 0) ?></span>
            </a>
            <?php foreach (Event::TIME_FILTERS as $key => [$label]): ?>
                <a href="<?= url('kegiatan?waktu=' . $key) ?>" class="filter-chip <?= $when === $key ? 'active' : '' ?>" <?= $when === $key ? 'aria-current="page"' : '' ?>>
                    <i class="bi bi-<?= $timeIcons[$key] ?>"></i><?= e($label) ?> <span class="count"><?= (int) ($timeCounts[$key] ?? 0) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <?php if (empty($events)): ?>
            <div class="empty-state">
                <div class="empty-state-icon"><i class="bi bi-calendar-x"></i></div>
                <?php if ($when === 'mendatang'): ?>
                    <h2 class="h4">Belum ada kegiatan yang akan datang</h2>
                    <p class="text-muted">Jadwal kegiatan berikutnya akan diumumkan di sini.</p>
                <?php elseif ($when === 'bulan-ini'): ?>
                    <h2 class="h4">Tidak ada kegiatan bulan ini</h2>
                    <p class="text-muted">Coba lihat kegiatan yang akan datang atau semua kegiatan.</p>
                <?php elseif ($when === 'selesai'): ?>
                    <h2 class="h4">Belum ada kegiatan yang sudah lewat</h2>
                    <p class="text-muted">Kegiatan yang telah selesai akan tersimpan di sini.</p>
                <?php else: ?>
                    <h2 class="h4">Belum ada kegiatan yang dijadwalkan</h2>
                    <p class="text-muted">Silakan kembali lagi nanti untuk melihat kegiatan terbaru.</p>
                <?php endif; ?>
                <a href="<?= $when ? url('kegiatan') : url('/') ?>" class="btn btn-primary mt-2">
                    <i class="bi bi-<?= $when ? 'grid' : 'house' ?> me-2"></i><?= $when ? 'Lihat semua kegiatan' : 'Kembali ke Beranda' ?>
                </a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($events as $event): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm event-card">
                            <?php if ($event['image']): ?>
                                <img src="<?= uploads(e($event['image'])) ?>" class="card-img-top" alt="<?= e($event['title']) ?>"
                                    style="height: 200px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-primary text-white d-flex align-items-center justify-content-center"
                                    style="height: 200px;">
                                    <i class="bi bi-calendar-event display-1"></i>
                                </div>
                            <?php endif; ?>

                            <div class="card-body p-4">
                                <!-- Date Badge -->
                                <div class="d-flex align-items-start mb-3">
                                    <div class="bg-primary text-white text-center rounded p-2 me-3" style="min-width: 60px;">
                                        <div class="fs-4 fw-bold"><?= date('d', strtotime($event['event_date'])) ?></div>
                                        <div class="small"><?= date('M', strtotime($event['event_date'])) ?></div>
                                    </div>
                                    <div>
                                        <h5 class="card-title fw-bold mb-1"><?= e($event['title']) ?></h5>
                                        <p class="text-muted small mb-0">
                                            <i class="bi bi-clock me-1"></i>
                                            <?= date('H:i', strtotime($event['event_time'])) ?> WIB
                                        </p>
                                    </div>
                                </div>

                                <p class="card-text text-muted">
                                    <?= e(excerpt($event['description'], 100)) ?>
                                </p>

                                <?php if ($event['location']): ?>
                                    <p class="text-muted small mb-3">
                                        <i class="bi bi-geo-alt text-primary me-1"></i>
                                        <?= e($event['location']) ?>
                                    </p>
                                <?php endif; ?>
                            </div>

                            <div class="card-footer bg-white border-0 p-4 pt-0">
                                <a href="<?= url('kegiatan/' . $event['id']) ?>" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-info-circle me-2"></i>Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($pagination['last_page'] > 1): ?>
                <nav aria-label="Navigasi halaman" class="mt-5">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= $pagination['current_page'] <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= $pageUrl($pagination['current_page'] - 1) ?>" aria-label="Sebelumnya"><i class="bi bi-chevron-left"></i></a>
                        </li>
                        <?php for ($i = 1; $i <= $pagination['last_page']; $i++): ?>
                            <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                                <a class="page-link" href="<?= $pageUrl($i) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= $pagination['current_page'] >= $pagination['last_page'] ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= $pageUrl($pagination['current_page'] + 1) ?>" aria-label="Berikutnya"><i class="bi bi-chevron-right"></i></a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<style>
    .event-card:hover {
        transform: translateY(-5px);
        transition: transform 0.3s ease;
    }
</style>