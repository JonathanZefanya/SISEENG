<?php
use App\Models\FamilyMember;

require_once __DIR__ . '/_helpers.php';

$target = old('target', 'existing');

jmActionStart('Pindahkan ke KK lain', 'Keanggotaan di ' . $family['family_code'] . ' ditutup dan tetap tercatat di riwayat.', $member, $row, $family);
?>
<form action="<?= url('admin/jemaat/anggota/pindah/' . $member['id']) ?>" method="POST">
    <?= csrfField() ?>

    <section class="jm-form-section">
        <div class="jm-form-head"><span class="jm-step">1</span><div><h2>Tujuan</h2><p>Ke KK yang sudah ada, atau buat KK baru.</p></div></div>
        <div class="jm-form-body">
            <div class="jm-options cols-2 mb-3">
                <?= jmOption('target', 'existing', $target === 'existing', 'house-heart', 'KK yang sudah ada', 'Mis. ikut saudara atau orang tua.') ?>
                <?= jmOption('target', 'new', $target === 'new', 'house-add', 'Buat KK baru', e($member['full_name']) . ' menjadi kepala keluarga.') ?>
            </div>

            <div class="row g-3" data-show-for="target=existing">
                <div class="col-md-8">
                    <label class="form-label">KK tujuan</label>
                    <?php jmPicker('target_family_id', 'family', (int) $row['family_id'], 'Cari no. KK atau nama kepala keluarga...'); ?>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="relationship">Hubungan di KK tujuan</label>
                    <select class="form-select" id="relationship" name="relationship">
                        <?= jmRelationshipOptions(old('relationship', 'other')) ?>
                    </select>
                </div>
            </div>
            <div data-show-for="target=new">
                <label class="form-label" for="new_address">Alamat KK baru</label>
                <textarea class="form-control" id="new_address" name="new_address" rows="2"><?= e(old('new_address')) ?></textarea>
                <div class="form-text">KK asal tercatat sebagai <?= e($family['family_code']) ?>.</div>
            </div>
        </div>
    </section>

    <section class="jm-form-section">
        <div class="jm-form-head"><span class="jm-step">2</span><div><h2>Alasan &amp; tanggal</h2></div></div>
        <div class="jm-form-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label" for="reason">Alasan</label>
                    <select class="form-select" id="reason" name="reason">
                        <?php foreach (FamilyMember::LEFT_REASONS as $value => $label): ?>
                            <?php if ($value === 'status_change') continue; ?>
                            <option value="<?= $value ?>" <?= old('reason', 'moved_family') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="date">Tanggal</label>
                    <input type="date" class="form-control" id="date" name="date" value="<?= e(old('date', date('Y-m-d'))) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="note">Keterangan</label>
                    <input type="text" class="form-control" id="note" name="note" value="<?= e(old('note')) ?>">
                </div>
            </div>
        </div>
    </section>

    <?php if ($headPicker): ?>
        <section class="jm-form-section">
            <div class="jm-form-head"><span class="jm-step">3</span><div><h2>Kepala pengganti di <?= e($family['family_code']) ?></h2><p><?= e($member['full_name']) ?> adalah kepala keluarga.</p></div></div>
            <div class="jm-form-body">
                <?php jmHeadPicker($headPicker['others'], null, $headPicker['suggested']); ?>
            </div>
        </section>
    <?php endif; ?>

    <?php jmActionBar(url('admin/jemaat/detail/' . $row['family_id']), 'Pindahkan', 'box-arrow-right', '', 'btn-primary', true); ?>
</form>
<?php jmActionEnd(); ?>
<?php jmScripts(); ?>
