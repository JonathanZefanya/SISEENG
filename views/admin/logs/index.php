<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 fw-bold">Log Aktivitas</h1>
        <p class="text-muted mb-0">Riwayat aktivitas pengguna sistem</p>
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
