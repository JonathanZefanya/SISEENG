<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">Data Jemaat</h1>
        <p class="text-muted mb-0">Kelola data anggota jemaat gereja</p>
    </div>
    <a href="<?= url('admin/members/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-2"></i>Tambah Jemaat
    </a>
</div>

<!-- Filter -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="<?= url('admin/members') ?>" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Cari</label>
                <input type="text" class="form-control" name="search" 
                       value="<?= e($_GET['search'] ?? '') ?>"
                       placeholder="Nama atau No. Telepon">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="">Semua Status</option>
                    <option value="active" <?= ($_GET['status'] ?? '') === 'active' ? 'selected' : '' ?>>Aktif</option>
                    <option value="inactive" <?= ($_GET['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Tidak Aktif</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Jenis Kelamin</label>
                <select class="form-select" name="gender">
                    <option value="">Semua</option>
                    <option value="M" <?= ($_GET['gender'] ?? '') === 'M' ? 'selected' : '' ?>>Laki-laki</option>
                    <option value="F" <?= ($_GET['gender'] ?? '') === 'F' ? 'selected' : '' ?>>Perempuan</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-2"></i>Filter
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <?php if (empty($members)): ?>
            <div class="text-center py-5">
                <i class="bi bi-people text-muted display-1"></i>
                <h4 class="mt-3 text-muted">Belum ada data jemaat</h4>
                <p class="text-muted">Klik tombol "Tambah Jemaat" untuk menambahkan data baru</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Nama</th>
                            <th>Kontak</th>
                            <th>Tanggal Lahir</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($members as $member): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-<?= $member['gender'] === 'M' ? 'primary' : 'pink' ?> text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                                             style="width: 45px; height: 45px; <?= $member['gender'] === 'F' ? 'background-color: #e91e63;' : '' ?>">
                                            <i class="bi bi-<?= $member['gender'] === 'M' ? 'person' : 'person-fill' ?>"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-semibold"><?= e($member['full_name']) ?></h6>
                                            <small class="text-muted">
                                                <?= $member['gender'] === 'M' ? 'Laki-laki' : 'Perempuan' ?>
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($member['phone']): ?>
                                        <div><i class="bi bi-telephone text-primary me-1"></i> <?= e($member['phone']) ?></div>
                                    <?php endif; ?>
                                    <?php if ($member['email']): ?>
                                        <small class="text-muted"><i class="bi bi-envelope me-1"></i> <?= e($member['email']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($member['birth_date']): ?>
                                        <?= formatDate($member['birth_date']) ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($member['status'] === 'active'): ?>
                                        <span class="badge bg-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Tidak Aktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <a href="<?= url('admin/members/show/' . $member['id']) ?>" 
                                           class="btn btn-sm btn-outline-secondary" 
                                           title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="<?= url('admin/members/edit/' . $member['id']) ?>" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                title="Hapus"
                                                onclick="confirmDelete(<?= $member['id'] ?>, '<?= e($member['full_name']) ?>')">
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
                                <a class="page-link" href="<?= url('admin/members?page=' . ($pagination['current_page'] - 1)) ?>">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>
                            
                            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                                <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= url('admin/members?page=' . $i) ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <li class="page-item <?= $pagination['current_page'] >= $pagination['total_pages'] ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= url('admin/members?page=' . ($pagination['current_page'] + 1)) ?>">
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
                <p class="text-muted mb-0">Data jemaat "<span id="memberName"></span>" akan dihapus permanen.</p>
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
function confirmDelete(id, name) {
    document.getElementById('memberName').textContent = name;
    document.getElementById('deleteForm').action = '<?= url('admin/members/delete/') ?>' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
