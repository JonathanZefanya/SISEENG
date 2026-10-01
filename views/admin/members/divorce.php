<?php
use App\Models\Member;

require_once __DIR__ . '/_helpers.php';

$defaultLeaver = '';
if ($spouse && $spouseInSameFamily) {
    // Default: yang bukan kepala keluarga yang keluar
    $defaultLeaver = $row['relationship'] === 'head' ? (string) $spouse['id'] : (string) $member['id'];
}
$leaver = old('leaver', $defaultLeaver);
$target = old('target', 'new');
$oldChildren = array_map('intval', (array) old('children', []));

jmActionStart('Catat perceraian', 'Status pernikahan berubah dan penyebabnya tercatat di riwayat.', $member, $row, $family);
?>
<form action="<?= url('admin/jemaat/anggota/cerai/' . $member['id']) ?>" method="POST">
    <?= csrfField() ?>

    <div class="jm-callout t-pink mb-3">
        <i class="bi bi-heartbreak"></i>
        <div>
            <?= e($member['full_name']) ?> menjadi <strong><?= e(Member::maritalLabel('widowed', $member['gender'])) ?></strong><?php if ($spouse): ?>
            dan <?= e($spouse['full_name']) ?> menjadi <strong><?= e(Member::maritalLabel('widowed', $spouse['gender'])) ?></strong><?php endif; ?>,
            dengan penyebab "Cerai".
        </div>
    </div>

    <section class="jm-form-section">
        <div class="jm-form-head"><span class="jm-step">1</span><div><h2>Tanggal &amp; keterangan</h2></div></div>
        <div class="jm-form-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label" for="date">Tanggal cerai</label>
                    <input type="date" class="form-control" id="date" name="date" value="<?= e(old('date', date('Y-m-d'))) ?>">
                </div>
                <div class="col-md-8">
                    <label class="form-label" for="note">Keterangan</label>
                    <input type="text" class="form-control" id="note" name="note" value="<?= e(old('note')) ?>">
                </div>
            </div>
        </div>
    </section>

    <section class="jm-form-section">
        <div class="jm-form-head"><span class="jm-step">2</span><div><h2>Siapa yang keluar dari KK?</h2></div></div>
        <div class="jm-form-body">
            <div class="jm-options">
                <?= jmOption('leaver', '', $leaver === '', 'house-check', 'Tidak ada yang pindah', 'Hanya ubah status pernikahan (mis. sudah tinggal terpisah).') ?>
                <?= jmOption('leaver', (string) $member['id'], $leaver === (string) $member['id'], 'person-dash', $member['full_name'],
                    $row['relationship'] === 'head' && $spouseInSameFamily ? 'Kepala keluarga; pasangannya otomatis menjadi kepala KK ini.' : 'Keluar dari ' . e($family['family_code']) . '.') ?>
                <?php if ($spouse && $spouseInSameFamily): ?>
                    <?= jmOption('leaver', (string) $spouse['id'], $leaver === (string) $spouse['id'], 'person-dash', $spouse['full_name'],
                        $row['relationship'] !== 'head' ? 'Kepala keluarga; ' . e($member['full_name']) . ' otomatis menjadi kepala KK ini.' : 'Keluar dari ' . e($family['family_code']) . '.') ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="jm-form-section" data-show-for="leaver=<?= (int) $member['id'] ?><?= $spouse ? ',' . (int) $spouse['id'] : '' ?>">
        <div class="jm-form-head"><span class="jm-step">3</span><div><h2>Pindah ke mana?</h2></div></div>
        <div class="jm-form-body">
            <div class="jm-options cols-2 mb-3">
                <?= jmOption('target', 'new', $target === 'new', 'house-add', 'KK baru', 'Menjadi kepala KK baru; KK asal tercatat.') ?>
                <?= jmOption('target', 'existing', $target === 'existing', 'house-heart', 'KK yang sudah ada', 'Mis. kembali ke KK orang tuanya.') ?>
            </div>
            <div data-show-for="target=new">
                <label class="form-label" for="new_address">Alamat KK baru</label>
                <input type="text" class="form-control" id="new_address" name="new_address" value="<?= e(old('new_address')) ?>">
            </div>
            <div class="row g-3" data-show-for="target=existing">
                <div class="col-md-8">
                    <label class="form-label">KK tujuan</label>
                    <?php jmPicker('target_family_id', 'family', (int) $row['family_id'], 'Cari no. KK atau nama kepala keluarga...'); ?>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="relationship">Hubungan di KK tujuan</label>
                    <select class="form-select" id="relationship" name="relationship">
                        <?= jmRelationshipOptions(old('relationship', 'child')) ?>
                    </select>
                </div>
            </div>

            <?php if ($children): ?>
                <div class="mt-4">
                    <div class="jm-sheet-title">Anak yang ikut pindah</div>
                    <div class="jm-pills">
                        <?php foreach ($children as $child): ?>
                            <input type="checkbox" class="btn-check" name="children[]" value="<?= (int) $child['id'] ?>" id="child<?= (int) $child['id'] ?>"
                                   <?= in_array((int) $child['id'], $oldChildren, true) ? 'checked' : '' ?>>
                            <label class="jm-pill" for="child<?= (int) $child['id'] ?>"><i class="bi bi-person"></i><?= e($child['full_name']) ?> · <?= jmAge($child['birth_date']) ?></label>
                        <?php endforeach; ?>
                    </div>
                    <div class="form-text">Anak yang tidak dipilih tetap di <?= e($family['family_code']) ?>.</div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php jmActionBar(url('admin/jemaat/detail/' . $row['family_id']), 'Catat perceraian', 'heartbreak', '', 'btn-danger', true); ?>
</form>
<?php jmActionEnd(); ?>
<?php jmScripts(); ?>
