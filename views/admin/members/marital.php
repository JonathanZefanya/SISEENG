<?php
use App\Models\Member;

require_once __DIR__ . '/_helpers.php';

jmActionStart('Ubah status pernikahan', 'Untuk koreksi data. Pernikahan baru lewat menu Catat pernikahan.', $member, $row, $family);
?>
<?php if ($spouse): ?>
    <div class="jm-form-section">
        <div class="jm-form-body">
            <div class="jm-callout t-yellow mb-3">
                <i class="bi bi-info-circle"></i>
                <div><?= e($member['full_name']) ?> tercatat menikah dengan <strong><?= e($spouse['full_name']) ?></strong>. Pilih salah satu cara di bawah agar pasangannya ikut tercatat dengan benar.</div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="<?= url('admin/jemaat/anggota/cerai/' . $member['id']) ?>" class="btn btn-outline-primary"><i class="bi bi-heartbreak me-2"></i>Catat perceraian</a>
                <a href="<?= url('admin/jemaat/anggota/status/' . $spouse['id']) ?>" class="btn btn-light"><i class="bi bi-flower1 me-2"></i>Pasangan meninggal</a>
            </div>
        </div>
    </div>
<?php else: ?>
    <form action="<?= url('admin/jemaat/anggota/pernikahan/' . $member['id']) ?>" method="POST">
        <?= csrfField() ?>
        <section class="jm-form-section">
            <div class="jm-form-head"><span class="jm-step"><i class="bi bi-heart"></i></span><div><h2>Status pernikahan</h2><p>Sekarang: <?= e(Member::maritalLabel($member['marital_status'], $member['gender'])) ?></p></div></div>
            <div class="jm-form-body">
                <?= jmSegment('marital_status', Member::maritalOptions($member['gender']), old('marital_status', $member['marital_status']), 'ms') ?>

                <div class="mt-3" data-show-for="marital_status=married" hidden>
                    <label class="form-label">Pasangan (bila juga jemaat)</label>
                    <?php jmPicker('spouse_id', 'member', null, 'Cari nama pasangan...'); ?>
                    <div class="form-text">Kosongkan bila pasangan bukan jemaat. KK tidak berubah; gunakan Catat pernikahan bila perlu pindah KK.</div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-4">
                        <label class="form-label" for="date">Tanggal</label>
                        <input type="date" class="form-control" id="date" name="date" value="<?= e(old('date', date('Y-m-d'))) ?>">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label" for="note">Keterangan</label>
                        <input type="text" class="form-control" id="note" name="note" value="<?= e(old('note')) ?>" placeholder="Mis. koreksi data lama">
                    </div>
                </div>
            </div>
        </section>
        <?php jmActionBar(url('admin/jemaat/detail/' . $row['family_id']), 'Simpan', 'check-lg', '', 'btn-primary', true); ?>
    </form>
<?php endif; ?>
<?php jmActionEnd(); ?>
<?php jmScripts(); ?>
