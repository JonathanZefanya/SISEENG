<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 fw-bold">Tambah Jadwal Ibadah</h1>
        <p class="text-muted mb-0">Buat jadwal ibadah baru</p>
    </div>
    <a href="<?= url('admin/jadwal') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>
</div>

<!-- Form -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form action="<?= url('admin/jadwal/simpan') ?>" method="POST">
            <?= csrfField() ?>
            
            <div class="row g-4">
                <div class="col-md-6">
                    <label for="title" class="form-label fw-semibold">Nama Ibadah <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-lg" id="title" name="title" 
                           value="<?= e(old('title')) ?>" placeholder="Contoh: Ibadah Raya" required>
                </div>
                
                <div class="col-md-6">
                    <label for="day" class="form-label fw-semibold">Hari <span class="text-danger">*</span></label>
                    <select class="form-select form-select-lg" id="day" name="day" required>
                        <option value="">Pilih Hari</option>
                        <?php 
                        $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                        foreach ($days as $day): 
                        ?>
                        <option value="<?= $day ?>" <?= old('day') === $day ? 'selected' : '' ?>><?= $day ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label for="time_start" class="form-label fw-semibold">Waktu Mulai <span class="text-danger">*</span></label>
                    <input type="time" class="form-control form-control-lg" id="time_start" name="time_start" 
                           value="<?= e(old('time_start')) ?>" required>
                </div>
                
                <div class="col-md-6">
                    <label for="time_end" class="form-label fw-semibold">Waktu Selesai</label>
                    <input type="time" class="form-control form-control-lg" id="time_end" name="time_end" 
                           value="<?= e(old('time_end')) ?>">
                </div>
                
                <div class="col-12">
                    <label for="location" class="form-label fw-semibold">Lokasi</label>
                    <input type="text" class="form-control form-control-lg" id="location" name="location" 
                           value="<?= e(old('location')) ?>" placeholder="Contoh: Gedung Utama">
                </div>
                
                <div class="col-12">
                    <label for="description" class="form-label fw-semibold">Keterangan</label>
                    <textarea class="form-control" id="description" name="description" rows="3"
                              placeholder="Keterangan tambahan (opsional)"><?= e(old('description')) ?></textarea>
                </div>
                
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                               <?= old('is_active') !== '0' ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="is_active">Aktifkan jadwal ini</label>
                    </div>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="d-flex justify-content-end gap-2">
                <a href="<?= url('admin/jadwal') ?>" class="btn btn-outline-secondary btn-lg">Batal</a>
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-check-lg me-2"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>
