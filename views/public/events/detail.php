<section class="py-5 bg-primary text-white">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-3">
                <li class="breadcrumb-item"><a href="<?= url('/') ?>" class="text-white-50">Beranda</a></li>
                <li class="breadcrumb-item"><a href="<?= url('kegiatan') ?>" class="text-white-50">Kegiatan</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page">Detail</li>
            </ol>
        </nav>
        <h1 class="display-5 fw-bold"><?= e($event['title']) ?></h1>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8">
                <?php if ($event['image']): ?>
                    <img src="<?= uploads(e($event['image'])) ?>" class="img-fluid rounded shadow-sm mb-4 w-100"
                        alt="<?= e($event['title']) ?>" style="max-height: 500px; object-fit: cover;">
                <?php endif; ?>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <h3 class="fw-bold text-primary mb-4">Deskripsi Kegiatan</h3>
                        <div class="fs-5 text-muted content">
                            <?= nl2br(e($event['description'])) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm sticky-top" style="top: 100px;">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-info-circle me-2"></i>Informasi Kegiatan
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-4">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-calendar-event text-primary fs-4 me-3"></i>
                                    <div>
                                        <small class="text-muted d-block">Tanggal</small>
                                        <strong class="fs-5"><?= formatDate($event['event_date']) ?></strong>
                                    </div>
                                </div>
                            </li>
                            <li class="mb-4">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-clock text-primary fs-4 me-3"></i>
                                    <div>
                                        <small class="text-muted d-block">Waktu</small>
                                        <strong class="fs-5"><?= date('H:i', strtotime($event['event_time'])) ?>
                                            WIB</strong>
                                    </div>
                                </div>
                            </li>
                            <?php if ($event['location']): ?>
                                <li class="mb-4">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-geo-alt text-primary fs-4 me-3"></i>
                                        <div>
                                            <small class="text-muted d-block">Lokasi</small>
                                            <strong class="fs-5"><?= e($event['location']) ?></strong>
                                        </div>
                                    </div>
                                </li>
                            <?php endif; ?>
                            <li>
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-tag text-primary fs-4 me-3"></i>
                                    <div>
                                        <small class="text-muted d-block">Status</small>
                                        <?php
                                        $eventDate = strtotime($event['event_date']);
                                        $today = strtotime(date('Y-m-d'));
                                        if ($eventDate > $today): ?>
                                            <span class="badge bg-success fs-6">Akan Datang</span>
                                        <?php elseif ($eventDate === $today): ?>
                                            <span class="badge bg-primary fs-6">Hari Ini</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary fs-6">Sudah Lewat</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="card-footer bg-white p-4">
                        <div class="d-grid gap-2">
                            <a href="<?= url('kegiatan') ?>" class="btn btn-outline-primary btn-lg">
                                <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar
                            </a>
                            <a href="<?= url('kontak') ?>" class="btn btn-primary btn-lg">
                                <i class="bi bi-chat-dots me-2"></i>Hubungi Kami
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>