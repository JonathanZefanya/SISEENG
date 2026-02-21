<section class="py-5 bg-primary text-white">
    <div class="container text-center">
        <h1 class="display-4 fw-bold mb-3">Sejarah Gereja</h1>
        <p class="lead fs-4">Perjalanan iman kami dari waktu ke waktu</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5">

                        <?php if (!empty($about['history'])): ?>
                            <?php
                            // Pecah sejarah per paragraf (jika ada baris kosong)
                            $paragraphs = array_values(array_filter(
                                array_map('trim', preg_split('/\n{2,}/', $about['history']))
                            ));
                            ?>
                            <?php if (count($paragraphs) > 1): ?>
                                <?php foreach ($paragraphs as $para): ?>
                                    <p class="fs-5 text-muted mb-4"><?= nl2br(e($para)) ?></p>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="fs-5 text-muted mb-4"><?= nl2br(e($about['history'])) ?></p>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="fs-5 text-muted mb-4">
                                <?= e(APP_NAME) ?> didirikan dengan visi untuk menjangkau masyarakat
                                dan menjadi berkat bagi banyak orang.
                            </p>
                        <?php endif; ?>

                    </div>
                </div>

                <!-- Back Link -->
                <div class="text-center mt-5">
                    <a href="<?= url('tentang') ?>" class="btn btn-outline-primary btn-lg">
                        <i class="bi bi-arrow-left me-2"></i>Kembali ke Tentang Kami
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
