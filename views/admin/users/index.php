<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 fw-bold">Kelola Admin</h1>
        <p class="text-muted mb-0">Tambah, edit, dan kelola akun admin sistem</p>
    </div>
    <a href="<?= url('admin/users/tambah') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-2"></i>Tambah Admin
    </a>
</div>

<!-- Users Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="min-width: 650px;">
                <colgroup>
                    <col style="width: 220px;"> <!-- Nama -->
                    <col> <!-- Email -->
                    <col style="width: 110px;"> <!-- Role -->
                    <col style="width: 90px;"> <!-- Status -->
                    <col style="width: 130px;"> <!-- Login Terakhir -->
                    <col style="width: 120px;"> <!-- Aksi -->
                </colgroup>
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Login Terakhir</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-people fs-1 d-block mb-2"></i>
                                Belum ada data admin
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <!-- Nama -->
                                <td class="ps-4" style="white-space: nowrap;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="flex-shrink-0 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                            style="width:38px; height:38px; font-size:.95rem;">
                                            <?= strtoupper(substr($user['name'], 0, 1)) ?>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="fw-semibold text-truncate" style="max-width:140px;">
                                                <?= e($user['name']) ?>
                                            </div>
                                            <?php if ($user['id'] == auth('id')): ?>
                                                <span class="badge bg-info" style="font-size:.65rem;">Anda</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>

                                <!-- Email -->
                                <td class="text-muted" style="max-width:200px;">
                                    <span class="text-truncate d-block"><?= e($user['email']) ?></span>
                                </td>

                                <!-- Role -->
                                <td>
                                    <?php if ($user['role'] === ROLE_SUPER_ADMIN): ?>
                                        <span class="badge bg-danger">Super Admin</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary">Admin</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Status -->
                                <td>
                                    <?php if ($user['status'] === 'active'): ?>
                                        <span class="badge bg-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Diblokir</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Login Terakhir -->
                                <td style="white-space: nowrap;">
                                    <?php if ($user['last_login']): ?>
                                        <small class="text-muted"><?= formatDate($user['last_login'], 'd M Y H:i') ?></small>
                                    <?php else: ?>
                                        <small class="text-muted">Belum pernah login</small>
                                    <?php endif; ?>
                                </td>

                                <!-- Aksi -->
                                <td class="text-end pe-4" style="white-space: nowrap;">
                                    <?php if ($user['id'] != auth('id')): ?>
                                        <div class="d-inline-flex gap-1">
                                            <a href="<?= url('admin/users/edit/' . $user['id']) ?>"
                                                class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>

                                            <form action="<?= url('admin/users/toggle-status/' . $user['id']) ?>" method="POST"
                                                class="d-inline">
                                                <?= csrfField() ?>
                                                <button type="submit"
                                                    class="btn btn-sm btn-outline-<?= $user['status'] === 'active' ? 'warning' : 'success' ?>"
                                                    title="<?= $user['status'] === 'active' ? 'Blokir' : 'Aktifkan' ?>"
                                                    onclick="return confirm('<?= $user['status'] === 'active' ? 'Blokir user ini?' : 'Aktifkan user ini?' ?>')">
                                                    <i
                                                        class="bi bi-<?= $user['status'] === 'active' ? 'slash-circle' : 'check-circle' ?>"></i>
                                                </button>
                                            </form>

                                            <form action="<?= url('admin/users/hapus/' . $user['id']) ?>" method="POST"
                                                class="d-inline">
                                                <?= csrfField() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"
                                                    onclick="return confirm('Yakin ingin menghapus user ini?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small">—</span>
                                    <?php endif; ?>
                                </td>
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
                <a class="page-link" href="<?= url('admin/users?page=' . ($pagination['current_page'] - 1)) ?>">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>

            <?php for ($i = 1; $i <= $pagination['last_page']; $i++): ?>
                <li class="page-item <?= $pagination['current_page'] == $i ? 'active' : '' ?>">
                    <a class="page-link" href="<?= url('admin/users?page=' . $i) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <li class="page-item <?= $pagination['current_page'] >= $pagination['last_page'] ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= url('admin/users?page=' . ($pagination['current_page'] + 1)) ?>">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        </ul>
    </nav>
<?php endif; ?>