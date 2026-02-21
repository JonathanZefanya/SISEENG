<!-- Page Header -->
<div class="d-flex justify-content-between align-items-start align-items-md-center mb-4 flex-column flex-md-row gap-3">
    <div>
        <h1 class="h3 mb-0 fw-bold">Log Aktivitas</h1>
        <p class="text-muted mb-0">
            Riwayat aktivitas pengguna sistem
            <?php if (!empty($totalLogs)): ?>
                &mdash; <span class="fw-semibold text-primary"><?= number_format($totalLogs) ?> total log</span>
            <?php endif; ?>
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <!-- Hapus Log Lama -->
        <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal"
            data-bs-target="#modalClearOld">
            <i class="bi bi-clock-history me-1"></i>Hapus Log Lama
        </button>
        <!-- Hapus Semua -->
        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalClearAll">
            <i class="bi bi-trash3 me-1"></i>Hapus Semua
        </button>
    </div>
</div>

<!-- Modal: Hapus Semua -->
<div class="modal fade" id="modalClearAll" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Hapus Semua Log?
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-2">
                <p class="text-muted mb-0">
                    Semua <strong><?= number_format($totalLogs ?? 0) ?> log aktivitas</strong> akan dihapus permanen.
                    Tindakan ini <strong>tidak bisa dibatalkan</strong>.
                </p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="<?= url('admin/logs/clear') ?>" method="POST">
                    <?= csrfField() ?>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash3 me-1"></i>Ya, Hapus Semua
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Hapus Log Lama -->
<div class="modal fade" id="modalClearOld" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-warning">
                    <i class="bi bi-clock-history me-2"></i>Hapus Log Lama
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= url('admin/logs/clear-old') ?>" method="POST">
                <?= csrfField() ?>
                <div class="modal-body pt-2">
                    <p class="text-muted mb-3">Hapus log aktivitas yang lebih dari:</p>
                    <div class="d-flex align-items-center gap-3">
                        <input type="number" name="days" class="form-control form-control-lg" style="max-width: 110px;"
                            value="30" min="1" max="365" required>
                        <span class="fw-semibold">hari yang lalu</span>
                    </div>
                    <small class="text-muted mt-2 d-block">Contoh: isi 30 untuk hapus log lebih dari 30 hari
                        lalu.</small>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-white">
                        <i class="bi bi-clock-history me-1"></i>Hapus Log Lama
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Logs Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Waktu</th>
                        <th>User</th>
                        <th>Aksi</th>
                        <th>Deskripsi</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-clock-history fs-1 d-block mb-2"></i>
                                Belum ada log aktivitas
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td class="ps-4">
                                    <small class="text-muted">
                                        <?= formatDate($log['created_at'], 'd M Y') ?><br>
                                        <?= date('H:i:s', strtotime($log['created_at'])) ?>
                                    </small>
                                </td>
                                <td>
                                    <span class="fw-semibold"><?= e($log['user_name'] ?? 'Unknown') ?></span>
                                    <?php if (!empty($log['user_email'])): ?>
                                        <br><small class="text-muted"><?= e($log['user_email']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $actionColors = [
                                        'login' => 'success',
                                        'logout' => 'secondary',
                                        'create' => 'primary',
                                        'update' => 'info',
                                        'delete' => 'danger',
                                    ];
                                    $color = 'secondary';
                                    foreach ($actionColors as $key => $val) {
                                        if (strpos($log['action'], $key) !== false) {
                                            $color = $val;
                                            break;
                                        }
                                    }
                                    ?>
                                    <span class="badge bg-<?= $color ?>"><?= e($log['action']) ?></span>
                                </td>
                                <td><?= e($log['description']) ?></td>
                                <td><small class="text-muted"><?= e($log['ip_address']) ?></small></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pagination -->
<?php if ($pagination['last_page'] > 1): ?>
    <nav class="mt-4">
        <ul class="pagination justify-content-center">
            <li class="page-item <?= $pagination['current_page'] <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= url('admin/logs?page=' . ($pagination['current_page'] - 1)) ?>">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>

            <?php for ($i = 1; $i <= $pagination['last_page']; $i++): ?>
                <li class="page-item <?= $pagination['current_page'] == $i ? 'active' : '' ?>">
                    <a class="page-link" href="<?= url('admin/logs?page=' . $i) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <li class="page-item <?= $pagination['current_page'] >= $pagination['last_page'] ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= url('admin/logs?page=' . ($pagination['current_page'] + 1)) ?>">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        </ul>
    </nav>
<?php endif; ?>