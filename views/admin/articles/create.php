<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">Tambah Artikel Baru</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= url('admin/articles') ?>">Artikel</a></li>
                <li class="breadcrumb-item active">Tambah Baru</li>
            </ol>
        </nav>
    </div>
    <a href="<?= url('admin/articles') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form action="<?= url('admin/articles/store') ?>" method="POST" enctype="multipart/form-data">
            <?= csrfField() ?>
            
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="mb-4">
                        <label for="title" class="form-label fw-semibold">Judul Artikel <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg" id="title" name="title" 
                               value="<?= e(old('title')) ?>" required
                               placeholder="Masukkan judul artikel">
                    </div>
                    
                    <div class="mb-4">
                        <label for="slug" class="form-label fw-semibold">Slug (URL)</label>
                        <div class="input-group">
                            <span class="input-group-text"><?= url('artikel/') ?></span>
                            <input type="text" class="form-control" id="slug" name="slug" 
                                   value="<?= e(old('slug')) ?>"
                                   placeholder="judul-artikel-anda">
                        </div>
                        <small class="text-muted">Biarkan kosong untuk generate otomatis dari judul</small>
                    </div>
                    
                    <div class="mb-4">
                        <label for="content" class="form-label fw-semibold">Konten Artikel <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="content" name="content" rows="15" required
                                  placeholder="Tulis konten artikel di sini..."><?= e(old('content')) ?></textarea>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card bg-light border-0">
                        <div class="card-body">
                            <h5 class="fw-bold mb-4">Pengaturan</h5>
                            
                            <div class="mb-4">
                                <label for="status" class="form-label fw-semibold">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="draft" <?= old('status') === 'draft' ? 'selected' : '' ?>>Draft</option>
                                    <option value="published" <?= old('status') === 'published' ? 'selected' : '' ?>>Publikasikan</option>
                                </select>
                            </div>
                            
                            <div class="mb-4">
                                <label for="category_id" class="form-label fw-semibold">Kategori</label>
                                <select class="form-select" id="category_id" name="category_id">
                                    <option value="">-- Pilih Kategori --</option>
                                    <?php foreach ($categories ?? [] as $category): ?>
                                    <option value="<?= $category['id'] ?>" <?= old('category_id') == $category['id'] ? 'selected' : '' ?>
                                            style="border-left: 4px solid <?= e($category['color']) ?>">
                                        <?= e($category['name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="d-flex justify-content-end mt-1">
                                    <a href="<?= url('admin/kategori-artikel/create') ?>" class="small text-primary">
                                        <i class="bi bi-plus-circle me-1"></i>Tambah Kategori
                                    </a>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="image" class="form-label fw-semibold">Gambar Utama</label>
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
                <a href="<?= url('admin/articles') ?>" class="btn btn-outline-secondary btn-lg">Batal</a>
                <button type="submit" name="action" value="draft" class="btn btn-secondary btn-lg">
                    <i class="bi bi-file-earmark me-2"></i>Simpan Draft
                </button>
                <button type="submit" name="action" value="publish" class="btn btn-primary btn-lg">
                    <i class="bi bi-send me-2"></i>Publikasikan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Auto generate slug from title
document.getElementById('title').addEventListener('input', function() {
    const slug = this.value
        .toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim();
    document.getElementById('slug').value = slug;
});

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
