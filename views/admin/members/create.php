<?php
require_once __DIR__ . '/_helpers.php';

$oldHead = old('head', []);
$oldMembers = array_values(array_filter((array) old('members', []), 'is_array'));

/**
 * Kartu satu anggota; $index '__I__' dipakai untuk template JS
 */
$memberCard = function ($index, array $values) {
    ?>
    <div class="jm-member-card" data-member-card>
        <div class="jm-member-card-head">
            <span class="jm-member-card-num" data-member-num></span>
            <select class="form-select form-select-sm" name="members[<?= $index ?>][relationship]" data-relationship required aria-label="Hubungan dengan kepala keluarga">
                <?= jmRelationshipOptions($values['relationship'] ?? 'child') ?>
            </select>
            <span class="small text-muted d-none d-md-inline">dari kepala keluarga</span>
            <button type="button" class="btn btn-light btn-icon ms-auto" data-remove-member aria-label="Hapus anggota ini">
                <i class="bi bi-trash3"></i>
            </button>
        </div>
        <div class="jm-member-card-body">
            <?php jmPersonFields("members[{$index}]", $values, ['idPrefix' => "m{$index}"]); ?>
        </div>
    </div>
    <?php
};
?>
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= url('admin/jemaat') ?>" class="btn btn-light btn-icon flex-shrink-0" aria-label="Kembali ke Data Jemaat"><i class="bi bi-arrow-left"></i></a>
    <div class="min-w-0">
        <h1 class="h3 fw-bold mb-0">Tambah Keluarga</h1>
        <p class="text-muted mb-0 small">Isi kepala keluarga, lalu anggotanya — semua tersimpan sekaligus sebagai satu KK.</p>
    </div>
</div>

<form action="<?= url('admin/jemaat/simpan') ?>" method="POST" id="familyForm">
    <?= csrfField() ?>

    <section class="jm-form-section">
        <div class="jm-form-head">
            <span class="jm-step">1</span>
            <div><h2>Kepala keluarga</h2><p>Jemaat yang tinggal sendiri tetap punya KK sendiri dan menjadi kepalanya.</p></div>
        </div>
        <div class="jm-form-body" id="headFields">
            <?php jmPersonFields('head', $oldHead); ?>
        </div>
    </section>

    <section class="jm-form-section">
        <div class="jm-form-head">
            <span class="jm-step">2</span>
            <div><h2>Anggota keluarga</h2><p>Lewati bila kepala keluarga tinggal sendiri.</p></div>
        </div>
        <div class="jm-form-body">
            <div id="memberList">
                <?php foreach ($oldMembers as $i => $values): ?>
                    <?php $memberCard($i, $values); ?>
                <?php endforeach; ?>
            </div>
            <div class="jm-add-tiles <?= $oldMembers ? 'mt-3' : '' ?>" id="addTiles">
                <button type="button" class="jm-add-tile" data-add-member="spouse"><i class="bi bi-heart"></i>Suami/Istri</button>
                <button type="button" class="jm-add-tile" data-add-member="child"><i class="bi bi-person-plus"></i>Anak</button>
                <button type="button" class="jm-add-tile" data-add-member="other"><i class="bi bi-people"></i>Anggota lain</button>
            </div>
        </div>
    </section>

    <section class="jm-form-section">
        <div class="jm-form-head">
            <span class="jm-step">3</span>
            <div><h2>Alamat &amp; kontak KK</h2><p>No. KK dibuat otomatis setelah disimpan.</p></div>
        </div>
        <div class="jm-form-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label" for="address">Alamat rumah</label>
                    <textarea class="form-control" id="address" name="address" rows="2" placeholder="Jalan, RT/RW, kelurahan"><?= e(old('address')) ?></textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="phone">Telepon rumah</label>
                    <input type="tel" class="form-control" id="phone" name="phone" value="<?= e(old('phone')) ?>" maxlength="20" inputmode="tel">
                </div>
                <div class="col-12">
                    <label class="form-label" for="family_notes">Catatan KK</label>
                    <input type="text" class="form-control" id="family_notes" name="family_notes" value="<?= e(old('family_notes')) ?>" placeholder="Opsional, mis. wilayah komsel">
                </div>
            </div>
        </div>
    </section>

    <?php jmActionBar(url('admin/jemaat'), 'Simpan keluarga', 'check-lg',
        '<i class="bi bi-people me-1"></i><b data-sum-count>1 orang</b> · kondisi: <b data-sum-condition>-</b>'); ?>
