<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 fw-bold">Pengaturan Website</h1>
        <p class="text-muted mb-0">Kelola informasi gereja, kontak, dan donasi</p>
    </div>
</div>

<form action="<?= url('admin/pengaturan/update') ?>" method="POST" enctype="multipart/form-data">
    <?= csrfField() ?>
    
    <!-- Nav Tabs -->
    <ul class="nav nav-tabs mb-4 bg-white rounded-top" id="settingsTabs" role="tablist" style="border-bottom: 2px solid #dee2e6;">
        <li class="nav-item" role="presentation">
            <button class="nav-link active px-4 py-3" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" style="color: #333; font-weight: 500;">
                <i class="bi bi-gear me-2 text-primary"></i>Umum
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link px-4 py-3" id="hero-tab" data-bs-toggle="tab" data-bs-target="#hero" type="button" style="color: #333; font-weight: 500;">
                <i class="bi bi-image me-2 text-success"></i>Hero Section
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link px-4 py-3" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" style="color: #333; font-weight: 500;">
                <i class="bi bi-telephone me-2 text-info"></i>Kontak
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link px-4 py-3" id="donation-tab" data-bs-toggle="tab" data-bs-target="#donation" type="button" style="color: #333; font-weight: 500;">
                <i class="bi bi-heart me-2 text-danger"></i>Donasi
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link px-4 py-3" id="social-tab" data-bs-toggle="tab" data-bs-target="#social" type="button" style="color: #333; font-weight: 500;">
                <i class="bi bi-share me-2 text-warning"></i>Media Sosial
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link px-4 py-3" id="about-tab" data-bs-toggle="tab" data-bs-target="#about" type="button" style="color: #333; font-weight: 500;">
                <i class="bi bi-info-circle me-2 text-secondary"></i>Tentang
            </button>
        </li>
    </ul>
    
    <!-- Tab Content -->
    <div class="tab-content" id="settingsTabContent">
        
        <!-- Tab: Umum -->
        <div class="tab-pane fade show active" id="general" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-gear me-2 text-primary"></i>Pengaturan Umum</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="site_name" class="form-label fw-semibold">Nama Gereja/Website <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg" id="site_name" name="site_name" 
                                   value="<?= e($settings['site_name'] ?? 'GBI Ciseeng') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="site_tagline" class="form-label fw-semibold">Tagline</label>
                            <input type="text" class="form-control form-control-lg" id="site_tagline" name="site_tagline" 
                                   value="<?= e($settings['site_tagline'] ?? '') ?>" placeholder="Melayani dengan Kasih">
                        </div>
                        <div class="col-12">
                            <label for="site_description" class="form-label fw-semibold">Deskripsi Website</label>
                            <textarea class="form-control" id="site_description" name="site_description" rows="3" 
                                      placeholder="Deskripsi singkat tentang gereja"><?= e($settings['site_description'] ?? '') ?></textarea>
                            <small class="text-muted">Digunakan untuk SEO dan meta description</small>
                        </div>
                        
                        <!-- Logo Upload -->
                        <div class="col-12">
                            <hr class="my-2">
                            <h6 class="fw-bold text-primary"><i class="bi bi-image me-2"></i>Logo Website</h6>
                        </div>
                        <div class="col-md-6">
                            <label for="site_logo" class="form-label fw-semibold">Upload Logo</label>
                            <input type="file" class="form-control form-control-lg" id="site_logo" name="site_logo" accept="image/*">
                            <small class="text-muted">Format: JPG, PNG, SVG. Ukuran maksimal: 2MB. Rekomendasi: 200x60 px</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Logo Saat Ini</label>
                            <div class="p-3 bg-light rounded text-center">
                                <?php if (!empty($settings['site_logo'])): ?>
                                <img src="<?= uploads('settings/' . $settings['site_logo']) ?>" alt="Logo" style="max-height: 60px;">
                                <p class="small text-muted mt-2 mb-0"><?= e($settings['site_logo']) ?></p>
                                <?php else: ?>
                                <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                <p class="small text-muted mb-0">Belum ada logo</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tab: Hero Section -->
        <div class="tab-pane fade" id="hero" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-image me-2 text-primary"></i>Hero Section (Bagian Atas Homepage)</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <!-- Hero Image Upload -->
                        <div class="col-md-6">
                            <label for="hero_image" class="form-label fw-semibold">Upload Gambar Hero</label>
                            <input type="file" class="form-control form-control-lg" id="hero_image" name="hero_image" accept="image/*">
                            <small class="text-muted">Format: JPG, PNG, SVG. Ukuran maksimal: 5MB. Rekomendasi: 600x400 px</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Gambar Hero Saat Ini</label>
                            <div class="p-3 bg-light rounded text-center">
                                <?php if (!empty($settings['hero_image'])): ?>
                                <img src="<?= uploads('settings/' . $settings['hero_image']) ?>" alt="Hero" style="max-height: 100px;">
                                <p class="small text-muted mt-2 mb-0"><?= e($settings['hero_image']) ?></p>
                                <?php else: ?>
                                <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                <p class="small text-muted mb-0">Menggunakan gambar default</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-12"><hr class="my-2"></div>
                        <div class="col-12">
                            <label for="hero_title" class="form-label fw-semibold">Judul Utama</label>
                            <input type="text" class="form-control form-control-lg" id="hero_title" name="hero_title" 
                                   value="<?= e($settings['hero_title'] ?? 'Selamat Datang di') ?>" placeholder="Selamat Datang di">
                        </div>
                        <div class="col-12">
                            <label for="hero_subtitle" class="form-label fw-semibold">Sub Judul (Nama Gereja)</label>
                            <input type="text" class="form-control form-control-lg" id="hero_subtitle" name="hero_subtitle" 
                                   value="<?= e($settings['hero_subtitle'] ?? '') ?>" placeholder="Gereja Bethel Indonesia Ciseeng">
                        </div>
                        <div class="col-md-8">
                            <label for="hero_verse" class="form-label fw-semibold">Ayat Alkitab</label>
                            <textarea class="form-control" id="hero_verse" name="hero_verse" rows="2" 
                                      placeholder="Karena di mana dua atau tiga orang berkumpul dalam nama-Ku, di situ Aku ada di tengah-tengah mereka."><?= e($settings['hero_verse'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-4">
                            <label for="hero_verse_ref" class="form-label fw-semibold">Referensi Ayat</label>
                            <input type="text" class="form-control form-control-lg" id="hero_verse_ref" name="hero_verse_ref" 
                                   value="<?= e($settings['hero_verse_ref'] ?? '') ?>" placeholder="Matius 18:20">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tab: Kontak -->
        <div class="tab-pane fade" id="contact" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-telephone me-2 text-primary"></i>Informasi Kontak</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="site_email" class="form-label fw-semibold">Email</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" id="site_email" name="site_email" 
                                       value="<?= e($settings['site_email'] ?? '') ?>" placeholder="info@gereja.com">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="site_phone" class="form-label fw-semibold">Nomor Telepon</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                <input type="text" class="form-control" id="site_phone" name="site_phone" 
                                       value="<?= e($settings['site_phone'] ?? '') ?>" placeholder="(021) 1234-5678">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="site_whatsapp" class="form-label fw-semibold">Nomor WhatsApp</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-whatsapp"></i></span>
                                <input type="text" class="form-control" id="site_whatsapp" name="site_whatsapp" 
                                       value="<?= e($settings['site_whatsapp'] ?? '') ?>" placeholder="08123456789">
                            </div>
                            <small class="text-muted">Format: 08xxx atau 628xxx (tanpa +)</small>
                        </div>
                        <div class="col-12">
                            <label for="site_address" class="form-label fw-semibold">Alamat Lengkap</label>
                            <textarea class="form-control" id="site_address" name="site_address" rows="3" 
                                      placeholder="Jl. Gereja No. 123, Kelurahan, Kecamatan, Kota, Kode Pos"><?= e($settings['site_address'] ?? '') ?></textarea>
                        </div>
                        
                        <!-- Google Maps Section -->
                        <div class="col-12">
                            <hr class="my-2">
                            <h6 class="fw-bold text-primary"><i class="bi bi-geo-alt me-2"></i>Lokasi Google Maps</h6>
                        </div>
                        <div class="col-12">
                            <label for="site_gmaps_embed" class="form-label fw-semibold">Google Maps Embed URL</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-map"></i></span>
                                <input type="url" class="form-control" id="site_gmaps_embed" name="site_gmaps_embed" 
                                       value="<?= e($settings['site_gmaps_embed'] ?? '') ?>" 
                                       placeholder="https://www.google.com/maps/embed?pb=...">
                            </div>
                            <small class="text-muted">
                                <strong>Cara mendapatkan URL:</strong><br>
                                1. Buka <a href="https://www.google.com/maps" target="_blank">Google Maps</a><br>
                                2. Cari lokasi gereja Anda<br>
                                3. Klik tombol "Bagikan" → pilih "Sematkan peta"<br>
                                4. Salin URL dari atribut <code>src="..."</code> pada kode iframe
                            </small>
                        </div>
                        
                        <!-- Preview Maps -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Preview Lokasi</label>
                            <div class="ratio ratio-16x9 rounded shadow-sm overflow-hidden border" id="maps-preview">
                                <?php if (!empty($settings['site_gmaps_embed'])): ?>
                                <iframe 
                                    src="<?= e($settings['site_gmaps_embed']) ?>" 
                                    style="border:0;" 
                                    allowfullscreen="" 
                                    loading="lazy">
                                </iframe>
                                <?php else: ?>
                                <div class="d-flex align-items-center justify-content-center bg-light">
                                    <div class="text-center text-muted">
                                        <i class="bi bi-geo-alt" style="font-size: 3rem;"></i>
                                        <p class="mb-0 mt-2">Belum ada lokasi maps</p>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tab: Donasi -->
        <div class="tab-pane fade" id="donation" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-heart me-2 text-primary"></i>Informasi Donasi</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-12">
                            <label for="donation_title" class="form-label fw-semibold">Judul Halaman Donasi</label>
                            <input type="text" class="form-control form-control-lg" id="donation_title" name="donation_title" 
                                   value="<?= e($settings['donation_title'] ?? 'Dukung Pelayanan Kami') ?>">
                        </div>
                        <div class="col-12">
                            <label for="donation_description" class="form-label fw-semibold">Deskripsi Donasi</label>
                            <textarea class="form-control" id="donation_description" name="donation_description" rows="3"><?= e($settings['donation_description'] ?? 'Persembahan dan donasi Anda sangat berarti untuk mendukung pelayanan gereja dan membantu sesama.') ?></textarea>
                        </div>
                        
                        <div class="col-12">
                            <div class="alert alert-info mb-0">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Rekening Bank & QRIS</strong> dapat dikelola melalui menu 
                                <a href="<?= url('admin/rekening-donasi') ?>" class="alert-link">
                                    <i class="bi bi-credit-card me-1"></i>Rekening Donasi
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tab: Media Sosial -->
        <div class="tab-pane fade" id="social" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-share me-2 text-primary"></i>Media Sosial</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="site_facebook" class="form-label fw-semibold">Facebook</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-facebook"></i></span>
                                <input type="url" class="form-control" id="site_facebook" name="site_facebook" 
                                       value="<?= e($settings['site_facebook'] ?? '') ?>" placeholder="https://facebook.com/gereja">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="site_instagram" class="form-label fw-semibold">Instagram</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-instagram"></i></span>
                                <input type="url" class="form-control" id="site_instagram" name="site_instagram" 
                                       value="<?= e($settings['site_instagram'] ?? '') ?>" placeholder="https://instagram.com/gereja">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="site_youtube" class="form-label fw-semibold">YouTube</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-youtube"></i></span>
                                <input type="url" class="form-control" id="site_youtube" name="site_youtube" 
                                       value="<?= e($settings['site_youtube'] ?? '') ?>" placeholder="https://youtube.com/gereja">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="site_tiktok" class="form-label fw-semibold">TikTok</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-tiktok"></i></span>
                                <input type="url" class="form-control" id="site_tiktok" name="site_tiktok" 
                                       value="<?= e($settings['site_tiktok'] ?? '') ?>" placeholder="https://tiktok.com/@gereja">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tab: Tentang -->
        <div class="tab-pane fade" id="about" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-info-circle me-2 text-primary"></i>Tentang Gereja</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-12">
                            <label for="about_vision" class="form-label fw-semibold">Visi Gereja</label>
                            <textarea class="form-control" id="about_vision" name="about_vision" rows="3" 
                                      placeholder="Visi gereja..."><?= e($settings['about_vision'] ?? '') ?></textarea>
                        </div>
                        <div class="col-12">
                            <label for="about_mission" class="form-label fw-semibold">Misi Gereja</label>
                            <textarea class="form-control" id="about_mission" name="about_mission" rows="4" 
                                      placeholder="Misi gereja (pisahkan dengan enter untuk setiap poin)"><?= e($settings['about_mission'] ?? '') ?></textarea>
                            <small class="text-muted">Pisahkan setiap misi dengan baris baru (enter)</small>
                        </div>
                        <div class="col-12">
                            <label for="about_history" class="form-label fw-semibold">Sejarah Singkat</label>
                            <textarea class="form-control" id="about_history" name="about_history" rows="5" 
                                      placeholder="Sejarah singkat gereja..."><?= e($settings['about_history'] ?? '') ?></textarea>
                        </div>
                        <div class="col-12">
                            <label for="about_pastor" class="form-label fw-semibold">Nama Gembala/Pendeta</label>
                            <input type="text" class="form-control form-control-lg" id="about_pastor" name="about_pastor" 
                                   value="<?= e($settings['about_pastor'] ?? '') ?>" placeholder="Pdt. Nama Lengkap">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Submit Button -->
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary btn-lg px-5">
                    <i class="bi bi-check-lg me-2"></i>Simpan Semua Pengaturan
                </button>
            </div>
        </div>
    </div>
</form>
