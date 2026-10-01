<?php
require_once __DIR__ . '/_helpers.php';

$mode = old('mode', 'new');
?>
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= url('admin/jemaat/detail/' . $family['id']) ?>" class="btn btn-light btn-icon flex-shrink-0" aria-label="Kembali ke detail KK"><i class="bi bi-arrow-left"></i></a>
    <div class="min-w-0">
        <h1 class="h3 fw-bold mb-0">Tambah anggota</h1>
        <p class="text-muted mb-0 small">Ke <?= e($family['family_code']) ?> · Kepala: <?= e($family['head_name']) ?></p>
    </div>
</div>

<form action="<?= url('admin/jemaat/anggota/tambah/' . $family['id']) ?>" method="POST">
    <?= csrfField() ?>

    <section class="jm-form-section">
        <div class="jm-form-head"><span class="jm-step">1</span><div><h2>Siapa yang ditambahkan?</h2></div></div>
        <div class="jm-form-body">
            <div class="jm-options cols-2">
                <?= jmOption('mode', 'new', $mode === 'new', 'person-plus', 'Orang baru', 'Belum terdaftar sebagai jemaat.') ?>
                <?= jmOption('mode', 'existing', $mode === 'existing', 'person-check', 'Jemaat terdaftar', 'Dipindahkan dari KK-nya sekarang (riwayat tetap ada).') ?>
            </div>
        </div>
    </section>

    <section class="jm-form-section">
        <div class="jm-form-head"><span class="jm-step">2</span><div><h2>Data anggota</h2></div></div>
        <div class="jm-form-body">
            <div class="row g-3 mb-3">
                <div class="col-sm-6 col-md-4">
                    <label class="form-label" for="relationship">Hubungan dengan kepala</label>
                    <select class="form-select" id="relationship" name="relationship" required>
                        <?= jmRelationshipOptions(old('relationship', 'child')) ?>
                    </select>
                </div>
                <div class="col-sm-6 col-md-4">
                    <label class="form-label" for="date">Tanggal masuk KK</label>
                    <input type="date" class="form-control" id="date" name="date" value="<?= e(old('date', date('Y-m-d'))) ?>">
                </div>
            </div>
            <div class="jm-callout t-blue mb-3" data-show-for="relationship=spouse" hidden>
                <i class="bi bi-heart"></i><div>Suami/Istri otomatis dicatat <strong>Menikah</strong> dengan <?= e($family['head_name']) ?>.</div>
            </div>

            <div data-show-for="mode=new">
                <?php jmPersonFields('person', old('person', [])); ?>
            </div>
            <div data-show-for="mode=existing">
                <?php jmPicker('existing_id', 'member', (int) $family['id'], 'Cari nama jemaat aktif...'); ?>
                <div class="mt-3">
                    <label class="form-label" for="note">Keterangan</label>
                    <input type="text" class="form-control" id="note" name="note" value="<?= e(old('note')) ?>">
                </div>
            </div>
        </div>
    </section>

    <?php jmActionBar(url('admin/jemaat/detail/' . $family['id']), 'Tambahkan', 'person-plus'); ?>
</form>
<?php jmScripts(); ?>
