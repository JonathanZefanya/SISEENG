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
                        <h2 class="fw-bold text-primary mb-4">Awal Mula</h2>
                        <p class="fs-5 text-muted mb-4">
                            <?= e(APP_NAME) ?> didirikan pada tahun 1990 oleh sekelompok orang percaya 
                            yang memiliki kerinduan untuk melihat kuasa Tuhan bekerja di kota ini. 
                            Bermula dari pertemuan doa di rumah dengan hanya 12 orang, 
                            gereja ini terus bertumbuh oleh kasih karunia Tuhan.
                        </p>
                        
                        <h2 class="fw-bold text-primary mb-4 mt-5">Pertumbuhan</h2>
                        <p class="fs-5 text-muted mb-4">
                            Melalui tahun-tahun yang penuh tantangan dan berkat, gereja kami terus berkembang. 
                            Pada tahun 2000, kami membangun gedung gereja pertama yang menjadi rumah bagi 
                            ratusan jemaat. Pelayanan kami diperluas mencakup pelayanan anak, remaja, 
                            pemuda, keluarga, dan masyarakat.
                        </p>
                        
                        <h2 class="fw-bold text-primary mb-4 mt-5">Masa Kini</h2>
                        <p class="fs-5 text-muted mb-4">
                            Saat ini, <?= e(APP_NAME) ?> telah menjadi komunitas yang terdiri dari 
                            berbagai generasi dan latar belakang. Kami tetap berkomitmen pada panggilan awal 
                            kami: menyembah Tuhan, bertumbuh dalam firman-Nya, dan menjadi berkat bagi sesama.
                        </p>
                        
                        <!-- Timeline -->
                        <div class="mt-5">
                            <h3 class="fw-bold text-primary mb-4">Tonggak Sejarah</h3>
                            
                            <div class="timeline">
                                <div class="d-flex mb-4">
                                    <div class="flex-shrink-0 text-end" style="width: 100px;">
                                        <span class="badge bg-primary fs-6">1990</span>
                                    </div>
                                    <div class="flex-grow-1 ms-4 border-start ps-4">
                                        <h5 class="fw-semibold">Pendirian Gereja</h5>
                                        <p class="text-muted">Persekutuan doa pertama dengan 12 orang di rumah</p>
                                    </div>
                                </div>
                                
                                <div class="d-flex mb-4">
                                    <div class="flex-shrink-0 text-end" style="width: 100px;">
                                        <span class="badge bg-primary fs-6">2000</span>
                                    </div>
                                    <div class="flex-grow-1 ms-4 border-start ps-4">
                                        <h5 class="fw-semibold">Gedung Gereja Pertama</h5>
                                        <p class="text-muted">Pembangunan gedung gereja dengan kapasitas 300 orang</p>
                                    </div>
                                </div>
                                
                                <div class="d-flex mb-4">
                                    <div class="flex-shrink-0 text-end" style="width: 100px;">
                                        <span class="badge bg-primary fs-6">2010</span>
                                    </div>
                                    <div class="flex-grow-1 ms-4 border-start ps-4">
                                        <h5 class="fw-semibold">Perluasan Pelayanan</h5>
                                        <p class="text-muted">Membuka pelayanan sosial dan sekolah minggu</p>
                                    </div>
                                </div>
                                
                                <div class="d-flex">
                                    <div class="flex-shrink-0 text-end" style="width: 100px;">
                                        <span class="badge bg-success fs-6">Kini</span>
                                    </div>
                                    <div class="flex-grow-1 ms-4 border-start ps-4">
                                        <h5 class="fw-semibold">Terus Bertumbuh</h5>
                                        <p class="text-muted mb-0">Melayani ribuan jemaat dengan berbagai program</p>
                                    </div>
                                </div>
                            </div>
                        </div>
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
