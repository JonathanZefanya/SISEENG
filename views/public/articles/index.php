<section class="py-5 bg-primary text-white">
    <div class="container text-center">
        <h1 class="display-4 fw-bold mb-3">
            <?php if (!empty($selectedCategory)): ?>
                <?= e($selectedCategory['name']) ?>
            <?php else: ?>
                Artikel & Renungan
            <?php endif; ?>
        </h1>
        <p class="lead fs-4">
            <?php if (!empty($selectedCategory) && $selectedCategory['description']): ?>
                <?= e($selectedCategory['description']) ?>
            <?php else: ?>
                Bacaan rohani untuk pertumbuhan iman Anda
            <?php endif; ?>
        </p>
    </div>
</section>

<!-- Filter Kategori -->
<?php if (!empty($categories)): ?>
    <section class="py-4 bg-light border-bottom">
        <div class="container">
            <div class="d-flex align-items-center gap-2 flex-wrap justify-content-center">
                <span class="text-muted me-2 fw-medium"><i class="bi bi-filter me-1"></i>Kategori:</span>
                <a href="<?= url('artikel') ?>"
                    class="btn btn-sm <?= empty($categorySlug) ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill px-3">
                    <i class="bi bi-grid me-1"></i>Semua
                </a>
                <?php foreach ($categories as $cat): ?>
                    <a href="<?= url('artikel?kategori=' . $cat['slug']) ?>"
                        class="btn btn-sm rounded-pill px-3 <?= ($categorySlug ?? '') === $cat['slug'] ? '' : 'btn-outline-secondary' ?>"
                        style="<?= ($categorySlug ?? '') === $cat['slug'] ? 'background-color: ' . e($cat['color']) . '; border-color: ' . e($cat['color']) . '; color: white;' : '' ?>">
                        <?= e($cat['name']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<section class="py-5">
    <div class="container">
        <?php if (empty($articles)): ?>
            <div class="text-center py-5">
                <i class="bi bi-journal-x text-muted display-1"></i>
                <h3 class="mt-4 text-muted">Belum ada artikel</h3>
                <p class="text-muted fs-5">
                    <?php if (!empty($selectedCategory)): ?>
                        Belum ada artikel dalam kategori "<?= e($selectedCategory['name']) ?>"
                    <?php else: ?>
                        Silakan kembali lagi nanti untuk membaca artikel terbaru
                    <?php endif; ?>
                </p>
                <?php if (!empty($selectedCategory)): ?>
                    <a href="<?= url('artikel') ?>" class="btn btn-primary btn-lg mt-3">
                        <i class="bi bi-grid me-2"></i>Lihat Semua Artikel
                    </a>
                <?php else: ?>
                    <a href="<?= url('/') ?>" class="btn btn-primary btn-lg mt-3">
                        <i class="bi bi-house me-2"></i>Kembali ke Beranda
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <!-- Featured Article (First) -->
            <?php if (isset($articles[0])): ?>
                <div class="card border-0 shadow-sm mb-5 overflow-hidden">
                    <div class="row g-0">
                        <div class="col-md-6">
                            <?php if ($articles[0]['image']): ?>
                                <img src="<?= uploads(e($articles[0]['image'])) ?>" class="img-fluid h-100 w-100"
                                    alt="<?= e($articles[0]['title']) ?>" style="object-fit: cover; min-height: 300px;">
                            <?php else: ?>
                                <div class="bg-primary text-white d-flex align-items-center justify-content-center h-100"
                                    style="min-height: 300px;">
                                    <i class="bi bi-journal-text display-1"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <div class="card-body p-4 p-md-5 d-flex flex-column h-100">
                                <?php if (!empty($articles[0]['category_name'])): ?>
                                    <span class="badge mb-3"
                                        style="width: fit-content; background-color: <?= e($articles[0]['category_color'] ?? '#6c757d') ?>">
                                        <?= e($articles[0]['category_name']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-primary mb-3" style="width: fit-content;">Artikel Terbaru</span>
                                <?php endif; ?>
                                <h2 class="card-title fw-bold"><?= e($articles[0]['title']) ?></h2>
                                <p class="card-text text-muted fs-5 flex-grow-1">
                                    <?= e(substr(strip_tags($articles[0]['content']), 0, 200)) ?>...
                                </p>
                                <div class="d-flex align-items-center justify-content-between mt-auto">
                                    <small class="text-muted">
                                        <i class="bi bi-calendar me-1"></i>
                                        <?= formatDate($articles[0]['created_at']) ?>
                                    </small>
                                    <a href="<?= url('artikel/' . $articles[0]['slug']) ?>" class="btn btn-primary">
                                        Baca Selengkapnya <i class="bi bi-arrow-right ms-2"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Other Articles -->
            <?php if (count($articles) > 1): ?>
                <div class="row g-4">
                    <?php foreach (array_slice($articles, 1) as $article): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border-0 shadow-sm article-card">
                                <?php if ($article['image']): ?>
                                    <img src="<?= uploads(e($article['image'])) ?>" class="card-img-top"
                                        alt="<?= e($article['title']) ?>" style="height: 200px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-secondary text-white d-flex align-items-center justify-content-center"
                                        style="height: 200px;">
                                        <i class="bi bi-journal-text display-3"></i>
                                    </div>
                                <?php endif; ?>

                                <div class="card-body p-4">
                                    <?php if (!empty($article['category_name'])): ?>
                                        <span class="badge mb-2"
                                            style="background-color: <?= e($article['category_color'] ?? '#6c757d') ?>">
                                            <?= e($article['category_name']) ?>
                                        </span>
                                    <?php endif; ?>
                                    <h5 class="card-title fw-bold"><?= e($article['title']) ?></h5>
                                    <p class="card-text text-muted">
                                        <?= e(substr(strip_tags($article['content']), 0, 100)) ?>...
                                    </p>
                                </div>

                                <div class="card-footer bg-white border-0 p-4 pt-0">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <small class="text-muted">
                                            <i class="bi bi-calendar me-1"></i>
                                            <?= formatDate($article['created_at']) ?>
                                        </small>
                                        <a href="<?= url('artikel/' . $article['slug']) ?>" class="btn btn-sm btn-outline-primary">
                                            Baca <i class="bi bi-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Pagination -->
            <?php if (isset($pagination) && isset($pagination['last_page']) && $pagination['last_page'] > 1): ?>
                <?php $queryParam = !empty($categorySlug) ? '&kategori=' . e($categorySlug) : ''; ?>
                <nav aria-label="Page navigation" class="mt-5">
                    <ul class="pagination justify-content-center pagination-lg">
                        <li class="page-item <?= $pagination['current_page'] <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link"
                                href="<?= url('artikel?page=' . ($pagination['current_page'] - 1) . $queryParam) ?>">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>

                        <?php for ($i = 1; $i <= $pagination['last_page']; $i++): ?>
                            <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                                <a class="page-link" href="<?= url('artikel?page=' . $i . $queryParam) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item <?= $pagination['current_page'] >= $pagination['last_page'] ? 'disabled' : '' ?>">
                            <a class="page-link"
                                href="<?= url('artikel?page=' . ($pagination['current_page'] + 1) . $queryParam) ?>">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<style>
    .article-card:hover {
        transform: translateY(-5px);
        transition: transform 0.3s ease;
    }
</style>