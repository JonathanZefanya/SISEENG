<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 fw-bold">Jadwal Ibadah</h1>
        <p class="text-muted mb-0">Kelola jadwal ibadah gereja</p>
    </div>
    <a href="<?= url('admin/jadwal/tambah') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-2"></i>Tambah Jadwal
    </a>
</div>

<!-- Schedules Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Nama Ibadah</th>
                        <th>Hari</th>
                        <th>Waktu</th>
                        <th>Lokasi</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($schedules)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                            Belum ada jadwal ibadah
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($schedules as $schedule): ?>
                        <tr>
                            <td class="ps-4">
                                <span class="fw-semibold"><?= e($schedule['title']) ?></span>
                                <?php if ($schedule['description']): ?>
                                <br><small class="text-muted"><?= e(truncate($schedule['description'], 50)) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-primary"><?= e($schedule['day_of_week']) ?></span>
                            </td>
                            <td>
                                <?= e($schedule['start_time']) ?>
                                <?= $schedule['end_time'] ? ' - ' . e($schedule['end_time']) : '' ?>
                            </td>
                            <td><?= e($schedule['location'] ?: '-') ?></td>
                            <td>
                                <?php if ($schedule['is_active']): ?>
                                <span class="badge bg-success">Aktif</span>
                                <?php else: ?>
                                <span class="badge bg-secondary">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= url('admin/jadwal/edit/' . $schedule['id']) ?>" class="btn btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="<?= url('admin/jadwal/hapus/' . $schedule['id']) ?>" method="POST" class="d-inline">
                                        <?= csrfField() ?>
                                        <button type="submit" class="btn btn-outline-danger" title="Hapus"
                                                onclick="return confirm('Yakin ingin menghapus jadwal ini?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
