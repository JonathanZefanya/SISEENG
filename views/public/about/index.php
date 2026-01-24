<section class="py-5 bg-primary text-white">
    <div class="container text-center">
        <h1 class="display-4 fw-bold mb-3">Tentang Kami</h1>
        <p class="lead fs-4">Mengenal lebih dekat <?= e(APP_NAME) ?></p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <img src="<?= asset('images/about-church.jpg') ?>" alt="Gereja" class="img-fluid rounded shadow" onerror="this.src='https://via.placeholder.com/600x400?text=Foto+Gereja'">
            </div>
            <div class="col-lg-6">
                <h2 class="display-6 fw-bold text-primary mb-4">Selamat Datang di <?= e(APP_NAME) ?></h2>
                <p class="lead fs-5 text-muted mb-4">
                    Kami adalah komunitas orang percaya yang berkumpul untuk menyembah Tuhan, 
                    bertumbuh dalam iman, dan melayani sesama dengan kasih Kristus.
                </p>
                <p class="fs-5 text-muted mb-4">
                    Gereja kami didirikan dengan visi untuk menjadi berkat bagi kota dan bangsa. 
                    Kami percaya bahwa setiap orang berharga di mata Tuhan dan memiliki tujuan 
                    yang mulia dalam hidup ini.
                </p>
                <div class="d-flex gap-3">
                    <a href="<?= url('tentang/visi-misi') ?>" class="btn btn-primary btn-lg">
                        <i class="bi bi-eye me-2"></i>Visi & Misi
                    </a>
                    <a href="<?= url('tentang/sejarah') ?>" class="btn btn-outline-primary btn-lg">
                        <i class="bi bi-book me-2"></i>Sejarah
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Nilai-Nilai Kami -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="display-6 fw-bold text-center text-primary mb-5">Nilai-Nilai Kami</h2>
        
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm text-center p-4">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-heart-fill fs-2"></i>
                    </div>
                    <h4 class="fw-bold">Kasih</h4>
                    <p class="text-muted mb-0">Mengasihi Tuhan dan sesama seperti diri sendiri</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm text-center p-4">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-book-fill fs-2"></i>
                    </div>
                    <h4 class="fw-bold">Kebenaran</h4>
                    <p class="text-muted mb-0">Berpegang teguh pada Firman Tuhan</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm text-center p-4">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-people-fill fs-2"></i>
                    </div>
                    <h4 class="fw-bold">Persekutuan</h4>
                    <p class="text-muted mb-0">Bertumbuh bersama dalam komunitas</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm text-center p-4">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-hand-thumbs-up-fill fs-2"></i>
                    </div>
                    <h4 class="fw-bold">Pelayanan</h4>
                    <p class="text-muted mb-0">Melayani dengan sukacita dan kerendahan hati</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-5 bg-primary text-white">
    <div class="container text-center">
        <h2 class="display-6 fw-bold mb-4">Bergabunglah Bersama Kami</h2>
        <p class="lead fs-4 mb-4">
            Kami mengundang Anda untuk mengalami persekutuan yang hangat dan penuh kasih.
        </p>
        <a href="<?= url('kontak') ?>" class="btn btn-warning btn-lg px-5 py-3 fs-5">
            <i class="bi bi-person-plus me-2"></i>Hubungi Kami
        </a>
    </div>
</section>
