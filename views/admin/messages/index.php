<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">Pesan Masuk</h1>
        <p class="text-muted mb-0">Kelola pesan dari pengunjung website</p>
    </div>
</div>

<!-- Filter -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="<?= url('admin/messages') ?>" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label">Cari</label>
                <input type="text" class="form-control" name="search" 
                       value="<?= e($_GET['search'] ?? '') ?>"
                       placeholder="Nama, email, atau subjek">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="">Semua Status</option>
                    <option value="unread" <?= ($_GET['status'] ?? '') === 'unread' ? 'selected' : '' ?>>Belum Dibaca</option>
                    <option value="read" <?= ($_GET['status'] ?? '') === 'read' ? 'selected' : '' ?>>Sudah Dibaca</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-2"></i>Filter
                </button>
            </div>
            <div class="col-md-2">
                <a href="<?= url('admin/messages') ?>" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <?php if (empty($messages)): ?>
            <div class="text-center py-5">
                <i class="bi bi-envelope-open text-muted display-1"></i>
                <h4 class="mt-3 text-muted">Tidak ada pesan</h4>
                <p class="text-muted">Belum ada pesan masuk dari pengunjung website</p>
            </div>
        <?php else: ?>
            <div class="list-group list-group-flush">
                <?php foreach ($messages as $message): ?>
                    <a href="<?= url('admin/messages/read/' . $message['id']) ?>" 
                       class="list-group-item list-group-item-action p-4 <?= $message['is_read'] ? '' : 'bg-light' ?>">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="d-flex align-items-start">
                                <div class="bg-<?= $message['is_read'] ? 'secondary' : 'primary' ?> text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                                     style="width: 50px; height: 50px; flex-shrink: 0;">
                                    <i class="bi bi-envelope<?= $message['is_read'] ? '-open' : '-fill' ?>"></i>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center mb-1">
                                        <h6 class="mb-0 fw-bold <?= $message['is_read'] ? '' : 'text-primary' ?>">
                                            <?= e($message['name']) ?>
                                        </h6>
                                        <?php if (!$message['is_read']): ?>
                                            <span class="badge bg-primary ms-2">Baru</span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="mb-1 text-muted small"><?= e($message['email']) ?></p>
                                    <p class="mb-1 <?= $message['is_read'] ? 'text-muted' : 'fw-semibold' ?>">
                                        <?= $message['subject'] ? e($message['subject']) : '(Tanpa Subjek)' ?>
                                    </p>
                                    <p class="mb-0 text-muted small">
                                        <?= e(substr($message['message'], 0, 100)) ?>...
                                    </p>
                                </div>
                            </div>
                            <div class="text-end ms-3" style="flex-shrink: 0;">
                                <small class="text-muted d-block">
                                    <?= timeAgo($message['created_at']) ?>
                                </small>
                                <small class="text-muted">
                                    <?= formatDate($message['created_at']) ?>
                                </small>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <?php if (isset($pagination) && isset($pagination['total_pages']) && $pagination['total_pages'] > 1): ?>
                <div class="card-footer bg-white border-top">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center mb-0">
                            <li class="page-item <?= $pagination['current_page'] <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= url('admin/messages?page=' . ($pagination['current_page'] - 1)) ?>">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>
                            
                            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                                <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= url('admin/messages?page=' . $i) ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <li class="page-item <?= $pagination['current_page'] >= $pagination['total_pages'] ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= url('admin/messages?page=' . ($pagination['current_page'] + 1)) ?>">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php
function timeAgo($datetime) {
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    
    if ($diff->d > 0) {
        return $diff->d . ' hari lalu';
    } elseif ($diff->h > 0) {
        return $diff->h . ' jam lalu';
    } elseif ($diff->i > 0) {
        return $diff->i . ' menit lalu';
    } else {
        return 'Baru saja';
    }
}
?>
