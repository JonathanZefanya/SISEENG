<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 fw-bold">
            <i class="bi bi-calendar-week text-primary me-2"></i>
            Jadwal Pengkhotbah
        </h1>
        <p class="text-muted mb-0">Kelola jadwal pengkhotbah bulanan</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#generateModal">
            <i class="bi bi-calendar-plus me-2"></i>Generate Jadwal
        </button>
        <a href="<?= url('admin/jadwal-pengkhotbah/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>Tambah Jadwal
        </a>
    </div>
</div>

<!-- Filter Bulan -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" action="<?= url('admin/jadwal-pengkhotbah') ?>" class="row g-3 align-items-center">
            <div class="col-auto">
                <label class="form-label mb-0 fw-semibold">Filter Bulan:</label>
            </div>
            <div class="col-auto">
                <input type="month" name="month" class="form-control" value="<?= e($currentMonth) ?>">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary">
                    <i class="bi bi-filter me-1"></i>Filter
                </button>
            </div>
            <div class="col-auto">
                <a href="<?= url('admin/jadwal-pengkhotbah') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Jadwal Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-list-ul me-2 text-primary"></i>
            Daftar Jadwal - <?= date('F Y', strtotime($currentMonth . '-01')) ?>
        </h5>
        <span class="badge bg-primary"><?= $pagination['count'] ?> jadwal</span>
    </div>
    <div class="card-body p-0">
        <?php if (empty($schedules)): ?>
        <div class="text-center py-5">
            <i class="bi bi-calendar-x text-muted" style="font-size: 4rem;"></i>
            <p class="text-muted mt-3 mb-0">Belum ada jadwal untuk bulan ini.</p>
            <p class="text-muted">Klik tombol "Generate Jadwal" untuk membuat jadwal otomatis.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 120px;">Tanggal</th>
                        <th>Hari</th>
                        <th>Ibadah</th>
                        <th>Waktu</th>
                        <th>Pengkhotbah</th>
                        <th>Judul Khotbah</th>
                        <th style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $lastDate = '';
                    foreach ($schedules as $schedule): 
                        $isNewDate = $schedule['schedule_date'] !== $lastDate;
                        $lastDate = $schedule['schedule_date'];
                        $dayName = getDayName(date('w', strtotime($schedule['schedule_date'])));
                        $isPast = strtotime($schedule['schedule_date']) < strtotime('today');
                    ?>
                    <tr class="<?= $isPast ? 'table-secondary' : '' ?>">
                        <td>
                            <?php if ($isNewDate): ?>
                            <span class="fw-bold"><?= date('d M Y', strtotime($schedule['schedule_date'])) ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($isNewDate): ?>
                            <span class="badge bg-<?= date('w', strtotime($schedule['schedule_date'])) == 0 ? 'danger' : 'secondary' ?>">
                                <?= $dayName ?>
                            </span>
                            <?php endif; ?>
                        </td>
                        <td><strong><?= e($schedule['service_name']) ?></strong></td>
                        <td>
                            <i class="bi bi-clock text-muted me-1"></i>
                            <?= date('H:i', strtotime($schedule['service_time'])) ?> WIB
                        </td>
                        <td>
                            <?php if ($schedule['preacher_name']): ?>
                            <span class="text-success fw-semibold">
                                <i class="bi bi-person-check me-1"></i><?= e($schedule['preacher_name']) ?>
                            </span>
                            <?php else: ?>
                            <span class="text-warning">
                                <i class="bi bi-question-circle me-1"></i>Belum ditentukan
                            </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($schedule['sermon_title']): ?>
                            <em><?= e($schedule['sermon_title']) ?></em>
                            <?php else: ?>
                            <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?= url('admin/jadwal-pengkhotbah/edit/' . $schedule['id']) ?>" 
                                   class="btn btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger" 
                                        onclick="confirmDelete(<?= $schedule['id'] ?>)" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
    
    <?php if ($pagination['total'] > 1): ?>
    <div class="card-footer bg-white">
        <nav>
            <ul class="pagination pagination-sm justify-content-center mb-0">
                <?php for ($i = 1; $i <= $pagination['total']; $i++): ?>
                <li class="page-item <?= $i == $pagination['current'] ? 'active' : '' ?>">
                    <a class="page-link" href="<?= url('admin/jadwal-pengkhotbah?page=' . $i . '&month=' . $currentMonth) ?>">
                        <?= $i ?>
                    </a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Generate Jadwal -->
<div class="modal fade" id="generateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= url('admin/jadwal-pengkhotbah/generate') ?>" method="POST">
                <?= csrfField() ?>
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-calendar-plus me-2"></i>Generate Jadwal Bulanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">
                        Generate jadwal ibadah hari Minggu secara otomatis untuk 1 bulan.
                        Jadwal yang sudah ada tidak akan ditimpa.
                    </p>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Bulan</label>
                            <select name="month" class="form-select" required>
                                <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?= $m ?>" <?= $m == date('n') ? 'selected' : '' ?>>
                                    <?= getMonthName($m) ?>
                                </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Tahun</label>
                            <select name="year" class="form-select" required>
                                <?php for ($y = date('Y'); $y <= date('Y') + 2; $y++): ?>
                                <option value="<?= $y ?>"><?= $y ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-2"></i>Generate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Delete Confirmation -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form id="deleteForm" method="POST">
                <?= csrfField() ?>
                <div class="modal-header">
                    <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Hapus Jadwal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus jadwal ini?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function confirmDelete(id) {
    document.getElementById('deleteForm').action = '<?= url('admin/jadwal-pengkhotbah/delete/') ?>' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
