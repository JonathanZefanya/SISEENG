<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">Tambah Kegiatan Baru</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= url('admin/events') ?>">Kegiatan</a></li>
                <li class="breadcrumb-item active">Tambah Baru</li>
            </ol>
        </nav>
    </div>
    <a href="<?= url('admin/events') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form action="<?= url('admin/events/store') ?>" method="POST" enctype="multipart/form-data">
            <?= csrfField() ?>
            
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="mb-4">
                        <label for="title" class="form-label fw-semibold">Nama Kegiatan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg" id="title" name="title" 
                               value="<?= e(old('title')) ?>" required
                               placeholder="Masukkan nama kegiatan">
                    </div>
                    
                    <div class="mb-4">
                        <label for="description" class="form-label fw-semibold">Deskripsi <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="description" name="description" rows="8" required
                                  placeholder="Deskripsikan kegiatan ini..."><?= e(old('description')) ?></textarea>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card bg-light border-0">
                        <div class="card-body">
                            <h5 class="fw-bold mb-4">Detail Kegiatan</h5>
                            
                            <div class="mb-4">
                                <label for="event_date" class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="event_date" name="event_date" 
                                       value="<?= e(old('event_date')) ?>" required>
                            </div>
                            
                            <div class="mb-4">
                                <label for="event_time" class="form-label fw-semibold">Waktu <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" id="event_time" name="event_time" 
                                       value="<?= e(old('event_time')) ?>" required>
                            </div>
                            
                            <div class="mb-4">
                                <label for="location" class="form-label fw-semibold">Lokasi</label>
                                <input type="text" class="form-control" id="location" name="location" 
                                       value="<?= e(old('location')) ?>"
                                       placeholder="Contoh: Gedung Utama Gereja">
                            </div>
                            
                            <div class="mb-4">
                                <label for="image" class="form-label fw-semibold">Gambar/Poster</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                <small class="text-muted">Format: JPG, PNG, GIF. Maks: 2MB</small>
                                <div id="imagePreview" class="mt-2"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="d-flex justify-content-end gap-2">
                <a href="<?= url('admin/events') ?>" class="btn btn-outline-secondary btn-lg">Batal</a>
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-check-lg me-2"></i>Simpan Kegiatan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Image preview
document.getElementById('image').addEventListener('change', function(e) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'img-thumbnail mt-2';
            img.style.maxHeight = '200px';
            preview.appendChild(img);
        };
        reader.readAsDataURL(this.files[0]);
    }
});
</script>
