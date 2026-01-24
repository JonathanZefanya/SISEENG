<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">Detail Jemaat</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= url('admin/members') ?>">Data Jemaat</a></li>
                <li class="breadcrumb-item active">Detail</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="<?= url('admin/members/edit/' . $member['id']) ?>" class="btn btn-primary me-2">
            <i class="bi bi-pencil me-2"></i>Edit
        </a>
        <a href="<?= url('admin/members') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center p-4">
                <div class="bg-<?= $member['gender'] === 'M' ? 'primary' : 'pink' ?> text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                     style="width: 100px; height: 100px; font-size: 2.5rem; <?= $member['gender'] === 'F' ? 'background-color: #e91e63;' : '' ?>">
                    <i class="bi bi-person-fill"></i>
                </div>
                <h4 class="fw-bold mb-1"><?= e($member['full_name']) ?></h4>
                <p class="text-muted mb-3"><?= $member['gender'] === 'M' ? 'Laki-laki' : 'Perempuan' ?></p>
                
                <?php if ($member['status'] === 'active'): ?>
                    <span class="badge bg-success fs-6">Aktif</span>
                <?php else: ?>
                    <span class="badge bg-secondary fs-6">Tidak Aktif</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold"><i class="bi bi-person text-primary me-2"></i>Data Pribadi</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-muted small">Nama Lengkap</label>
                        <p class="fw-semibold mb-0"><?= e($member['full_name']) ?></p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Jenis Kelamin</label>
                        <p class="fw-semibold mb-0"><?= $member['gender'] === 'M' ? 'Laki-laki' : 'Perempuan' ?></p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Tempat Lahir</label>
                        <p class="fw-semibold mb-0"><?= e($member['birth_place'] ?: '-') ?></p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Tanggal Lahir</label>
                        <p class="fw-semibold mb-0"><?= $member['birth_date'] ? formatDate($member['birth_date']) : '-' ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold"><i class="bi bi-telephone text-primary me-2"></i>Kontak</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-muted small">No. Telepon</label>
                        <p class="fw-semibold mb-0">
                            <?php if ($member['phone']): ?>
                                <a href="tel:<?= e($member['phone']) ?>" class="text-decoration-none">
                                    <i class="bi bi-telephone text-primary me-1"></i><?= e($member['phone']) ?>
                                </a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Email</label>
                        <p class="fw-semibold mb-0">
                            <?php if ($member['email']): ?>
                                <a href="mailto:<?= e($member['email']) ?>" class="text-decoration-none">
                                    <i class="bi bi-envelope text-primary me-1"></i><?= e($member['email']) ?>
                                </a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="col-12">
                        <label class="text-muted small">Alamat</label>
                        <p class="fw-semibold mb-0"><?= nl2br(e($member['address'] ?: '-')) ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold"><i class="bi bi-info-circle text-primary me-2"></i>Informasi Keanggotaan</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-muted small">Tanggal Baptis</label>
                        <p class="fw-semibold mb-0"><?= $member['baptism_date'] ? formatDate($member['baptism_date']) : '-' ?></p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Tanggal Bergabung</label>
                        <p class="fw-semibold mb-0"><?= $member['membership_date'] ? formatDate($member['membership_date']) : '-' ?></p>
                    </div>
                    <?php if ($member['notes']): ?>
                    <div class="col-12">
                        <label class="text-muted small">Catatan</label>
                        <p class="fw-semibold mb-0"><?= nl2br(e($member['notes'])) ?></p>
                    </div>
                    <?php endif; ?>
                    <div class="col-md-6">
                        <label class="text-muted small">Dibuat pada</label>
                        <p class="fw-semibold mb-0"><?= formatDateTime($member['created_at']) ?></p>
                    </div>
                    <?php if ($member['updated_at']): ?>
                    <div class="col-md-6">
                        <label class="text-muted small">Terakhir diupdate</label>
                        <p class="fw-semibold mb-0"><?= formatDateTime($member['updated_at']) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
