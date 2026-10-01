<?php
require_once __DIR__ . '/_helpers.php';

$isHead = $row['relationship'] === 'head';
$spouseMode = old('spouse_mode', 'existing');
$mode = old('mode', $isHead ? 'join_member' : 'new_family');
$headChoice = old('head_choice', $member['gender'] === 'M' ? 'member' : 'spouse');

jmActionStart('Catat pernikahan', 'Status keduanya menjadi Menikah dan KK diatur sekaligus.', $member, $row, $family);
?>
<form action="<?= url('admin/jemaat/anggota/menikah/' . $member['id']) ?>" method="POST">
    <?= csrfField() ?>

    <section class="jm-form-section">
        <div class="jm-form-head"><span class="jm-step">1</span><div><h2>Pasangan</h2><p>Pilih jemaat yang sudah terdaftar, atau isi data orang baru.</p></div></div>
        <div class="jm-form-body">
            <div class="jm-options cols-2 mb-3">
                <?= jmOption('spouse_mode', 'existing', $spouseMode === 'existing', 'person-check', 'Sudah terdaftar', 'Cari dari data jemaat aktif.') ?>
                <?= jmOption('spouse_mode', 'new', $spouseMode === 'new', 'person-plus', 'Orang baru', 'Data pasangan dibuat sekarang.') ?>
            </div>
            <div data-show-for="spouse_mode=existing">
                <?php jmPicker('spouse_id', 'member', null, 'Cari nama pasangan...'); ?>
            </div>
            <div data-show-for="spouse_mode=new">
                <?php jmPersonFields('spouse', old('spouse', ['gender' => $member['gender'] === 'M' ? 'F' : 'M']), ['marital' => false, 'idPrefix' => 'sp']); ?>
            </div>
        </div>
    </section>

    <section class="jm-form-section">
        <div class="jm-form-head"><span class="jm-step">2</span><div><h2>Kartu Keluarga setelah menikah</h2></div></div>
        <div class="jm-form-body">
            <div class="jm-options">
                <?= jmOption('mode', 'new_family', $mode === 'new_family', 'house-add', 'Buat KK baru',
                    'Keduanya keluar dari KK lama. KK asal tercatat ' . e($family['family_code']) . ' dan mendapat penanda "Anak sudah berkeluarga".') ?>
                <?= jmOption('mode', 'join_member', $mode === 'join_member', 'box-arrow-in-down', 'Pasangan masuk ke KK ' . $member['full_name'],
                    $isHead ? 'Pasangan tercatat sebagai Suami/Istri di ' . e($family['family_code']) . '.' : 'Hanya bisa bila ' . e($member['full_name']) . ' kepala KK-nya.',
                    $isHead ? '' : 'disabled') ?>
                <?= jmOption('mode', 'join_spouse', $mode === 'join_spouse', 'box-arrow-in-right', $member['full_name'] . ' masuk ke KK pasangan',
                    'Pasangan harus jemaat terdaftar dan kepala KK-nya.') ?>
            </div>

            <div class="row g-3 mt-1" data-show-for="mode=new_family">
                <div class="col-md-5">
                    <span class="form-label d-block">Kepala KK baru</span>
                    <?= jmSegment('head_choice', ['member' => strtok($member['full_name'], ' '), 'spouse' => 'Pasangan'], $headChoice, 'hc') ?>
                </div>
                <div class="col-md-7">
                    <label class="form-label" for="new_address">Alamat KK baru</label>
                    <input type="text" class="form-control" id="new_address" name="new_address" value="<?= e(old('new_address')) ?>">
                </div>
            </div>
        </div>
    </section>

    <section class="jm-form-section">
        <div class="jm-form-head"><span class="jm-step">3</span><div><h2>Pemberkatan</h2></div></div>
        <div class="jm-form-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label" for="date">Tanggal menikah</label>
                    <input type="date" class="form-control" id="date" name="date" value="<?= e(old('date', date('Y-m-d'))) ?>">
                </div>
                <div class="col-md-8">
                    <label class="form-label" for="note">Keterangan</label>
                    <input type="text" class="form-control" id="note" name="note" value="<?= e(old('note')) ?>" placeholder="Mis. tempat pemberkatan">
                </div>
            </div>
        </div>
    </section>

    <?php jmActionBar(url('admin/jemaat/detail/' . $row['family_id']), 'Catat pernikahan', 'hearts', '', 'btn-primary', true); ?>
</form>
<?php jmActionEnd(); ?>
<?php jmScripts(); ?>
