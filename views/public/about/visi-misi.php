<section class="py-5 bg-primary text-white">
    <div class="container text-center">
        <h1 class="display-4 fw-bold mb-3">Visi &amp; Misi</h1>
        <p class="lead fs-4">Arah dan tujuan pelayanan kami</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <!-- Visi -->
                <div class="card border-0 shadow-sm mb-5">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                style="width: 80px; height: 80px;">
                                <i class="bi bi-eye-fill fs-2"></i>
                            </div>
                            <h2 class="display-6 fw-bold text-primary">VISI</h2>
                        </div>
                        <blockquote class="blockquote text-center">
                            <p class="fs-3 fw-light fst-italic">
                                "<?= nl2br(e($about['vision'])) ?>"
                            </p>
                        </blockquote>
                    </div>
                </div>

                <!-- Misi -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5">
                        <div class="text-center mb-5">
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                style="width: 80px; height: 80px;">
                                <i class="bi bi-bullseye fs-2"></i>
                            </div>
                            <h2 class="display-6 fw-bold text-primary">MISI</h2>
                        </div>

                        <?php
                        // Pisahkan setiap baris menjadi list item
                        $missions = array_values(array_filter(
                            array_map('trim', explode("\n", $about['mission']))
                        ));
                        ?>
                        <?php if (!empty($missions)): ?>
                            <div class="row g-4">
                                <?php foreach ($missions as $i => $item): ?>
                                    <div class="col-md-6">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                                    style="width: 50px; height: 50px;">
                                                    <span class="fw-bold"><?= $i + 1 ?></span>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <p class="fs-5 mb-0 mt-2"><?= e($item) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
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