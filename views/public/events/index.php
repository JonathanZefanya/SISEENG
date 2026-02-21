<section class="py-5 bg-primary text-white">
    <div class="container text-center">
        <h1 class="display-4 fw-bold mb-3">Kegiatan & Acara</h1>
        <p class="lead fs-4">Ikuti berbagai kegiatan menarik di gereja kami</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <?php if (empty($events)): ?>
            <div class="text-center py-5">
                <i class="bi bi-calendar-x text-muted display-1"></i>
                <h3 class="mt-4 text-muted">Belum ada kegiatan yang dijadwalkan</h3>
                <p class="text-muted fs-5">Silakan kembali lagi nanti untuk melihat kegiatan terbaru</p>
                <a href="<?= url('/') ?>" class="btn btn-primary btn-lg mt-3">
                    <i class="bi bi-house me-2"></i>Kembali ke Beranda
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
                                    <?= e(substr(strip_tags($event['description']), 0, 100)) ?>...
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
            <?php if (isset($pagination) && isset($pagination['total_pages']) && $pagination['total_pages'] > 1): ?>
                <nav aria-label="Page navigation" class="mt-5">
                    <ul class="pagination justify-content-center pagination-lg">
                        <li class="page-item <?= $pagination['current_page'] <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= url('kegiatan?page=' . ($pagination['current_page'] - 1)) ?>">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>

                        <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                            <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                                <a class="page-link" href="<?= url('kegiatan?page=' . $i) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <li
                            class="page-item <?= $pagination['current_page'] >= $pagination['total_pages'] ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= url('kegiatan?page=' . ($pagination['current_page'] + 1)) ?>">
                                <i class="bi bi-chevron-right"></i>
                            </a>
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