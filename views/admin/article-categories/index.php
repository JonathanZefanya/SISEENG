<?php
/**
 * =========================================================
 * Halaman Daftar Kategori Artikel
 * =========================================================
 */
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">
            <i class="bi bi-tags me-2 text-primary"></i>Kategori Artikel
        </h1>
        <p class="text-muted mb-0">Kelola kategori untuk artikel gereja</p>
    </div>
    <a href="<?= url('admin/kategori-artikel/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Tambah Kategori
    </a>
</div>

<?php if ($flash = getFlash('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i><?= $flash ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($flash = getFlash('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i><?= $flash ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <?php if (empty($categories)): ?>
            <div class="text-center py-5">
                <i class="bi bi-tags display-4 text-muted"></i>
                <p class="text-muted mt-3 mb-0">Belum ada kategori. <a href="<?= url('admin/kategori-artikel/create') ?>">Tambah kategori baru</a></p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">#</th>
                            <th width="30%">Kategori</th>
                            <th width="30%">Deskripsi</th>
                            <th width="10%">Warna</th>
                            <th width="10%" class="text-center">Artikel</th>
                            <th width="10%" class="text-center">Status</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $index => $category): ?>
                            <tr>
                                <td class="align-middle"><?= $index + 1 ?></td>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        <span class="badge me-2" style="background-color: <?= e($category['color'] ?? '#6c757d') ?>; width: 12px; height: 12px; padding: 0; border-radius: 50%;"></span>
                                        <div>
                                            <span class="fw-medium"><?= e($category['name']) ?></span>
                                            <small class="d-block text-muted"><?= e($category['slug']) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <small class="text-muted">
                                        <?= $category['description'] ? e(substr($category['description'], 0, 50)) . (strlen($category['description']) > 50 ? '...' : '') : '-' ?>
                                    </small>
                                </td>
                                <td class="align-middle">
                                    <span class="badge" style="background-color: <?= e($category['color'] ?? '#6c757d') ?>">
                                        <?= e($category['color'] ?? '#6c757d') ?>
                                    </span>
                                </td>
                                <td class="align-middle text-center">
                                    <span class="badge bg-secondary rounded-pill"><?= $category['article_count'] ?? 0 ?></span>
                                </td>
                                <td class="align-middle text-center">
                                    <form action="<?= url('admin/kategori-artikel/toggle-status/' . $category['id']) ?>" method="POST" class="d-inline">
                                        <?= csrfField() ?>
                                        <button type="submit" class="btn btn-sm <?= $category['is_active'] ? 'btn-success' : 'btn-secondary' ?>" title="<?= $category['is_active'] ? 'Aktif' : 'Nonaktif' ?>">
                                            <i class="bi bi-<?= $category['is_active'] ? 'check-circle' : 'x-circle' ?>"></i>
                                        </button>
                                    </form>
                                </td>
                                <td class="align-middle text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= url('admin/kategori-artikel/edit/' . $category['id']) ?>" class="btn btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="confirmDelete(<?= $category['id'] ?>, '<?= e($category['name']) ?>')" 
                                                title="Hapus"
                                                <?= ($category['article_count'] ?? 0) > 0 ? 'disabled' : '' ?>>
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
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle text-danger me-2"></i>Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus kategori <strong id="categoryName"></strong>?</p>
                <p class="text-muted small mb-0">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    <?= csrfField() ?>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, name) {
    document.getElementById('categoryName').textContent = name;
    document.getElementById('deleteForm').action = '<?= url('admin/kategori-artikel/delete/') ?>' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
