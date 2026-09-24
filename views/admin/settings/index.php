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
    <ul class="nav nav-tabs mb-4 bg-white rounded-top" id="settingsTabs" role="tablist"
        style="border-bottom: 2px solid #dee2e6;">
        <li class="nav-item" role="presentation">
            <button class="nav-link active px-4 py-3" id="general-tab" data-bs-toggle="tab" data-bs-target="#general"
                type="button" style="color: #333; font-weight: 500;">
                <i class="bi bi-gear me-2 text-primary"></i>Umum
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link px-4 py-3" id="hero-tab" data-bs-toggle="tab" data-bs-target="#hero" type="button"
                style="color: #333; font-weight: 500;">
                <i class="bi bi-image me-2 text-success"></i>Hero Section
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link px-4 py-3" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact"
                type="button" style="color: #333; font-weight: 500;">
                <i class="bi bi-telephone me-2 text-info"></i>Kontak
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link px-4 py-3" id="donation-tab" data-bs-toggle="tab" data-bs-target="#donation"
                type="button" style="color: #333; font-weight: 500;">
                <i class="bi bi-heart me-2 text-danger"></i>Donasi
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link px-4 py-3" id="social-tab" data-bs-toggle="tab" data-bs-target="#social"
                type="button" style="color: #333; font-weight: 500;">
                <i class="bi bi-share me-2 text-warning"></i>Media Sosial
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link px-4 py-3" id="about-tab" data-bs-toggle="tab" data-bs-target="#about" type="button"
                style="color: #333; font-weight: 500;">
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
                            <label for="site_name" class="form-label fw-semibold">Nama Gereja/Website <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg" id="site_name" name="site_name"
                                value="<?= e($settings['site_name'] ?? 'GBI Ciseeng') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="site_tagline" class="form-label fw-semibold">Tagline</label>
                            <input type="text" class="form-control form-control-lg" id="site_tagline"
                                name="site_tagline" value="<?= e($settings['site_tagline'] ?? '') ?>"
                                placeholder="Melayani dengan Kasih">
                        </div>
                        <div class="col-12">
                            <label for="site_description" class="form-label fw-semibold">Deskripsi Website</label>
                            <textarea class="form-control" id="site_description" name="site_description" rows="3"
                                placeholder="Deskripsi singkat tentang gereja"><?= e($settings['site_description'] ?? '') ?></textarea>
                            <small class="text-muted">Digunakan untuk SEO dan meta description</small>
                        </div>

                        <!-- Warna Tema -->
                        <?php
                        $themeColor = themeColor();
                        $themePresets = [
                            '#00aa13' => 'Hijau',
                            '#0a7cff' => 'Biru',
                            '#1e3a8a' => 'Navy',
                            '#00a5a5' => 'Tosca',
                            '#7c3aed' => 'Ungu',
                            '#e11d48' => 'Merah',
                            '#ea580c' => 'Oranye',
                            '#b45309' => 'Cokelat',
                            '#db2777' => 'Pink',
                            '#334155' => 'Abu Gelap',
                        ];
                        ?>
                        <div class="col-12">
                            <hr class="my-2">
                            <h6 class="fw-bold text-primary"><i class="bi bi-palette me-2"></i>Warna Tema</h6>
                            <p class="text-muted small mb-0">Warna utama website & pengerja panel (tombol, header, menu aktif, ikon).</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pilihan Cepat</label>
                            <div class="d-flex flex-wrap gap-2 mb-3" id="themePresets">
                                <?php foreach ($themePresets as $hex => $label): ?>
                                    <button type="button" class="theme-swatch <?= $hex === $themeColor ? 'active' : '' ?>"
                                        data-color="<?= $hex ?>" title="<?= $label ?>" aria-label="<?= $label ?>"
                                        style="background: <?= $hex ?>;"></button>
                                <?php endforeach; ?>
                            </div>

                            <label for="theme_color_hex" class="form-label fw-semibold">Warna Kustom</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" id="theme_color_picker"
                                    value="<?= e($themeColor) ?>" title="Pilih warna">
                                <input type="text" class="form-control" id="theme_color_hex" name="theme_color"
                                    value="<?= e($themeColor) ?>" pattern="#[0-9a-fA-F]{6}" maxlength="7"
                                    placeholder="#00aa13">
                                <button type="button" class="btn btn-light" id="themeReset" title="Kembali ke hijau">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </button>
                            </div>
                            <small class="text-muted d-block mt-1" id="themeContrastHint">
                                Pilih warna yang cukup gelap agar teks putih di atasnya tetap terbaca.
                            </small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pratinjau</label>
                            <div class="theme-preview" id="themePreview">
                                <div class="tp-header">
                                    <i class="bi bi-brightness-high-fill"></i>
                                    <span><?= e($settings['site_name'] ?? 'Nama Gereja') ?></span>
                                </div>
                                <div class="tp-body">
                                    <div class="d-flex gap-2 mb-3">
                                        <span class="tp-icon"><i class="bi bi-calendar-check-fill"></i></span>
                                        <span class="tp-icon tp-soft"><i class="bi bi-person-video3"></i></span>
                                        <span class="tp-chip">Menu aktif</span>
                                    </div>
                                    <span class="tp-btn">Tombol Utama</span>
                                    <span class="tp-link">Tautan</span>
                                </div>
                            </div>
                        </div>

                        <!-- Logo Upload -->
                        <div class="col-12">
                            <hr class="my-2">
                            <h6 class="fw-bold text-primary"><i class="bi bi-image me-2"></i>Logo Website</h6>
                        </div>
                        <div class="col-md-6">
                            <label for="site_logo" class="form-label fw-semibold">Upload Logo</label>
                            <input type="file" class="form-control form-control-lg" id="site_logo" name="site_logo"
                                accept="image/*">
                            <small class="text-muted">Format: JPG, PNG, SVG. Ukuran maksimal: 2MB. Rekomendasi: 200x60
                                px</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Logo Saat Ini</label>
                            <div class="p-3 bg-light rounded text-center">
                                <?php if (!empty($settings['site_logo'])): ?>
                                    <img src="<?= uploads('settings/' . $settings['site_logo']) ?>" alt="Logo"
                                        style="max-height: 60px;">
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
                    <h5 class="mb-0 fw-bold"><i class="bi bi-image me-2 text-primary"></i>Hero Section (Bagian Atas
                        Homepage)</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <!-- Hero Image Upload -->
                        <div class="col-md-6">
                            <label for="hero_image" class="form-label fw-semibold">Upload Gambar Hero</label>
                            <input type="file" class="form-control form-control-lg" id="hero_image" name="hero_image"
                                accept="image/*">
                            <small class="text-muted">Format: JPG, PNG, SVG. Ukuran maksimal: 5MB. Rekomendasi: 600x400
                                px</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Gambar Hero Saat Ini</label>
                            <div class="p-3 bg-light rounded text-center">
                                <?php if (!empty($settings['hero_image'])): ?>
                                    <img src="<?= uploads('settings/' . $settings['hero_image']) ?>" alt="Hero"
                                        style="max-height: 100px;">
                                    <p class="small text-muted mt-2 mb-0"><?= e($settings['hero_image']) ?></p>
                                <?php else: ?>
                                    <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                    <p class="small text-muted mb-0">Menggunakan gambar default</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-12">
                            <hr class="my-2">
                        </div>
                        <div class="col-12">
                            <label for="hero_title" class="form-label fw-semibold">Judul Utama</label>
                            <input type="text" class="form-control form-control-lg" id="hero_title" name="hero_title"
                                value="<?= e($settings['hero_title'] ?? 'Selamat Datang di') ?>"
                                placeholder="Selamat Datang di">
                        </div>
                        <div class="col-12">
                            <label for="hero_subtitle" class="form-label fw-semibold">Sub Judul (Nama Gereja)</label>
                            <input type="text" class="form-control form-control-lg" id="hero_subtitle"
                                name="hero_subtitle" value="<?= e($settings['hero_subtitle'] ?? '') ?>"
                                placeholder="Gereja Bethel Indonesia Ciseeng">
                        </div>
                        <div class="col-md-8">
                            <label for="hero_verse" class="form-label fw-semibold">Ayat Alkitab</label>
                            <textarea class="form-control" id="hero_verse" name="hero_verse" rows="2"
                                placeholder="Karena di mana dua atau tiga orang berkumpul dalam nama-Ku, di situ Aku ada di tengah-tengah mereka."><?= e($settings['hero_verse'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-4">
                            <label for="hero_verse_ref" class="form-label fw-semibold">Referensi Ayat</label>
                            <input type="text" class="form-control form-control-lg" id="hero_verse_ref"
                                name="hero_verse_ref" value="<?= e($settings['hero_verse_ref'] ?? '') ?>"
                                placeholder="Matius 18:20">
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
                        <div class="col-md-6">
                            <label for="site_operational_hours" class="form-label fw-semibold">Jam Operasional</label>
                            <textarea class="form-control" id="site_operational_hours" name="site_operational_hours"
                                rows="3"
                                placeholder="Senin - Jumat: 08:00 - 17:00&#10;Sabtu: 08:00 - 12:00&#10;Minggu: Ibadah"><?= e($settings['site_operational_hours'] ?? '') ?></textarea>
                            <small class="text-muted">Pisahkan dengan baris baru (enter) untuk setiap hari</small>
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
                                    <iframe src="<?= e($settings['site_gmaps_embed']) ?>" style="border:0;"
                                        allowfullscreen="" loading="lazy">
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
                            <input type="text" class="form-control form-control-lg" id="donation_title"
                                name="donation_title"
                                value="<?= e($settings['donation_title'] ?? 'Dukung Pelayanan Kami') ?>">
                        </div>
                        <div class="col-12">
                            <label for="donation_description" class="form-label fw-semibold">Deskripsi Donasi</label>
                            <textarea class="form-control" id="donation_description" name="donation_description"
                                rows="3"><?= e($settings['donation_description'] ?? 'Persembahan dan donasi Anda sangat berarti untuk mendukung pelayanan gereja dan membantu sesama.') ?></textarea>
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
                                    value="<?= e($settings['site_facebook'] ?? '') ?>"
                                    placeholder="https://facebook.com/gereja">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="site_instagram" class="form-label fw-semibold">Instagram</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-instagram"></i></span>
                                <input type="url" class="form-control" id="site_instagram" name="site_instagram"
                                    value="<?= e($settings['site_instagram'] ?? '') ?>"
                                    placeholder="https://instagram.com/gereja">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="site_youtube" class="form-label fw-semibold">YouTube</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-youtube"></i></span>
                                <input type="url" class="form-control" id="site_youtube" name="site_youtube"
                                    value="<?= e($settings['site_youtube'] ?? '') ?>"
                                    placeholder="https://youtube.com/gereja">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="site_tiktok" class="form-label fw-semibold">TikTok</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-tiktok"></i></span>
                                <input type="url" class="form-control" id="site_tiktok" name="site_tiktok"
                                    value="<?= e($settings['site_tiktok'] ?? '') ?>"
                                    placeholder="https://tiktok.com/@gereja">
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
                        <!-- Foto Tentang Kami -->
                        <div class="col-12">
                            <h6 class="fw-bold text-primary"><i class="bi bi-image me-2"></i>Foto Halaman Tentang Kami
                            </h6>
                        </div>
                        <div class="col-md-6">
                            <label for="about_image" class="form-label fw-semibold">Upload Foto Gereja</label>
                            <input type="file" class="form-control form-control-lg" id="about_image" name="about_image"
                                accept="image/*">
                            <small class="text-muted">Format: JPG, PNG, WebP. Maks: 5MB. Rekomendasi: 600x400 px</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Foto Saat Ini</label>
                            <div class="p-3 bg-light rounded text-center">
                                <?php if (!empty($settings['about_image'])): ?>
                                    <img src="<?= uploads('settings/' . $settings['about_image']) ?>" alt="Foto Gereja"
                                        style="max-height: 120px; max-width: 100%; object-fit: cover;">
                                    <p class="small text-muted mt-2 mb-0"><?= e($settings['about_image']) ?></p>
                                <?php else: ?>
                                    <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                    <p class="small text-muted mb-0">Belum ada foto, akan pakai placeholder</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-12">
                            <hr class="my-1">
                        </div>

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
                            <input type="text" class="form-control form-control-lg" id="about_pastor"
                                name="about_pastor" value="<?= e($settings['about_pastor'] ?? '') ?>"
                                placeholder="Pdt. Nama Lengkap">
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
<style>
    .theme-swatch {
        width: 38px; height: 38px;
        border-radius: 50%;
        border: 3px solid #fff;
        box-shadow: 0 0 0 1px var(--line);
        cursor: pointer;
        transition: transform .12s ease;
    }
    .theme-swatch:hover { transform: scale(1.08); }
    .theme-swatch.active { box-shadow: 0 0 0 2px var(--ink); }
    .form-control-color { max-width: 56px; padding: .35rem; }

    .theme-preview { border: 1px solid var(--line); border-radius: 16px; overflow: hidden; background: var(--canvas); }
    .theme-preview .tp-header { background: var(--brand); color: #fff; padding: .9rem 1rem; font-weight: 800; display: flex; gap: .5rem; align-items: center; }
    .theme-preview .tp-body { padding: 1rem; }
    .theme-preview .tp-icon { width: 42px; height: 42px; border-radius: 13px; background: var(--brand); color: #fff; display: grid; place-items: center; font-size: 1.1rem; }
    .theme-preview .tp-icon.tp-soft { background: var(--brand-soft); color: var(--brand-darker); }
    .theme-preview .tp-chip { align-self: center; background: var(--brand-soft); color: var(--brand-darker); font-weight: 800; font-size: .8rem; padding: .35rem .8rem; border-radius: 999px; }
    .theme-preview .tp-btn { display: inline-block; background: var(--brand); color: #fff; font-weight: 700; font-size: .85rem; padding: .5rem 1.1rem; border-radius: 999px; margin-right: .75rem; }
    .theme-preview .tp-link { color: var(--brand-dark); font-weight: 700; font-size: .85rem; }
</style>

<script>
    (function () {
        const DEFAULT = '#00aa13';
        const picker = document.getElementById('theme_color_picker');
        const hexInput = document.getElementById('theme_color_hex');
        const hint = document.getElementById('themeContrastHint');
        const swatches = document.querySelectorAll('.theme-swatch');

        // Sama dengan mixColor() di core/Helpers.php
        const toRgb = hex => [1, 3, 5].map(i => parseInt(hex.substr(i, 2), 16));
        const mix = (rgb, w, a) => rgb.map((c, i) => Math.round(c * (1 - a) + w[i] * a));
        const toHex = rgb => '#' + rgb.map(c => c.toString(16).padStart(2, '0')).join('');

        // Terapkan langsung ke seluruh halaman admin sebagai pratinjau
        function apply(hex) {
            if (!/^#[0-9a-f]{6}$/i.test(hex)) return;
            hex = hex.toLowerCase();
            const rgb = toRgb(hex);
            const dark = mix(rgb, [0, 0, 0], .18);
            const root = document.documentElement.style;
            root.setProperty('--brand', hex);
            root.setProperty('--brand-rgb', rgb.join(', '));
            root.setProperty('--brand-dark', toHex(dark));
            root.setProperty('--brand-dark-rgb', dark.join(', '));
            root.setProperty('--brand-darker', toHex(mix(rgb, [0, 0, 0], .35)));
            root.setProperty('--brand-light', toHex(mix(rgb, [255, 255, 255], .25)));
            root.setProperty('--brand-soft', toHex(mix(rgb, [255, 255, 255], .88)));

            picker.value = hex;
            swatches.forEach(s => s.classList.toggle('active', s.dataset.color === hex));

            // Peringatan kontras: luminans relatif (WCAG) terhadap teks putih
            const lum = rgb.map(c => { c /= 255; return c <= .03928 ? c / 12.92 : Math.pow((c + .055) / 1.055, 2.4); });
            const L = .2126 * lum[0] + .7152 * lum[1] + .0722 * lum[2];
            const contrast = 1.05 / (L + .05);
            hint.classList.toggle('text-danger', contrast < 3);
            hint.classList.toggle('text-muted', contrast >= 3);
            hint.textContent = contrast < 3
                ? '⚠ Warna ini terlalu terang, teks putih di atasnya akan sulit dibaca.'
                : 'Pilih warna yang cukup gelap agar teks putih di atasnya tetap terbaca.';
        }

        picker.addEventListener('input', () => { hexInput.value = picker.value; apply(picker.value); });
        hexInput.addEventListener('input', () => apply(hexInput.value.trim()));
        swatches.forEach(s => s.addEventListener('click', () => { hexInput.value = s.dataset.color; apply(s.dataset.color); }));
        document.getElementById('themeReset').addEventListener('click', () => { hexInput.value = DEFAULT; apply(DEFAULT); });

        apply(hexInput.value);
    })();
</script>
