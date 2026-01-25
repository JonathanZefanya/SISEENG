<section class="py-5 bg-primary text-white">
    <div class="container text-center">
        <h1 class="display-4 fw-bold mb-3"><?= e(setting('donation_title', 'Donasi')) ?></h1>
        <p class="lead fs-4"><?= e(setting('donation_description', 'Dukungan Anda sangat berarti bagi pelayanan gereja')) ?></p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
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
                        <?php 
                        // Load donation accounts from database
                        $accountModel = new \App\Models\DonationAccount();
                        $accounts = $accountModel->getActive();
                        $totalAccounts = count($accounts);
                        ?>
                        
                        <?php if (!empty($accounts)): ?>
                        <div class="row g-4 justify-content-center">
                            <?php foreach ($accounts as $account): 
                                // Determine column size based on total accounts
                                $colClass = 'col-md-6';
                                if ($totalAccounts == 1) {
                                    $colClass = 'col-md-6';
                                } elseif ($totalAccounts == 2) {
                                    $colClass = 'col-md-6';
                                } elseif ($totalAccounts >= 3) {
                                    $colClass = 'col-md-6 col-lg-4';
                                }
                            ?>
                            <div class="<?= $colClass ?>">
                                <div class="p-4 bg-light rounded text-center h-100 d-flex flex-column">
                                    <?php if ($account['bank_logo']): ?>
                                    <div class="mb-3">
                                        <img src="<?= uploads('donations/' . $account['bank_logo']) ?>" 
                                             alt="<?= e($account['bank_name']) ?>" 
                                             style="max-height: 50px; max-width: 120px; object-fit: contain;">
                                    </div>
                                    <?php else: ?>
                                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 60px; height: 60px;">
                                        <i class="bi bi-bank fs-4"></i>
                                    </div>
                                    <?php endif; ?>
                                    <h5 class="fw-bold mb-2"><?= e($account['bank_name']) ?></h5>
                                    <p class="fw-bold text-primary mb-1" style="font-size: 1.4rem; letter-spacing: 1px;">
                                        <?= e($account['account_number']) ?>
                                    </p>
                                    <p class="text-muted mb-3">a.n. <?= e($account['account_name']) ?></p>
                                    <div class="mt-auto">
                                        <button class="btn btn-outline-primary" onclick="copyToClipboard('<?= e($account['account_number']) ?>', this)">
                                            <i class="bi bi-clipboard me-2"></i>Salin No. Rekening
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-info text-center mb-0">
                            <i class="bi bi-info-circle me-2"></i>
                            Informasi rekening bank belum tersedia. Silakan hubungi kami untuk informasi lebih lanjut.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- QRIS -->
                <?php $qrisImage = setting('donation_qris_image'); ?>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h4 class="mb-0 fw-bold">
                            <i class="bi bi-qr-code text-primary me-2"></i>QRIS (Scan & Pay)
                        </h4>
                    </div>
                    <div class="card-body p-4 text-center">
                        <p class="text-muted mb-4 fs-5">Scan kode QR di bawah ini menggunakan aplikasi e-wallet atau mobile banking Anda</p>
                        
                        <?php if (!empty($qrisImage)): ?>
                        <div class="d-inline-block p-3 bg-white border rounded shadow-sm">
                            <img src="<?= uploads('donations/' . $qrisImage) ?>" alt="QRIS" style="max-width: 280px; width: 100%;">
                            <?php if (setting('donation_qris_name')): ?>
                            <p class="mt-2 mb-0 fw-semibold text-primary"><?= e(setting('donation_qris_name')) ?></p>
                            <?php endif; ?>
                        </div>
                        <?php else: ?>
                        <div class="d-inline-block p-4 bg-light border rounded">
                            <div class="d-flex align-items-center justify-content-center" style="width: 200px; height: 200px;">
                                <div class="text-center text-muted">
                                    <i class="bi bi-qr-code display-1"></i>
                                    <p class="mt-2 mb-0 small">QRIS belum tersedia</p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
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
function copyToClipboard(text, btn) {
    navigator.clipboard.writeText(text).then(function() {
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check-lg me-2"></i>Tersalin!';
        btn.classList.remove('btn-outline-primary');
        btn.classList.add('btn-success');
        
        setTimeout(function() {
            btn.innerHTML = originalHtml;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-primary');
        }, 2000);
    });
}
</script>
