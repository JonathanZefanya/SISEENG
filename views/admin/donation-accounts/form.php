<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 fw-bold"><?= $isEdit ? 'Edit' : 'Tambah' ?> Rekening Donasi</h1>
        <p class="text-muted mb-0"><?= $isEdit ? 'Ubah informasi rekening bank' : 'Tambahkan rekening bank baru untuk donasi' ?></p>
    </div>
    <a href="<?= url('admin/rekening-donasi') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-bank me-2 text-primary"></i>Informasi Rekening
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= url('admin/rekening-donasi/' . ($isEdit ? 'update/' . $account['id'] : 'store')) ?>" 
                      method="POST" enctype="multipart/form-data">
                    <?= csrfField() ?>
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="bank_name" class="form-label fw-semibold">
                                Nama Bank <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-lg" id="bank_name" name="bank_name" 
                                   value="<?= e($account['bank_name'] ?? '') ?>" placeholder="Contoh: Bank BCA" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="account_number" class="form-label fw-semibold">
                                Nomor Rekening <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-lg" id="account_number" name="account_number" 
                                   value="<?= e($account['account_number'] ?? '') ?>" placeholder="1234567890" required>
                        </div>
                        
                        <div class="col-12">
                            <label for="account_name" class="form-label fw-semibold">
                                Nama Pemilik Rekening <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-lg" id="account_name" name="account_name" 
                                   value="<?= e($account['account_name'] ?? '') ?>" placeholder="GBI Ciseeng" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="bank_logo" class="form-label fw-semibold">Logo Bank (Opsional)</label>
                            <input type="file" class="form-control form-control-lg" id="bank_logo" name="bank_logo" accept="image/*">
                            <small class="text-muted">Format: JPG, PNG, GIF, WebP. Maksimal 2MB.</small>
                            
                            <?php if (!empty($account['bank_logo'])): ?>
                            <div class="mt-2 p-2 bg-light rounded d-inline-block">
                                <img src="<?= uploads('donations/' . $account['bank_logo']) ?>" alt="Logo" style="max-height: 50px;">
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="sort_order" class="form-label fw-semibold">Urutan</label>
                            <input type="number" class="form-control form-control-lg" id="sort_order" name="sort_order" 
                                   value="<?= e($account['sort_order'] ?? 0) ?>" min="0">
                            <small class="text-muted">Semakin kecil, ditampilkan lebih dulu</small>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Status</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                       <?= (!$isEdit || ($account['is_active'] ?? 1)) ? 'checked' : '' ?> style="width: 3em; height: 1.5em;">
                                <label class="form-check-label ms-2" for="is_active">Aktif</label>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= url('admin/rekening-donasi') ?>" class="btn btn-outline-secondary btn-lg px-4">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg px-4">
                            <i class="bi bi-check-lg me-2"></i><?= $isEdit ? 'Simpan Perubahan' : 'Tambah Rekening' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
