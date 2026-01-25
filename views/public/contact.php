<section class="py-5 bg-primary text-white">
    <div class="container text-center">
        <h1 class="display-4 fw-bold mb-3">Hubungi Kami</h1>
        <p class="lead fs-4">Kami siap mendengar dan melayani Anda</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <!-- Contact Form -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <h3 class="fw-bold mb-4">Kirim Pesan</h3>
                        
                        <form action="<?= url('kontak/kirim') ?>" method="POST">
                            <?= csrfField() ?>
                            
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-semibold fs-5">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg" id="name" name="name" 
                                           value="<?= e(old('name')) ?>" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-semibold fs-5">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control form-control-lg" id="email" name="email" 
                                           value="<?= e(old('email')) ?>" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="phone" class="form-label fw-semibold fs-5">No. Telepon</label>
                                    <input type="tel" class="form-control form-control-lg" id="phone" name="phone" 
                                           value="<?= e(old('phone')) ?>">
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="subject" class="form-label fw-semibold fs-5">Subjek</label>
                                    <input type="text" class="form-control form-control-lg" id="subject" name="subject" 
                                           value="<?= e(old('subject')) ?>">
                                </div>
                                
                                <div class="col-12">
                                    <label for="message" class="form-label fw-semibold fs-5">Pesan <span class="text-danger">*</span></label>
                                    <textarea class="form-control form-control-lg" id="message" name="message" rows="5" required><?= e(old('message')) ?></textarea>
                                </div>
                                
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-lg px-5 py-3 fs-5">
                                        <i class="bi bi-send me-2"></i>Kirim Pesan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Contact Info -->
            <div class="col-lg-5">
                <h3 class="fw-bold mb-4">Informasi Kontak</h3>
                
                <div class="d-flex mb-4">
                    <div class="flex-shrink-0">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="bi bi-geo-alt fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-4">
                        <h5 class="fw-bold">Alamat</h5>
                        <p class="text-muted fs-5 mb-0">
                            Jl. Gereja No. 123<br>
                            Kelurahan Contoh, Kecamatan Contoh<br>
                            Jakarta 12345
                        </p>
                    </div>
                </div>
                
                <div class="d-flex mb-4">
                    <div class="flex-shrink-0">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="bi bi-telephone fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-4">
                        <h5 class="fw-bold">Telepon</h5>
                        <p class="text-muted fs-5 mb-0">(021) 1234-5678</p>
                    </div>
                </div>
                
                <div class="d-flex mb-4">
                    <div class="flex-shrink-0">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="bi bi-envelope fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-4">
                        <h5 class="fw-bold">Email</h5>
                        <p class="text-muted fs-5 mb-0">info@gereja.com</p>
                    </div>
                </div>
                
                <div class="d-flex mb-4">
                    <div class="flex-shrink-0">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="bi bi-clock fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-4">
                        <h5 class="fw-bold">Jam Operasional</h5>
                        <p class="text-muted fs-5 mb-0">
                            Senin - Jumat: 08:00 - 17:00<br>
                            Sabtu: 08:00 - 12:00<br>
                            Minggu: Ibadah
                        </p>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <h5 class="fw-bold mb-3">Ikuti Kami</h5>
                <div class="d-flex gap-3">
                    <a href="#" class="btn btn-outline-primary btn-lg rounded-circle">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="#" class="btn btn-outline-danger btn-lg rounded-circle">
                        <i class="bi bi-instagram"></i>
                    </a>
                    <a href="#" class="btn btn-outline-danger btn-lg rounded-circle">
                        <i class="bi bi-youtube"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="py-5 bg-light">
    <div class="container">
        <h3 class="fw-bold text-center mb-4">Lokasi Kami</h3>
        <div class="ratio ratio-21x9 rounded shadow overflow-hidden">
            <?php 
            $gmapsUrl = setting('site_gmaps_embed');
            if (!empty($gmapsUrl)): 
            ?>
            <iframe 
                src="<?= e($gmapsUrl) ?>" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy">
            </iframe>
            <?php else: ?>
            <div class="d-flex align-items-center justify-content-center bg-light" style="min-height: 300px;">
                <div class="text-center text-muted">
                    <i class="bi bi-geo-alt" style="font-size: 4rem;"></i>
                    <p class="mb-0 mt-2 fs-5">Lokasi belum tersedia</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
