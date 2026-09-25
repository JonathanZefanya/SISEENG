<?php
// Ambil data settings dari database
$siteAddress = setting('site_address');
$sitePhone = setting('site_phone');
$siteEmail = setting('site_email');
$siteWhatsapp = setting('site_whatsapp');
$siteFacebook = setting('site_facebook');
$siteInstagram = setting('site_instagram');
$siteYoutube = setting('site_youtube');
$siteTiktok = setting('site_tiktok');
$siteOperationalHours = setting('site_operational_hours');
?>

<section class="py-5 bg-primary text-white">
    <div class="container text-center">
        <h1 class="display-4 fw-bold mb-3">Hubungi Kami</h1>
        <p class="lead fs-4">Kami siap mendengar dan melayani Anda</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4 g-lg-5">
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
                                
                                <!-- Captcha -->
                                <div class="col-12">
                                    <label for="captcha" class="form-label fw-semibold fs-5">
                                        Verifikasi <span class="text-danger">*</span>
                                        <span class="ms-1 text-muted" style="font-weight:normal;">
                                            (Buktikan Anda bukan robot)
                                        </span>
                                    </label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-primary text-white fw-bold fs-5 px-4" id="captcha-question">
                                            <?= e($captchaQuestion) ?>
                                        </span>
                                        <input type="number" class="form-control form-control-lg"
                                               id="captcha" name="captcha"
                                               placeholder="Jawaban" autocomplete="off" required>
                                    </div>
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
                
                <?php if (!empty($siteAddress)): ?>
                <div class="d-flex mb-4">
                    <div class="flex-shrink-0">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="bi bi-geo-alt fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-4">
                        <h5 class="fw-bold">Alamat</h5>
                        <p class="text-muted fs-5 mb-0"><?= nl2br(e($siteAddress)) ?></p>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($sitePhone)): ?>
                <div class="d-flex mb-4">
                    <div class="flex-shrink-0">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="bi bi-telephone fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-4">
                        <h5 class="fw-bold">Telepon</h5>
                        <p class="text-muted fs-5 mb-0">
                            <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $sitePhone)) ?>" class="text-decoration-none text-muted">
                                <?= e($sitePhone) ?>
                            </a>
                        </p>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($siteWhatsapp)): ?>
                <div class="d-flex mb-4">
                    <div class="flex-shrink-0">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="bi bi-whatsapp fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-4">
                        <h5 class="fw-bold">WhatsApp</h5>
                        <p class="text-muted fs-5 mb-0">
                            <?php 
                            $waNumber = preg_replace('/[^0-9]/', '', $siteWhatsapp);
                            // Convert 08xxx to 628xxx
                            if (substr($waNumber, 0, 1) === '0') {
                                $waNumber = '62' . substr($waNumber, 1);
                            }
                            ?>
                            <a href="https://wa.me/<?= e($waNumber) ?>" target="_blank" class="text-decoration-none text-muted">
                                <?= e($siteWhatsapp) ?>
                            </a>
                        </p>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($siteEmail)): ?>
                <div class="d-flex mb-4">
                    <div class="flex-shrink-0">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="bi bi-envelope fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-4">
                        <h5 class="fw-bold">Email</h5>
                        <p class="text-muted fs-5 mb-0">
                            <a href="mailto:<?= e($siteEmail) ?>" class="text-decoration-none text-muted">
                                <?= e($siteEmail) ?>
                            </a>
                        </p>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($siteOperationalHours)): ?>
                <div class="d-flex mb-4">
                    <div class="flex-shrink-0">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="bi bi-clock fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-4">
                        <h5 class="fw-bold">Jam Operasional</h5>
                        <p class="text-muted fs-5 mb-0"><?= nl2br(e($siteOperationalHours)) ?></p>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($siteFacebook) || !empty($siteInstagram) || !empty($siteYoutube) || !empty($siteTiktok)): ?>
                <hr class="my-4">
                
                <h5 class="fw-bold mb-3">Ikuti Kami</h5>
                <div class="d-flex gap-3">
                    <?php if (!empty($siteFacebook)): ?>
                    <a href="<?= e($siteFacebook) ?>" target="_blank" class="btn btn-outline-primary btn-lg rounded-circle" title="Facebook">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($siteInstagram)): ?>
                    <a href="<?= e($siteInstagram) ?>" target="_blank" class="btn btn-outline-danger btn-lg rounded-circle" title="Instagram">
                        <i class="bi bi-instagram"></i>
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($siteYoutube)): ?>
                    <a href="<?= e($siteYoutube) ?>" target="_blank" class="btn btn-outline-danger btn-lg rounded-circle" title="YouTube">
                        <i class="bi bi-youtube"></i>
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($siteTiktok)): ?>
                    <a href="<?= e($siteTiktok) ?>" target="_blank" class="btn btn-outline-dark btn-lg rounded-circle" title="TikTok">
                        <i class="bi bi-tiktok"></i>
                    </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
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
