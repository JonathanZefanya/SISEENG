<?php
use App\Models\Member;

require_once __DIR__ . '/_helpers.php';

$isActive = $member['status'] === 'active';
$selected = old('status', $isActive ? '' : 'active');
$descriptions = [
    'active' => 'Masuk kembali ke KK dan dihitung dalam total jemaat.',
    'inactive' => 'Masih tercatat, tapi tidak lagi beribadah/aktif.',
    'deceased' => 'Keluar dari KK sebagai arsip. Pasangan otomatis Janda/Duda.',
    'moved_church' => 'Pindah keanggotaan ke gereja lain.',
    'moved_religion' => 'Tidak lagi beragama Kristen.',
];

jmActionStart('Ubah status jemaat', 'Data tidak dihapus — perubahan tercatat di riwayat.', $member, $row, $family);
?>
<form action="<?= url('admin/jemaat/anggota/status/' . $member['id']) ?>" method="POST">
    <?= csrfField() ?>

    <section class="jm-form-section">
        <div class="jm-form-head"><span class="jm-step"><i class="bi bi-person-gear"></i></span><div><h2>Status baru</h2><p>Sekarang: <?= e(Member::statusLabel($member['status'])) ?></p></div></div>
        <div class="jm-form-body">
            <div class="jm-options cols-2">
                <?php foreach (Member::STATUSES as $value => $label): ?>
                    <?php if ($value === $member['status']) continue; ?>
                    <?= jmOption('status', $value, $selected === $value, Member::STATUS_TONES[$value][1],
                        $value === 'active' ? 'Aktifkan kembali' : $label, e($descriptions[$value]), 'required') ?>
                <?php endforeach; ?>
            </div>

            <div class="row g-3 mt-2">
                <div class="col-md-4">
                    <label class="form-label" for="date">Tanggal kejadian</label>
                    <input type="date" class="form-control" id="date" name="date" value="<?= e(old('date', date('Y-m-d'))) ?>" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label" for="note">Keterangan</label>
                    <input type="text" class="form-control" id="note" name="note" value="<?= e(old('note')) ?>"
                           placeholder="Mis. nama gereja tujuan, tempat pemakaman">
                </div>
            </div>
        </div>
    </section>

    <?php if ($isActive): ?>
        <?php if ($spouse): ?>
            <div class="jm-callout t-pink mb-3" data-show-for="status=deceased" hidden>
                <i class="bi bi-heartbreak"></i>
                <div><strong><?= e($spouse['full_name']) ?></strong> otomatis tercatat sebagai <strong><?= e(Member::maritalLabel('widowed', $spouse['gender'])) ?></strong> karena pasangan meninggal.</div>
            </div>
        <?php endif; ?>

        <?php if ($headPicker): ?>
            <section class="jm-form-section">
                <div class="jm-form-head"><span class="jm-step"><i class="bi bi-star"></i></span><div><h2>Kepala keluarga pengganti</h2><p><?= e($member['full_name']) ?> adalah kepala keluarga. Pilih penggantinya.</p></div></div>
                <div class="jm-form-body">
                    <?php jmHeadPicker($headPicker['others'], null, $headPicker['suggested']); ?>
                </div>
            </section>
        <?php elseif ($row['relationship'] === 'head'): ?>
            <div class="jm-callout t-grey mb-3">
                <i class="bi bi-archive"></i>
                <div>Tidak ada anggota aktif lain, jadi KK ini akan menjadi <strong>Arsip</strong>.</div>
            </div>
        <?php endif; ?>

        <div class="jm-callout t-blue">
            <i class="bi bi-info-circle"></i>
            <div>Selain Aktif, jemaat <strong>tidak dihitung</strong> dalam total jemaat dan pindah ke tab Arsip pada KK-nya.</div>
        </div>
    <?php else: ?>
        <section class="jm-form-section" data-show-for="status=active">
            <div class="jm-form-head"><span class="jm-step"><i class="bi bi-house-heart"></i></span><div><h2>Masuk ke KK</h2><p>KK terakhir: <?= e($family['family_code'] ?? '-') ?></p></div></div>
            <div class="jm-form-body">
                <div class="jm-options cols-2">
                    <?= jmOption('reactivate_to', 'last', old('reactivate_to', 'last') === 'last', 'arrow-counterclockwise', 'Kembali ke KK terakhir',
                        'Masuk dengan hubungan keluarga sebelumnya.') ?>
                    <?= jmOption('reactivate_to', 'new', old('reactivate_to') === 'new', 'house-add', 'Buat KK baru',
                        'Dia menjadi kepala keluarga di KK baru.') ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php jmActionBar(url('admin/jemaat/detail/' . $row['family_id']), 'Simpan status', 'check-lg', '', 'btn-primary', true); ?>
</form>
<?php jmActionEnd(); ?>
<?php jmScripts(); ?>
