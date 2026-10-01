<?php
require_once __DIR__ . '/_helpers.php';

jmActionStart('Jadikan kepala keluarga', 'Kepala sebelumnya (' . ($family['head_name'] ?: '-') . ') tetap menjadi anggota KK ini.', $member, $row, $family);
?>
<form action="<?= url('admin/jemaat/anggota/kepala/' . $member['id']) ?>" method="POST">
    <?= csrfField() ?>

    <section class="jm-form-section">
        <div class="jm-form-head">
            <span class="jm-step"><i class="bi bi-diagram-3"></i></span>
            <div><h2>Hubungan dengan kepala baru</h2><p>Semua hubungan dihitung ulang terhadap <?= e($member['full_name']) ?>.</p></div>
        </div>
        <div class="jm-form-body">
            <?php jmHeadPicker($others, (int) $member['id'], null, $row['relationship']); ?>
            <div class="row g-3 mt-1">
                <div class="col-md-4">
                    <label class="form-label" for="date">Tanggal berlaku</label>
                    <input type="date" class="form-control" id="date" name="date" value="<?= e(old('date', date('Y-m-d'))) ?>">
                </div>
            </div>
        </div>
    </section>

    <?php jmActionBar(url('admin/jemaat/detail/' . $row['family_id']), 'Jadikan kepala', 'star', '', 'btn-primary', true); ?>
</form>
<?php jmActionEnd(); ?>
<?php jmScripts(); ?>
