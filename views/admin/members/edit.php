<?php
use App\Models\Member;

require_once __DIR__ . '/_helpers.php';

// Isian lama hanya dipakai bila berasal dari form orang yang sama
$oldInput = \Core\Session::get('old_input');
$values = is_array($oldInput) && (int) ($oldInput['_for'] ?? 0) === (int) $member['id'] ? array_merge($member, $oldInput) : $member;
$maritalLocked = $member['marital_status'] !== null ? Member::maritalLabel($member['marital_status'], $member['gender']) : null;

jmActionStart('Edit data pribadi', 'Status jemaat, pernikahan, dan KK diubah lewat menu masing-masing.', $member, $row, $family);
?>
<form action="<?= url('admin/jemaat/anggota/edit/' . $member['id']) ?>" method="POST">
    <?= csrfField() ?>
    <input type="hidden" name="_for" value="<?= (int) $member['id'] ?>">

    <?php if (!$maritalLocked): ?>
        <div class="jm-callout t-yellow mb-3">
            <i class="bi bi-exclamation-circle"></i>
            <div>Status pernikahan belum diisi (data lama). Setelah diisi, perubahan berikutnya lewat menu pernikahan agar riwayatnya tercatat.</div>
        </div>
    <?php endif; ?>

    <section class="jm-form-section">
        <div class="jm-form-head"><span class="jm-step"><i class="bi bi-person-vcard"></i></span><div><h2>Data pribadi</h2></div></div>
        <div class="jm-form-body">
            <?php jmPersonFields('', $values, ['maritalLocked' => $maritalLocked, 'expanded' => true]); ?>
        </div>
    </section>

    <?php jmActionBar(url('admin/jemaat/detail/' . $row['family_id']), 'Simpan perubahan', 'check-lg', '', 'btn-primary', true); ?>
</form>
<?php jmActionEnd(); ?>
<?php jmScripts(); ?>
