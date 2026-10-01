<?php
require_once __DIR__ . '/_helpers.php';
?>
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= url('admin/jemaat/detail/' . $family['id']) ?>" class="btn btn-light btn-icon flex-shrink-0" aria-label="Kembali ke detail KK"><i class="bi bi-arrow-left"></i></a>
    <div class="min-w-0">
        <h1 class="h3 fw-bold mb-0">Edit <?= e($family['family_code']) ?></h1>
        <p class="text-muted mb-0 small">Keluarga <?= e($family['head_name'] ?: '-') ?> · kepala & anggota diubah dari halaman detail KK</p>
    </div>
</div>

<form action="<?= url('admin/jemaat/edit-kk/' . $family['id']) ?>" method="POST">
    <?= csrfField() ?>
    <section class="jm-form-section">
        <div class="jm-form-head"><span class="jm-step"><i class="bi bi-house-heart"></i></span><div><h2>Alamat &amp; kontak KK</h2></div></div>
        <div class="jm-form-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label" for="address">Alamat rumah</label>
                    <textarea class="form-control" id="address" name="address" rows="3"><?= e($familyRow['address']) ?></textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="phone">Telepon rumah</label>
                    <input type="tel" class="form-control" id="phone" name="phone" value="<?= e($familyRow['phone']) ?>" maxlength="20" inputmode="tel">
                </div>
                <div class="col-12">
                    <label class="form-label" for="notes">Catatan KK</label>
                    <input type="text" class="form-control" id="notes" name="notes" value="<?= e($familyRow['notes']) ?>">
                </div>
            </div>
        </div>
    </section>

    <?php jmActionBar(url('admin/jemaat/detail/' . $family['id']), 'Simpan', 'check-lg'); ?>
</form>
