<section class="py-5 bg-primary text-white">
    <div class="container text-center">
        <h1 class="display-4 fw-bold mb-3"><?= e(setting('donation_title', 'Donasi')) ?></h1>
        <p class="lead fs-4"><?= e(setting('donation_description', 'Dukungan Anda sangat berarti bagi pelayanan gereja')) ?></p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <h2 class="fw-bold text-primary mb-3">Persembahan & Donasi</h2>
                    <p class="lead text-muted">
                        "Hendaklah masing-masing memberikan menurut kerelaan hatinya, jangan dengan sedih hati atau karena paksaan, sebab Allah mengasihi orang yang memberi dengan sukacita."
                        <br><small>- 2 Korintus 9:7</small>
                    </p>
                </div>
                
                <!-- Transfer Bank -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h4 class="mb-0 fw-bold">
                            <i class="bi bi-bank text-primary me-2"></i>Transfer Bank
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <?php if (setting('donation_bank_name')): ?>
                            <div class="col-md-6">
                                <div class="p-4 bg-light rounded text-center">
                                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                        <i class="bi bi-bank fs-4"></i>
                                    </div>
                                    <h5 class="fw-bold mb-2"><?= e(setting('donation_bank_name')) ?></h5>
                                    <p class="display-6 fw-bold text-primary mb-1"><?= e(setting('donation_bank_account')) ?></p>
                                    <p class="text-muted mb-0">a.n. <?= e(setting('donation_account_name')) ?></p>
                                    <button class="btn btn-outline-primary mt-3" onclick="copyToClipboard('<?= e(setting('donation_bank_account')) ?>')">
                                        <i class="bi bi-clipboard me-2"></i>Salin No. Rekening
                                    </button>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (setting('donation_bank_name_2')): ?>
                            <div class="col-md-6">
                                <div class="p-4 bg-light rounded text-center">
                                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                        <i class="bi bi-bank fs-4"></i>
                                    </div>
                                    <h5 class="fw-bold mb-2"><?= e(setting('donation_bank_name_2')) ?></h5>
                                    <p class="display-6 fw-bold text-primary mb-1"><?= e(setting('donation_bank_account_2')) ?></p>
                                    <p class="text-muted mb-0">a.n. <?= e(setting('donation_account_name_2')) ?></p>
                                    <button class="btn btn-outline-primary mt-3" onclick="copyToClipboard('<?= e(setting('donation_bank_account_2')) ?>')">
                                        <i class="bi bi-clipboard me-2"></i>Salin No. Rekening
                                    </button>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!setting('donation_bank_name') && !setting('donation_bank_name_2')): ?>
                            <div class="col-12">
                                <div class="alert alert-info text-center">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Informasi rekening bank belum diatur. Silakan hubungi admin.
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- QRIS -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h4 class="mb-0 fw-bold">
                            <i class="bi bi-qr-code text-primary me-2"></i>QRIS (Scan & Pay)
                        </h4>
                    </div>
                    <div class="card-body p-4 text-center">
                        <p class="text-muted mb-4 fs-5">Scan kode QR di bawah ini menggunakan aplikasi e-wallet atau mobile banking Anda</p>
                        <div class="d-inline-block p-4 bg-white border rounded shadow-sm">
                            <!-- Placeholder QR Code -->
                            <div class="bg-light d-flex align-items-center justify-content-center" style="width: 250px; height: 250px;">
                                <div class="text-center text-muted">
                                    <i class="bi bi-qr-code display-1"></i>
                                    <p class="mt-2 mb-0 small">QR Code akan ditampilkan di sini</p>
                                </div>
                            </div>
                        </div>
                        <p class="mt-4 text-muted">
                            <i class="bi bi-info-circle me-2"></i>
                            Didukung oleh: GoPay, OVO, DANA, LinkAja, ShopeePay, dan semua bank
                        </p>
                    </div>
                </div>
                
                <!-- Catatan -->
                <div class="card border-0 shadow-sm bg-warning bg-opacity-10">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">
                            <i class="bi bi-lightbulb text-warning me-2"></i>Catatan Penting
                        </h5>
                        <ul class="list-unstyled mb-0 fs-5">
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Setelah transfer, silakan konfirmasi via WhatsApp <?= setting('site_whatsapp') ? '(' . e(setting('site_whatsapp')) . ')' : '' ?> atau email
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Cantumkan nama dan keperluan donasi pada berita transfer
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Bukti transfer akan menjadi konfirmasi donasi Anda
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="text-center mt-5">
                    <p class="fs-5 text-muted">Ada pertanyaan tentang donasi?</p>
                    <a href="<?= url('kontak') ?>" class="btn btn-primary btn-lg px-5">
                        <i class="bi bi-chat-dots me-2"></i>Hubungi Kami
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Nomor rekening berhasil disalin!');
    });
}
</script>
