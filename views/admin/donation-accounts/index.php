<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 fw-bold">Rekening Donasi</h1>
        <p class="text-muted mb-0">Kelola rekening bank untuk donasi</p>
    </div>
    <a href="<?= url('admin/rekening-donasi/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-2"></i>Tambah Rekening
    </a>
</div>

<!-- QRIS Section -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold"><i class="bi bi-qr-code me-2 text-primary"></i>QRIS (Scan & Pay)</h5>
    </div>
    <div class="card-body p-4">
        <form action="<?= url('admin/rekening-donasi/update-qris') ?>" method="POST" enctype="multipart/form-data">
            <?= csrfField() ?>
            <div class="row g-4">
                <div class="col-md-6">
                    <label for="donation_qris_image" class="form-label fw-semibold">Upload Gambar QRIS</label>
                    <input type="file" class="form-control form-control-lg" id="donation_qris_image" name="donation_qris_image" accept="image/*">
                    <small class="text-muted">Format: JPG, PNG. Ukuran maksimal: 2MB. Rekomendasi: 500x500 px</small>
                </div>
                <div class="col-md-3">
                    <label for="donation_qris_name" class="form-label fw-semibold">Nama QRIS (Opsional)</label>
                    <input type="text" class="form-control form-control-lg" id="donation_qris_name" name="donation_qris_name" 
                           value="<?= e(setting('donation_qris_name') ?? '') ?>" placeholder="Contoh: GBI Ciseeng">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">QRIS Saat Ini</label>
                    <div class="p-3 bg-light rounded text-center">
                        <?php if (!empty(setting('donation_qris_image'))): ?>
                        <img src="<?= uploads('donations/' . setting('donation_qris_image')) ?>" alt="QRIS" style="max-height: 120px;">
                        <?php else: ?>
                        <i class="bi bi-qr-code text-muted" style="font-size: 3rem;"></i>
                        <p class="small text-muted mb-0">Belum ada QRIS</p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-2"></i>Simpan QRIS
                    </button>
                    <?php if (!empty(setting('donation_qris_image'))): ?>
                    <button type="submit" name="delete_qris" value="1" class="btn btn-outline-danger ms-2" onclick="return confirm('Hapus gambar QRIS?')">
                        <i class="bi bi-trash me-2"></i>Hapus QRIS
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Bank Accounts List -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold"><i class="bi bi-bank me-2 text-primary"></i>Daftar Rekening Bank</h5>
    </div>
    <div class="card-body p-0">
        <?php if (empty($accounts)): ?>
        <div class="text-center py-5">
            <i class="bi bi-bank text-muted" style="font-size: 4rem;"></i>
            <p class="text-muted mt-3 mb-0">Belum ada rekening donasi</p>
            <a href="<?= url('admin/rekening-donasi/create') ?>" class="btn btn-primary mt-3">
                <i class="bi bi-plus-lg me-2"></i>Tambah Rekening Pertama
            </a>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th width="60">Urutan</th>
                        <th>Bank</th>
                        <th>No. Rekening</th>
                        <th>Atas Nama</th>
                        <th width="100">Status</th>
                        <th width="150" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($accounts as $account): ?>
                    <tr>
                        <td>
                            <span class="badge bg-secondary"><?= $account['sort_order'] ?></span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <?php if ($account['bank_logo']): ?>
                                <img src="<?= uploads('donations/' . $account['bank_logo']) ?>" alt="<?= e($account['bank_name']) ?>" 
                                     class="me-2 rounded" style="width: 40px; height: 40px; object-fit: contain;">
                                <?php else: ?>
                                <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center me-2" 
                                     style="width: 40px; height: 40px;">
                                    <i class="bi bi-bank"></i>
                                </div>
                                <?php endif; ?>
                                <strong><?= e($account['bank_name']) ?></strong>
                            </div>
                        </td>
                        <td>
                            <code class="fs-6"><?= e($account['account_number']) ?></code>
                        </td>
                        <td><?= e($account['account_name']) ?></td>
                        <td>
                            <?php if ($account['is_active']): ?>
                            <span class="badge bg-success">Aktif</span>
                            <?php else: ?>
                            <span class="badge bg-secondary">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <div class="btn-group">
                                <a href="<?= url('admin/rekening-donasi/edit/' . $account['id']) ?>" 
                                   class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="<?= url('admin/rekening-donasi/toggle/' . $account['id']) ?>" method="POST" class="d-inline">
                                    <?= csrfField() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-<?= $account['is_active'] ? 'warning' : 'success' ?>" 
                                            title="<?= $account['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>">
                                        <i class="bi bi-<?= $account['is_active'] ? 'eye-slash' : 'eye' ?>"></i>
                                    </button>
                                </form>
                                <form action="<?= url('admin/rekening-donasi/delete/' . $account['id']) ?>" method="POST" class="d-inline"
                                      onsubmit="return confirm('Yakin ingin menghapus rekening ini?')">
                                    <?= csrfField() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
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

<div class="alert alert-info mt-4">
    <i class="bi bi-info-circle me-2"></i>
    <strong>Tips:</strong> Atur urutan rekening untuk menentukan tampilan di halaman donasi. Rekening dengan urutan lebih kecil akan ditampilkan lebih dulu.
</div>
