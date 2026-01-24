<?php
/**
 * =========================================================
 * Form Kategori Artikel
 * =========================================================
 */

$category = $category ?? null;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">
            <i class="bi bi-<?= $isEdit ? 'pencil' : 'plus-circle' ?> me-2 text-primary"></i>
            <?= $isEdit ? 'Edit Kategori' : 'Tambah Kategori' ?>
        </h1>
        <p class="text-muted mb-0">
            <?= $isEdit ? 'Perbarui informasi kategori' : 'Buat kategori artikel baru' ?>
        </p>
    </div>
    <a href="<?= url('admin/kategori-artikel') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<?php if ($flash = getFlash('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i><?= $flash ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <form action="<?= url($isEdit ? 'admin/kategori-artikel/update/' . $category['id'] : 'admin/kategori-artikel/store') ?>" method="POST">
            <?= csrfField() ?>
            
            <div class="row">
                <div class="col-md-8">
                    <!-- Nama Kategori -->
                    <div class="mb-3">
                        <label for="name" class="form-label fw-medium">
                            Nama Kategori <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="name" 
                               name="name" 
                               value="<?= e($category['name'] ?? '') ?>" 
                               placeholder="Contoh: Renungan Harian"
                               required>
                        <div class="form-text">Nama kategori yang akan ditampilkan</div>
                    </div>
                    
                    <!-- Deskripsi -->
                    <div class="mb-3">
                        <label for="description" class="form-label fw-medium">Deskripsi</label>
                        <textarea class="form-control" 
                                  id="description" 
                                  name="description" 
                                  rows="3" 
                                  placeholder="Deskripsi singkat tentang kategori ini"><?= e($category['description'] ?? '') ?></textarea>
                        <div class="form-text">Opsional - deskripsi untuk menjelaskan kategori</div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <!-- Warna -->
                    <div class="mb-3">
                        <label for="color" class="form-label fw-medium">Warna Badge</label>
                        <div class="input-group">
                            <input type="color" 
                                   class="form-control form-control-color" 
                                   id="colorPicker" 
                                   value="<?= e($category['color'] ?? '#6c757d') ?>" 
                                   style="width: 50px;">
                            <input type="text" 
                                   class="form-control" 
                                   id="color" 
                                   name="color" 
                                   value="<?= e($category['color'] ?? '#6c757d') ?>" 
                                   placeholder="#6c757d"
                                   pattern="^#[0-9A-Fa-f]{6}$">
                        </div>
                        <div class="form-text">Warna untuk badge kategori</div>
                    </div>
                    
                    <!-- Preview -->
                    <div class="mb-3">
                        <label class="form-label fw-medium">Preview Badge</label>
                        <div class="p-3 bg-light rounded">
                            <span class="badge" id="previewBadge" style="background-color: <?= e($category['color'] ?? '#6c757d') ?>">
                                <?= e($category['name'] ?? 'Nama Kategori') ?>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Status -->
                    <div class="mb-3">
                        <label class="form-label fw-medium">Status</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   role="switch" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1"
                                   <?= ($category['is_active'] ?? 1) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_active">Aktif</label>
                        </div>
                        <div class="form-text">Kategori aktif akan ditampilkan di form artikel</div>
                    </div>
                    
                    <!-- Preset Warna -->
                    <div class="mb-3">
                        <label class="form-label fw-medium">Warna Cepat</label>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-sm color-preset" data-color="#0d6efd" style="background-color: #0d6efd; width: 32px; height: 32px; border-radius: 4px; border: 2px solid transparent;"></button>
                            <button type="button" class="btn btn-sm color-preset" data-color="#198754" style="background-color: #198754; width: 32px; height: 32px; border-radius: 4px; border: 2px solid transparent;"></button>
                            <button type="button" class="btn btn-sm color-preset" data-color="#dc3545" style="background-color: #dc3545; width: 32px; height: 32px; border-radius: 4px; border: 2px solid transparent;"></button>
                            <button type="button" class="btn btn-sm color-preset" data-color="#ffc107" style="background-color: #ffc107; width: 32px; height: 32px; border-radius: 4px; border: 2px solid transparent;"></button>
                            <button type="button" class="btn btn-sm color-preset" data-color="#0dcaf0" style="background-color: #0dcaf0; width: 32px; height: 32px; border-radius: 4px; border: 2px solid transparent;"></button>
                            <button type="button" class="btn btn-sm color-preset" data-color="#6f42c1" style="background-color: #6f42c1; width: 32px; height: 32px; border-radius: 4px; border: 2px solid transparent;"></button>
                            <button type="button" class="btn btn-sm color-preset" data-color="#fd7e14" style="background-color: #fd7e14; width: 32px; height: 32px; border-radius: 4px; border: 2px solid transparent;"></button>
                            <button type="button" class="btn btn-sm color-preset" data-color="#20c997" style="background-color: #20c997; width: 32px; height: 32px; border-radius: 4px; border: 2px solid transparent;"></button>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="d-flex justify-content-end gap-2">
                <a href="<?= url('admin/kategori-artikel') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle me-1"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i> <?= $isEdit ? 'Perbarui' : 'Simpan' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const colorPicker = document.getElementById('colorPicker');
    const colorInput = document.getElementById('color');
    const nameInput = document.getElementById('name');
    const previewBadge = document.getElementById('previewBadge');
    const colorPresets = document.querySelectorAll('.color-preset');
    
    // Sync color picker with text input
    colorPicker.addEventListener('input', function() {
        colorInput.value = this.value;
        updatePreview();
    });
    
    colorInput.addEventListener('input', function() {
        if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
            colorPicker.value = this.value;
            updatePreview();
        }
    });
    
    // Update preview when name changes
    nameInput.addEventListener('input', function() {
        previewBadge.textContent = this.value || 'Nama Kategori';
    });
    
    // Color presets
    colorPresets.forEach(btn => {
        btn.addEventListener('click', function() {
            const color = this.dataset.color;
            colorPicker.value = color;
            colorInput.value = color;
            updatePreview();
            
            // Highlight selected
            colorPresets.forEach(b => b.style.border = '2px solid transparent');
            this.style.border = '2px solid #333';
        });
    });
    
    function updatePreview() {
        previewBadge.style.backgroundColor = colorInput.value;
    }
});
</script>
