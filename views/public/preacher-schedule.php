<!-- Hero Section -->
<section class="bg-primary text-white py-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="display-5 fw-bold mb-2">
                    <i class="bi bi-calendar-week me-3"></i>Jadwal Pengkhotbah
                </h1>
                <p class="lead mb-0">Jadwal pelayan firman bulanan</p>
            </div>
        </div>
    </div>
</section>

<!-- Breadcrumb -->
<nav class="bg-light py-3">
    <div class="container">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= url() ?>">Beranda</a></li>
            <li class="breadcrumb-item active">Jadwal Pengkhotbah</li>
        </ol>
    </div>
</nav>

<!-- Main Content -->
<section class="py-5">
    <div class="container">
        
        <!-- Month Navigation -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <a href="<?= url('jadwal-pengkhotbah?month=' . $prevMonth) ?>" class="btn btn-outline-primary">
                        <i class="bi bi-chevron-left me-2"></i>Bulan Sebelumnya
                    </a>
                    
                    <h3 class="mb-0 fw-bold text-primary">
                        <i class="bi bi-calendar3 me-2"></i>
                        <?= $monthName ?> <?= $year ?>
                    </h3>
                    
                    <a href="<?= url('jadwal-pengkhotbah?month=' . $nextMonth) ?>" class="btn btn-outline-primary">
                        Bulan Berikutnya<i class="bi bi-chevron-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <?php if (empty($schedules)): ?>
        <!-- Empty State -->
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-calendar-x text-muted" style="font-size: 5rem;"></i>
                <h4 class="mt-4 text-muted">Belum Ada Jadwal</h4>
                <p class="text-muted mb-0">Jadwal pengkhotbah untuk bulan <?= $monthName ?> <?= $year ?> belum tersedia.</p>
            </div>
        </div>
        <?php else: ?>
        
        <!-- Schedule Calendar View -->
        <div class="row g-4">
            <?php foreach ($schedules as $date => $daySchedules): 
                $dateObj = new DateTime($date);
                $dayOfWeek = $dateObj->format('w');
                $isToday = $date === date('Y-m-d');
                $isPast = strtotime($date) < strtotime('today');
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm <?= $isToday ? 'border-primary border-2' : '' ?> <?= $isPast ? 'opacity-75' : '' ?>">
                    <div class="card-header <?= $isToday ? 'bg-primary text-white' : ($dayOfWeek == 0 ? 'bg-danger text-white' : 'bg-light') ?> py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge <?= $isToday ? 'bg-white text-primary' : ($dayOfWeek == 0 ? 'bg-white text-danger' : 'bg-secondary') ?> mb-1">
                                    <?= getDayName($dayOfWeek) ?>
                                </span>
                                <h5 class="mb-0 fw-bold"><?= $dateObj->format('d F Y') ?></h5>
                            </div>
                            <?php if ($isToday): ?>
                            <span class="badge bg-warning text-dark">Hari Ini</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <?php foreach ($daySchedules as $schedule): ?>
                            <li class="list-group-item py-3">
                                <div class="d-flex align-items-start">
                                    <div class="bg-primary text-white rounded px-2 py-1 me-3 text-center" style="min-width: 60px;">
                                        <small class="fw-bold"><?= date('H:i', strtotime($schedule['service_time'])) ?></small>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-bold"><?= e($schedule['service_name']) ?></h6>
                                        <p class="mb-1 fs-5">
                                            <?php if ($schedule['preacher_name']): ?>
                                            <i class="bi bi-person-fill text-success me-1"></i>
                                            <strong><?= e($schedule['preacher_name']) ?></strong>
                                            <?php else: ?>
                                            <i class="bi bi-question-circle text-warning me-1"></i>
                                            <span class="text-muted fst-italic">Belum Ditentukan</span>
                                            <?php endif; ?>
                                        </p>
                                        <?php if ($schedule['sermon_title']): ?>
                                        <p class="mb-0 text-muted small">
                                            <i class="bi bi-chat-quote me-1"></i>
                                            <em>"<?= e($schedule['sermon_title']) ?>"</em>
                                        </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php endif; ?>
        
        <!-- Info Section -->
        <div class="row mt-5">
            <div class="col-lg-8 mx-auto">
                <div class="card border-0 bg-light">
                    <div class="card-body text-center py-4">
                        <i class="bi bi-info-circle text-primary fs-1 mb-3"></i>
                        <h5 class="fw-bold">Informasi</h5>
                        <p class="text-muted mb-0">
                            Jadwal pengkhotbah dapat berubah sewaktu-waktu. 
                            Untuk informasi lebih lanjut, silakan hubungi sekretariat gereja.
                        </p>
                        <a href="<?= url('kontak') ?>" class="btn btn-primary mt-3">
                            <i class="bi bi-telephone me-2"></i>Hubungi Kami
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</section>
