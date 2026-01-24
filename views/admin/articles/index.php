<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">Kelola Artikel</h1>
        <p class="text-muted mb-0">Buat dan kelola artikel untuk website gereja</p>
    </div>
    <a href="<?= url('admin/articles/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-2"></i>Tambah Artikel
    </a>
</div>

<!-- Filter Kategori -->
<?php if (!empty($categories)): ?>
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="text-muted me-2"><i class="bi bi-funnel me-1"></i>Filter:</span>
            <a href="<?= url('admin/artikel') ?>" 
               class="btn btn-sm <?= empty($selectedCategory) ? 'btn-primary' : 'btn-outline-secondary' ?>">
                Semua
            </a>
            <?php foreach ($categories as $cat): ?>
            <a href="<?= url('admin/artikel?category=' . $cat['id']) ?>" 
               class="btn btn-sm <?= ($selectedCategory ?? 0) == $cat['id'] ? 'btn-primary' : 'btn-outline-secondary' ?>"
               style="<?= ($selectedCategory ?? 0) == $cat['id'] ? 'background-color: ' . e($cat['color']) . '; border-color: ' . e($cat['color']) : '' ?>">
                <?= e($cat['name']) ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <?php if (empty($articles)): ?>
            <div class="text-center py-5">
                <i class="bi bi-journal-x text-muted display-1"></i>
                <h4 class="mt-3 text-muted">Belum ada artikel</h4>
                <p class="text-muted">Klik tombol "Tambah Artikel" untuk membuat artikel baru</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Artikel</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Tanggal Dibuat</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($articles as $article): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <?php if ($article['image']): ?>
                                            <img src="<?= asset('uploads/articles/' . e($article['image'])) ?>" 
                                                 alt="<?= e($article['title']) ?>"
                                                 class="rounded me-3"
                                                 style="width: 60px; height: 60px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-secondary text-white rounded me-3 d-flex align-items-center justify-content-center" 
                                                 style="width: 60px; height: 60px;">
                                                <i class="bi bi-journal-text fs-4"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <h6 class="mb-1 fw-semibold"><?= e($article['title']) ?></h6>
                                            <small class="text-muted">
                                                <?= e(substr(strip_tags($article['content']), 0, 60)) ?>...
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if (!empty($article['category_name'])): ?>
                                        <span class="badge" style="background-color: <?= e($article['category_color'] ?? '#6c757d') ?>">
                                            <?= e($article['category_name']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($article['status'] === 'published'): ?>
                                        <span class="badge bg-success">Dipublikasi</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Draft</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?= formatDate($article['created_at']) ?>
                                    </small>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <a href="<?= url('artikel/' . $article['slug']) ?>" 
                                           target="_blank"
                                           class="btn btn-sm btn-outline-secondary" 
                                           title="Lihat">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="<?= url('admin/articles/edit/' . $article['id']) ?>" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                title="Hapus"
                                                onclick="confirmDelete(<?= $article['id'] ?>, '<?= e($article['title']) ?>')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if (isset($pagination) && isset($pagination['total_pages']) && $pagination['total_pages'] > 1): ?>
                <div class="card-footer bg-white border-top">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center mb-0">
                            <li class="page-item <?= $pagination['current_page'] <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= url('admin/articles?page=' . ($pagination['current_page'] - 1)) ?>">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>
                            
                            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                                <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= url('admin/articles?page=' . $i) ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <li class="page-item <?= $pagination['current_page'] >= $pagination['total_pages'] ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= url('admin/articles?page=' . ($pagination['current_page'] + 1)) ?>">
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="bi bi-exclamation-triangle text-warning display-3"></i>
                <h4 class="mt-3">Apakah Anda yakin?</h4>
                <p class="text-muted mb-0">Artikel "<span id="articleTitle"></span>" akan dihapus permanen.</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST">
                    <?= csrfField() ?>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-2"></i>Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, title) {
    document.getElementById('articleTitle').textContent = title;
    document.getElementById('deleteForm').action = '<?= url('admin/articles/delete/') ?>' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
