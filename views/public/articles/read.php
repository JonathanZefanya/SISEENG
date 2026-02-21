<section class="py-5 bg-primary text-white">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-3">
                <li class="breadcrumb-item"><a href="<?= url('/') ?>" class="text-white-50">Beranda</a></li>
                <li class="breadcrumb-item"><a href="<?= url('artikel') ?>" class="text-white-50">Artikel</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page">Baca</li>
            </ol>
        </nav>
        <h1 class="display-5 fw-bold"><?= e($article['title']) ?></h1>
        <div class="d-flex align-items-center text-white-50 mt-3">
            <span class="me-4">
                <i class="bi bi-person me-1"></i>
                <?= e($article['author_name'] ?? 'Admin') ?>
            </span>
            <span class="me-4">
                <i class="bi bi-calendar me-1"></i>
                <?= formatDate($article['created_at']) ?>
            </span>
            <?php if (isset($article['views'])): ?>
                <span>
                    <i class="bi bi-eye me-1"></i>
                    <?= number_format($article['views']) ?> dibaca
                </span>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if ($article['image']): ?>
                    <img src="<?= uploads(e($article['image'])) ?>" class="img-fluid rounded shadow-sm mb-4 w-100"
                        alt="<?= e($article['title']) ?>">
                <?php endif; ?>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <div class="article-content fs-5 lh-lg">
                            <?= nl2br(e($article['content'])) ?>
                        </div>
                    </div>
                </div>

                <!-- Share Buttons -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Bagikan Artikel Ini</h5>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(currentUrl()) ?>"
                                target="_blank" class="btn btn-primary">
                                <i class="bi bi-facebook me-2"></i>Facebook
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?= urlencode(currentUrl()) ?>&text=<?= urlencode($article['title']) ?>"
                                target="_blank" class="btn btn-info text-white">
                                <i class="bi bi-twitter me-2"></i>Twitter
                            </a>
                            <a href="https://wa.me/?text=<?= urlencode($article['title'] . ' ' . currentUrl()) ?>"
                                target="_blank" class="btn btn-success">
                                <i class="bi bi-whatsapp me-2"></i>WhatsApp
                            </a>
                            <button type="button" class="btn btn-secondary" onclick="copyLink()">
                                <i class="bi bi-link-45deg me-2"></i>Salin Link
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="d-flex justify-content-between mt-4">
                    <a href="<?= url('artikel') ?>" class="btn btn-outline-primary btn-lg">
                        <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar Artikel
                    </a>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4 mt-4 mt-lg-0">
                <?php if (isset($related_articles) && !empty($related_articles)): ?>
                    <div class="card border-0 shadow-sm sticky-top" style="top: 100px;">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 fw-bold">
                                <i class="bi bi-journal-text me-2"></i>Artikel Lainnya
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                <?php foreach ($related_articles as $related): ?>
                                    <li class="list-group-item p-3">
                                        <a href="<?= url('artikel/' . $related['slug']) ?>"
                                            class="text-decoration-none text-dark">
                                            <h6 class="fw-semibold mb-1"><?= e($related['title']) ?></h6>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar me-1"></i>
                                                <?= formatDate($related['created_at']) ?>
                                            </small>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="card-footer bg-white">
                            <a href="<?= url('artikel') ?>" class="btn btn-outline-primary w-100">
                                Lihat Semua Artikel
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
    function copyLink() {
        navigator.clipboard.writeText(window.location.href).then(function () {
            alert('Link berhasil disalin!');
        });
    }
</script>

<style>
    .article-content p {
        margin-bottom: 1.5rem;
    }

    .article-content img {
        max-width: 100%;
        height: auto;
        border-radius: 0.5rem;
    }
</style>