</form>

<template id="memberTemplate">
    <?php $memberCard('__I__', []); ?>
</template>

<?php jmScripts(); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const list = document.getElementById('memberList');
    const tiles = document.getElementById('addTiles');
    const template = document.getElementById('memberTemplate').innerHTML;
    const spouseTile = document.querySelector('[data-add-member="spouse"]');
    const head = document.getElementById('headFields');
    let counter = <?= count($oldMembers) ?>;

    const checkRadio = function (scope, group, value) {
        const input = scope.querySelector('[' + group + '] input[value="' + value + '"]');
        if (input) input.checked = true;
    };
    const radioValue = function (scope, group) {
        const input = scope.querySelector('[' + group + '] input:checked');
        return input ? input.value : '';
    };

    // Ringkasan di bilah bawah: jumlah orang & perkiraan kondisi keluarga
    function refresh() {
        const cards = list.querySelectorAll('[data-member-card]');
        cards.forEach(function (card, i) { card.querySelector('[data-member-num]').textContent = i + 1; });
        tiles.classList.toggle('mt-3', cards.length > 0);

        const rels = Array.from(list.querySelectorAll('[data-relationship]')).map(function (s) { return s.value; });
        spouseTile.disabled = rels.indexOf('spouse') !== -1;

        const kids = rels.filter(function (r) { return r === 'child'; }).length;
        const marital = radioValue(head, 'data-marital');
        let condition = 'lengkapi status kepala';
        if (marital === 'widowed') condition = 'Janda/Duda ' + (kids ? 'dengan' : 'tanpa') + ' anak';
        else if (marital === 'married') condition = kids ? 'Menikah, punya anak' : 'Menikah, belum punya anak';
        else if (marital === 'single') condition = cards.length ? 'Lainnya' : 'Single';

        document.querySelector('[data-sum-count]').textContent = (cards.length + 1) + ' orang';
        document.querySelector('[data-sum-condition]').textContent = condition;
    }

    // Suami/Istri: keduanya Menikah, jenis kelamin kebalikan kepala
    function onSpouse(card) {
        checkRadio(card, 'data-marital', 'married');
        checkRadio(head, 'data-marital', 'married');
        const headGender = radioValue(head, 'data-gender');
        if (headGender && !radioValue(card, 'data-gender')) checkRadio(card, 'data-gender', headGender === 'M' ? 'F' : 'M');
        window.jmSyncWidow(card.querySelector('[data-person]'));
    }

    function setupCard(card) {
        card.querySelector('[data-remove-member]').addEventListener('click', function () {
            card.remove();
            refresh();
        });
        card.querySelector('[data-relationship]').addEventListener('change', function () {
            if (this.value === 'spouse') onSpouse(card);
            refresh();
        });
    }

    document.querySelectorAll('[data-add-member]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const holder = document.createElement('div');
            holder.innerHTML = template.replace(/__I__/g, counter++).trim();
            const card = holder.firstElementChild;
            const type = btn.dataset.addMember;
            card.querySelector('[data-relationship]').value = type;
            if (type === 'child') checkRadio(card, 'data-marital', 'single');
            list.appendChild(card);
            setupCard(card);
            if (type === 'spouse') onSpouse(card);
            refresh();
            card.querySelector('input[type="text"]').focus();
            card.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });
    });

    list.querySelectorAll('[data-member-card]').forEach(setupCard);
    document.getElementById('familyForm').addEventListener('change', refresh);
    refresh();
});
</script>
