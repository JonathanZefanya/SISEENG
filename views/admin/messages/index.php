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
            <div class="col-6 col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-2"></i>Filter
                </button>
            </div>
            <div class="col-6 col-md-2">
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
                       class="list-group-item list-group-item-action msg-item <?= $message['is_read'] ? '' : 'is-unread' ?>">
                        <div class="msg-avatar">
                            <i class="bi bi-envelope<?= $message['is_read'] ? '-open' : '-fill' ?>"></i>
                        </div>
                        <div class="msg-body">
                            <div class="msg-head">
                                <span class="msg-name"><?= e($message['name']) ?></span>
                                <?php if (!$message['is_read']): ?>
                                    <span class="badge bg-primary">Baru</span>
                                <?php endif; ?>
                                <span class="msg-time" title="<?= e(formatDate($message['created_at'])) ?>">
                                    <?= timeAgo($message['created_at']) ?>
                                    <span class="d-none d-md-inline"> · <?= formatDate($message['created_at']) ?></span>
                                </span>
                            </div>
                            <div class="msg-email"><?= e($message['email']) ?></div>
                            <div class="msg-subject"><?= $message['subject'] ? e($message['subject']) : '(Tanpa Subjek)' ?></div>
                            <div class="msg-preview"><?= e(mb_substr($message['message'], 0, 160)) ?></div>
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

<style>
    .msg-item { display: flex; align-items: flex-start; gap: .9rem; padding: 1rem 1.25rem; border-color: var(--line); }
    .msg-item.is-unread { background: var(--brand-soft); }
    .msg-avatar {
        width: 44px; height: 44px; flex-shrink: 0;
        border-radius: 50%;
        display: grid; place-items: center;
        background: #eceff3; color: var(--muted);
        font-size: 1.1rem;
    }
    .is-unread .msg-avatar { background: var(--brand); color: #fff; }
    .msg-body { flex: 1; min-width: 0; }
    .msg-head { display: flex; align-items: center; gap: .5rem; min-width: 0; }
    .msg-name { font-weight: 800; color: var(--ink); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; min-width: 0; }
    .is-unread .msg-name { color: var(--brand-darker); }
    .msg-head .badge { flex-shrink: 0; font-size: .65rem; }
    .msg-time { margin-left: auto; flex-shrink: 0; font-size: .75rem; color: var(--muted); white-space: nowrap; }
    .msg-email { font-size: .8rem; color: var(--muted); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .msg-subject { margin-top: .2rem; font-weight: 600; color: var(--ink-2); overflow-wrap: anywhere; }
    .is-unread .msg-subject { font-weight: 800; color: var(--ink); }
    .msg-preview {
        font-size: .85rem; color: var(--muted); overflow-wrap: anywhere;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    @media (max-width: 575.98px) {
        .msg-item { padding: .9rem 1rem; gap: .75rem; }
        .msg-avatar { width: 38px; height: 38px; font-size: 1rem; }
    }
</style>

<?php
function timeAgo($datetime) {
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    
    if ($diff->days > 0) {
        return $diff->days . ' hari lalu';
    } elseif ($diff->h > 0) {
        return $diff->h . ' jam lalu';
    } elseif ($diff->i > 0) {
        return $diff->i . ' menit lalu';
    } else {
        return 'Baru saja';
    }
}
?>
