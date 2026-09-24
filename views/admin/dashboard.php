<!-- Sapaan -->
<div class="hello-card mb-4">
    <div class="d-flex justify-content-between align-items-start gap-3 position-relative" style="z-index:1;">
        <div>
            <h1>Halo, <?= e(auth('name')) ?> 👋</h1>
            <p>Apa yang mau kamu kelola hari ini?</p>
        </div>
        <span class="badge bg-white text-dark px-3 py-2 flex-shrink-0">
            <i class="bi bi-shield-check me-1 text-primary"></i><?= isSuperAdmin() ? 'Super Admin' : 'Admin' ?>
        </span>
    </div>
</div>

<!-- Aksi cepat -->
<div class="card mb-4">
    <div class="card-body">
        <div class="quick-grid">
            <a href="<?= url('admin/jadwal-pengkhotbah') ?>" class="quick-item">
                <span class="quick-icon tone-green"><i class="bi bi-person-video3"></i></span>Pengkhotbah
            </a>
            <a href="<?= url('admin/jadwal') ?>" class="quick-item">
                <span class="quick-icon tone-teal"><i class="bi bi-calendar-check-fill"></i></span>Jadwal Ibadah
            </a>
            <a href="<?= url('admin/kegiatan') ?>" class="quick-item">
                <span class="quick-icon tone-orange"><i class="bi bi-calendar-event-fill"></i></span>Kegiatan
            </a>
            <a href="<?= url('admin/artikel') ?>" class="quick-item">
                <span class="quick-icon tone-blue"><i class="bi bi-journal-text"></i></span>Artikel
            </a>
            <a href="<?= url('admin/jemaat') ?>" class="quick-item">
                <span class="quick-icon tone-purple"><i class="bi bi-people-fill"></i></span>Jemaat
            </a>
            <a href="<?= url('admin/pesan') ?>" class="quick-item">
                <span class="quick-icon tone-pink"><i class="bi bi-envelope-fill"></i></span>Pesan
            </a>
            <a href="<?= url('admin/rekening-donasi') ?>" class="quick-item">
                <span class="quick-icon tone-red"><i class="bi bi-credit-card-fill"></i></span>Donasi
            </a>
            <a href="<?= url('admin/pengaturan') ?>" class="quick-item">
                <span class="quick-icon tone-dark"><i class="bi bi-gear-fill"></i></span>Pengaturan
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="stat-card bg-primary text-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="stat-label mb-1">Total Jemaat</p>
                    <h2 class="stat-value mb-0"><?= number_format($stats['total_members']) ?></h2>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-6 col-xl-3">
        <div class="stat-card bg-success text-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="stat-label mb-1">Total Artikel</p>
                    <h2 class="stat-value mb-0"><?= number_format($stats['total_articles']) ?></h2>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-file-earmark-text-fill"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-6 col-xl-3">
        <div class="stat-card bg-warning text-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="stat-label mb-1">Pesan Baru</p>
                    <h2 class="stat-value mb-0"><?= number_format($stats['unread_messages']) ?></h2>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-envelope-fill"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-6 col-xl-3">
        <div class="stat-card bg-info text-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="stat-label mb-1">Kegiatan</p>
                    <h2 class="stat-value mb-0"><?= number_format($stats['upcoming_events']) ?></h2>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-calendar-event-fill"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Pesan Terbaru -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title">
                    <i class="bi bi-envelope text-primary me-2"></i>Pesan Terbaru
                </h5>
                <a href="<?= url('admin/pesan') ?>" class="btn btn-sm btn-primary">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentMessages)): ?>
                <div class="p-5 text-center text-muted">
                    <i class="bi bi-inbox display-4 d-block mb-3 opacity-25"></i>
                    <p class="mb-0">Tidak ada pesan baru</p>
                </div>
                <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($recentMessages as $msg): ?>
                    <a href="<?= url('admin/pesan/baca/' . $msg['id']) ?>" class="list-group-item list-group-item-action p-3 border-0 border-bottom">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <h6 class="mb-0 fw-semibold text-dark"><?= e($msg['name']) ?></h6>
                            <small class="text-muted"><?= formatDate($msg['created_at'], 'd M') ?></small>
                        </div>
                        <p class="mb-1 text-primary small fw-medium"><?= e($msg['subject'] ?: 'Tanpa subjek') ?></p>
                        <small class="text-muted"><?= e(truncate($msg['message'], 60)) ?></small>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Kegiatan Mendatang -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title">
                    <i class="bi bi-calendar-event text-primary me-2"></i>Kegiatan Mendatang
                </h5>
                <a href="<?= url('admin/kegiatan') ?>" class="btn btn-sm btn-primary">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($upcomingEvents)): ?>
                <div class="p-5 text-center text-muted">
                    <i class="bi bi-calendar-x display-4 d-block mb-3 opacity-25"></i>
                    <p class="mb-0">Tidak ada kegiatan mendatang</p>
                </div>
                <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($upcomingEvents as $event): ?>
                    <div class="list-group-item p-3 border-0 border-bottom">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 text-center me-3">
                                <div class="bg-primary text-white rounded-3 p-2" style="min-width: 55px;">
                                    <span class="d-block fw-bold fs-5"><?= date('d', strtotime($event['event_date'])) ?></span>
                                    <small class="text-white-50"><?= formatDate($event['event_date'], 'M') ?></small>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 fw-semibold text-dark"><?= e($event['title']) ?></h6>
                                <div class="d-flex flex-wrap gap-3">
                                    <small class="text-muted">
                                        <i class="bi bi-clock me-1"></i><?= e($event['event_time'] ?? 'TBA') ?>
                                    </small>
                                    <?php if ($event['location']): ?>
                                    <small class="text-muted">
                                        <i class="bi bi-geo-alt me-1"></i><?= e($event['location']) ?>
                                    </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if (isSuperAdmin() && !empty($recentLogs)): ?>
<!-- Log Aktivitas (Super Admin Only) -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-clock-history text-primary me-2"></i>Log Aktivitas Terbaru
                </h5>
                <a href="<?= url('admin/logs') ?>" class="btn btn-sm btn-primary">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>User</th>
                                <th>Aksi</th>
                                <th>Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentLogs as $log): ?>
                            <tr>
                                <td>
                                    <small class="text-muted">
                                        <?= formatDate($log['created_at'], 'd M Y H:i') ?>
                                    </small>
                                </td>
                                <td class="fw-medium"><?= e($log['user_name'] ?? 'Unknown') ?></td>
                                <td>
                                    <span class="badge bg-primary bg-opacity-10 text-primary"><?= e($log['action']) ?></span>
                                </td>
                                <td class="text-muted"><?= e($log['description']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
