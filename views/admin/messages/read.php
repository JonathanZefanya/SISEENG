<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">Baca Pesan</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= url('admin/messages') ?>">Pesan Masuk</a></li>
                <li class="breadcrumb-item active">Baca</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('admin/messages') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
        <form action="<?= url('admin/messages/delete/' . $message['id']) ?>" method="POST" class="d-inline" 
              onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesan ini?')">
            <?= csrfField() ?>
            <button type="submit" class="btn btn-outline-danger">
                <i class="bi bi-trash me-2"></i>Hapus
            </button>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h4 class="mb-0 fw-bold"><?= $message['subject'] ? e($message['subject']) : '(Tanpa Subjek)' ?></h4>
            </div>
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                         style="width: 60px; height: 60px;">
                        <i class="bi bi-person-fill fs-3"></i>
                    </div>
                    <div>
                        <h5 class="mb-1 fw-bold"><?= e($message['name']) ?></h5>
                        <p class="mb-0 text-muted"><?= e($message['email']) ?></p>
                    </div>
                </div>
                
                <div class="message-content fs-5 lh-lg">
                    <?= nl2br(e($message['message'])) ?>
                </div>
            </div>
        </div>
        
        <!-- Reply via Email -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-reply text-primary me-2"></i>Balas Pesan
                </h5>
            </div>
            <div class="card-body p-4">
                <p class="text-muted mb-4">
                    Anda dapat membalas pesan ini melalui email atau WhatsApp:
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="mailto:<?= e($message['email']) ?>?subject=Re: <?= e($message['subject']) ?>" 
                       class="btn btn-primary btn-lg">
                        <i class="bi bi-envelope me-2"></i>Balas via Email
                    </a>
                    <?php if ($message['phone']): ?>
                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $message['phone']) ?>" 
                       target="_blank" class="btn btn-success btn-lg">
                        <i class="bi bi-whatsapp me-2"></i>Balas via WhatsApp
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-info-circle text-primary me-2"></i>Informasi
                </h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-3">
                        <small class="text-muted d-block">Nama Pengirim</small>
                        <span class="fw-semibold"><?= e($message['name']) ?></span>
                    </li>
                    <li class="mb-3">
                        <small class="text-muted d-block">Email</small>
                        <a href="mailto:<?= e($message['email']) ?>" class="fw-semibold text-decoration-none">
                            <?= e($message['email']) ?>
                        </a>
                    </li>
                    <?php if ($message['phone']): ?>
                    <li class="mb-3">
                        <small class="text-muted d-block">No. Telepon</small>
                        <a href="tel:<?= e($message['phone']) ?>" class="fw-semibold text-decoration-none">
                            <?= e($message['phone']) ?>
                        </a>
                    </li>
                    <?php endif; ?>
                    <li class="mb-3">
                        <small class="text-muted d-block">Diterima Pada</small>
                        <span class="fw-semibold"><?= formatDate($message['created_at']) ?></span>
                        <br>
                        <small class="text-muted"><?= date('H:i', strtotime($message['created_at'])) ?> WIB</small>
                    </li>
                    <li class="mb-3">
                        <small class="text-muted d-block">Status</small>
                        <span class="badge bg-success">Sudah Dibaca</span>
                    </li>
                    <?php if ($message['ip_address']): ?>
                    <li>
                        <small class="text-muted d-block">IP Address</small>
                        <code><?= e($message['ip_address']) ?></code>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
