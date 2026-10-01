<?php
/**
 * Komponen tampilan modul Data Jemaat (dipakai bersama oleh view di folder ini)
 */

use App\Models\Member;
use App\Models\Family;
use App\Models\FamilyMember;

// ===================== Tag & avatar =====================

function jmTag(string $label, string $tone, ?string $icon = null): string
{
    return '<span class="jm-tag ' . $tone . '">' . ($icon ? '<i class="bi bi-' . $icon . '"></i>' : '') . e($label) . '</span>';
}

function jmStatusTag(string $status): string
{
    [$tone, $icon] = Member::STATUS_TONES[$status] ?? ['t-grey', null];
    return jmTag(Member::statusLabel($status), $tone, $icon);
}

function jmConditionTag(string $condition): string
{
    [$tone, $icon] = Family::CONDITION_TONES[$condition] ?? ['t-grey', null];
    return jmTag(Family::CONDITIONS[$condition] ?? $condition, $tone, $icon);
}

function jmFlagTags(array $family): string
{
    $html = '';
    if ((int) $family['adult_child_count'] > 0 && (int) $family['active_count'] > 0) {
        $html .= jmTag(Family::FLAGS['adult_child'], 't-outline', 'person-check');
    }
    if ((int) $family['married_child_count'] > 0) {
        $html .= jmTag(Family::FLAGS['married_child'], 't-outline', 'diagram-3');
    }
    return $html;
}

function jmAge(?string $birthDate): string
{
    $age = Member::age($birthDate);
    return $age === null ? '-' : $age . ' th';
}

function jmInitials(?string $name): string
{
    $parts = preg_split('/\s+/', trim((string) $name)) ?: [];
    $initials = '';
    foreach (array_slice($parts, 0, 2) as $part) {
        $initials .= mb_strtoupper(mb_substr($part, 0, 1));
    }
    return $initials ?: '?';
}

function jmAvatar(?string $name, ?string $gender, string $size = '', bool $off = false): string
{
    $class = 'jm-avatar' . ($size ? ' jm-avatar-' . $size : '') . ($gender === 'F' ? ' is-f' : '') . ($off ? ' is-off' : '');
    return '<span class="' . $class . '" aria-hidden="true">' . e(jmInitials($name)) . '</span>';
}

/**
 * <option> hubungan keluarga
 */
function jmRelationshipOptions(?string $selected, array $exclude = ['head']): string
{
    $html = '';
    foreach (FamilyMember::RELATIONSHIPS as $value => $label) {
        if (in_array($value, $exclude, true)) {
            continue;
        }
        $html .= '<option value="' . $value . '"' . ($selected === $value ? ' selected' : '') . '>' . e($label) . '</option>';
    }
    return $html;
}

// ===================== Form =====================

/**
 * Segmented control (radio) — dipakai untuk jenis kelamin & status pernikahan
 */
function jmSegment(string $name, array $options, ?string $selected, string $idBase, bool $required = true, string $attrs = ''): string
{
    $html = '<div class="jm-segment" role="radiogroup" ' . $attrs . '>';
    foreach ($options as $value => $label) {
        $id = $idBase . '_' . $value;
        $html .= '<input type="radio" class="btn-check" name="' . e($name) . '" id="' . e($id) . '" value="' . e($value) . '"'
            . ($selected === $value ? ' checked' : '') . ($required ? ' required' : '') . '>'
            . '<label class="jm-seg" for="' . e($id) . '"' . ($value === 'widowed' ? ' data-widow-label' : '') . '>' . e($label) . '</label>';
    }
    return $html . '</div>';
}

/**
 * Field data pribadi: data inti selalu tampil, sisanya di "Data lengkap"
 *
 * @param string $prefix Nama array input, mis. "head" → head[full_name]; '' = tanpa prefix
 * @param array $v Nilai awal
 * @param array $opts marital (bool), maritalLocked (string), idPrefix, expanded (bool)
 */
function jmPersonFields(string $prefix, array $v, array $opts = []): void
{
    $name = fn($field) => $prefix === '' ? $field : "{$prefix}[{$field}]";
    $idBase = $opts['idPrefix'] ?? ($prefix !== '' ? preg_replace('/[^a-z0-9]+/i', '_', $prefix) : 'p');
    $id = fn($field) => $idBase . '_' . $field;
    $val = fn($field) => e($v[$field] ?? '');
    $showMarital = $opts['marital'] ?? true;

    $extra = ['birth_place', 'phone', 'email', 'baptism_date', 'membership_date', 'notes'];
    $expanded = !empty($opts['expanded']) || (bool) array_filter(array_intersect_key($v, array_flip($extra)), fn($x) => $x !== null && $x !== '');
    $gender = $v['gender'] ?? null;
    ?>
    <div class="row g-3" data-person>
        <div class="col-xl-6">
            <label class="form-label" for="<?= $id('full_name') ?>">Nama lengkap <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="<?= $id('full_name') ?>" name="<?= $name('full_name') ?>" value="<?= $val('full_name') ?>"
                   maxlength="100" required autocomplete="off" placeholder="Sesuai KTP/akta">
        </div>
        <div class="col-sm-6 col-xl-3">
            <span class="form-label d-block" id="<?= $id('gender') ?>_label">Jenis kelamin <span class="text-danger">*</span></span>
            <?= jmSegment($name('gender'), ['M' => 'Laki-laki', 'F' => 'Perempuan'], $gender, $id('gender'), true,
                'aria-labelledby="' . $id('gender') . '_label" data-gender') ?>
        </div>
        <div class="col-sm-6 col-xl-3">
            <label class="form-label" for="<?= $id('birth_date') ?>">Tanggal lahir</label>
            <input type="date" class="form-control" id="<?= $id('birth_date') ?>" name="<?= $name('birth_date') ?>" value="<?= $val('birth_date') ?>" max="<?= date('Y-m-d') ?>">
        </div>
        <?php if ($showMarital): ?>
            <div class="col-12">
                <span class="form-label d-block" id="<?= $id('marital') ?>_label">Status pernikahan <span class="text-danger">*</span></span>
                <?php if (!empty($opts['maritalLocked'])): ?>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <?= jmTag($opts['maritalLocked'], 't-grey', 'lock') ?>
                        <small class="text-muted">Diubah lewat menu Ubah status pernikahan / Catat pernikahan / Catat perceraian.</small>
                    </div>
                <?php else: ?>
                    <?= jmSegment($name('marital_status'), Member::maritalOptions($gender), $v['marital_status'] ?? null, $id('marital'), true,
                        'aria-labelledby="' . $id('marital') . '_label" data-marital') ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <div class="col-12">
            <button type="button" class="jm-more-toggle <?= $expanded ? '' : 'collapsed' ?>" data-bs-toggle="collapse"
                    data-bs-target="#<?= $id('more') ?>" aria-expanded="<?= $expanded ? 'true' : 'false' ?>" aria-controls="<?= $id('more') ?>">
                <i class="bi bi-chevron-down"></i>Data lengkap <span class="fw-normal text-muted">(kontak, baptis, catatan)</span>
            </button>
            <div class="collapse <?= $expanded ? 'show' : '' ?>" id="<?= $id('more') ?>">
                <div class="row g-3 pt-2">
                    <div class="col-sm-6 col-md-4">
                        <label class="form-label" for="<?= $id('birth_place') ?>">Tempat lahir</label>
                        <input type="text" class="form-control" id="<?= $id('birth_place') ?>" name="<?= $name('birth_place') ?>" value="<?= $val('birth_place') ?>" maxlength="100">
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <label class="form-label" for="<?= $id('phone') ?>">No. telepon / WA</label>
                        <input type="tel" class="form-control" id="<?= $id('phone') ?>" name="<?= $name('phone') ?>" value="<?= $val('phone') ?>" maxlength="20" placeholder="08xxxxxxxxxx" inputmode="tel">
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <label class="form-label" for="<?= $id('email') ?>">Email</label>
                        <input type="email" class="form-control" id="<?= $id('email') ?>" name="<?= $name('email') ?>" value="<?= $val('email') ?>" maxlength="100">
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <label class="form-label" for="<?= $id('baptism_date') ?>">Tanggal baptis</label>
                        <input type="date" class="form-control" id="<?= $id('baptism_date') ?>" name="<?= $name('baptism_date') ?>" value="<?= $val('baptism_date') ?>">
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <label class="form-label" for="<?= $id('membership_date') ?>">Tanggal bergabung</label>
                        <input type="date" class="form-control" id="<?= $id('membership_date') ?>" name="<?= $name('membership_date') ?>" value="<?= $val('membership_date') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="<?= $id('notes') ?>">Catatan</label>
                        <input type="text" class="form-control" id="<?= $id('notes') ?>" name="<?= $name('notes') ?>" value="<?= $val('notes') ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Kartu pilihan (radio besar dengan ikon)
 */
function jmOption(string $name, string $value, bool $checked, string $icon, string $title, string $desc = '', string $attrs = ''): string
{
    $id = 'opt_' . preg_replace('/[^a-z0-9]+/i', '_', $name . '_' . $value);
    return '<label class="jm-option" for="' . $id . '">'
        . '<input type="radio" name="' . e($name) . '" id="' . $id . '" value="' . e($value) . '"' . ($checked ? ' checked' : '') . ' ' . $attrs . '>'
        . '<span class="jm-option-body">'
        . '<span class="jm-option-icon"><i class="bi bi-' . $icon . '"></i></span>'
        . '<span class="jm-option-text"><span class="jm-option-title d-block">' . e($title) . '</span>'
        . ($desc !== '' ? '<span class="jm-option-desc d-block">' . $desc . '</span>' : '')
        . '</span><span class="jm-option-check"><i class="bi bi-check-lg"></i></span>'
        . '</span></label>';
}

/**
 * Kolom cari jemaat / KK (hasil dari admin/jemaat/cari)
 *
 * @param string $type 'member' | 'family'
 */
function jmPicker(string $name, string $type, ?int $exclude, string $placeholder): void
{
    ?>
    <div class="jm-picker" data-picker="<?= $type ?>" data-exclude="<?= (int) $exclude ?>">
        <input type="hidden" name="<?= e($name) ?>" value="" data-picker-value>
        <div class="jm-search" data-picker-search>
            <i class="bi bi-search"></i>
            <input type="text" class="form-control" placeholder="<?= e($placeholder) ?>" autocomplete="off" data-picker-input
                   aria-label="<?= e($placeholder) ?>">
        </div>
        <div class="jm-picker-results" hidden data-picker-results></div>
        <div class="jm-picker-selected" hidden data-picker-selected>
            <span class="jm-avatar jm-avatar-sm" data-picker-avatar></span>
            <span class="flex-grow-1 min-w-0">
                <span class="fw-bold d-block text-truncate" data-picker-label></span>
                <small class="text-muted d-block text-truncate" data-picker-meta></small>
            </span>
            <button type="button" class="btn btn-sm btn-light" data-picker-reset>Ganti</button>
        </div>
    </div>
    <?php
}

/**
 * Pemilih kepala keluarga baru + penyesuaian hubungan anggota
 *
 * @param array $others anggota aktif lain (dari FamilyMember::activeMembers)
 * @param int|null $fixedHeadId bila kepala baru sudah ditentukan (menu Jadikan Kepala)
 * @param int|null $suggested usulan kepala baru
 * @param string $fixedRel hubungan kepala baru (tetap) terhadap kepala lama
 */
function jmHeadPicker(array $others, ?int $fixedHeadId = null, ?int $suggested = null, string $fixedRel = ''): void
{
    $oldRel = fn($m) => (old('rel') ?: [])[$m['id']] ?? null;
    $selectedHead = (int) (old('new_head_id') ?: $suggested);
    ?>
    <div class="jm-heads" data-head-picker <?php if ($fixedHeadId): ?>data-fixed-head="<?= $fixedHeadId ?>" data-fixed-rel="<?= e($fixedRel) ?>"<?php endif; ?>>
        <?php foreach ($others as $m): ?>
            <div class="jm-head-row" data-member-row data-id="<?= (int) $m['id'] ?>" data-rel="<?= e($m['relationship']) ?>" data-spouse="<?= (int) $m['spouse_id'] ?>">
                <?php if (!$fixedHeadId): ?>
                    <input class="form-check-input" type="radio" name="new_head_id" value="<?= (int) $m['id'] ?>" id="head_<?= (int) $m['id'] ?>"
                           <?= $selectedHead === (int) $m['id'] ? 'checked' : '' ?> required>
                <?php endif; ?>
                <?= jmAvatar($m['full_name'], $m['gender'], 'sm') ?>
                <label class="jm-head-who" <?= $fixedHeadId ? '' : 'for="head_' . (int) $m['id'] . '"' ?>>
                    <span class="fw-bold d-block"><?= e($m['full_name']) ?></span>
                    <small class="text-muted">Sekarang: <?= e(FamilyMember::relationshipLabel($m['relationship'], $m['gender'])) ?> · <?= jmAge($m['birth_date']) ?></small>
                </label>
                <span class="jm-tag t-green" data-head-label hidden><i class="bi bi-star-fill"></i>Kepala keluarga baru</span>
                <select class="form-select form-select-sm" name="rel[<?= (int) $m['id'] ?>]" data-rel-select <?= $oldRel($m) ? 'data-keep' : '' ?>
                        aria-label="Hubungan <?= e($m['full_name']) ?> dengan kepala baru">
                    <?= jmRelationshipOptions($oldRel($m) ?? ($m['relationship'] === 'head' ? 'other' : $m['relationship'])) ?>
                </select>
            </div>
        <?php endforeach; ?>
        <p class="form-text mb-0">
            <i class="bi bi-magic me-1"></i>Hubungan terisi otomatis sesuai kepala baru (istri jadi kepala → anak tetap Anak; anak jadi kepala → ibu jadi Orang Tua). Periksa sebelum menyimpan.
        </p>
    </div>
    <?php
}

// ===================== Layout halaman aksi =====================

/**
 * Buka halaman aksi: judul + kartu orang (kiri) + kolom form (kanan). Tutup dengan jmActionEnd().
 */
function jmActionStart(string $title, string $subtitle, array $member, array $row, $family = null): void
{
    $familyId = (int) $row['family_id'];
    $back = url('admin/jemaat/detail/' . $familyId);
    ?>
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="<?= $back ?>" class="btn btn-light btn-icon flex-shrink-0" aria-label="Kembali ke detail KK"><i class="bi bi-arrow-left"></i></a>
        <div class="min-w-0">
            <h1 class="h3 fw-bold mb-0"><?= e($title) ?></h1>
            <p class="text-muted mb-0 small"><?= e($subtitle) ?></p>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-lg-4 col-xl-3">
            <div class="jm-subject">
                <div class="jm-subject-card">
                    <?= jmAvatar($member['full_name'], $member['gender'], 'lg', $member['status'] !== 'active') ?>
                    <div class="min-w-0 flex-grow-1">
                        <div class="jm-subject-name"><?= e($member['full_name']) ?></div>
                        <div class="jm-subject-meta">
                            <?= e(FamilyMember::relationshipLabel($row['relationship'], $member['gender'])) ?> · <?= jmAge($member['birth_date']) ?>
                        </div>
                        <div class="jm-subject-tags">
                            <?= jmStatusTag($member['status']) ?>
                            <?= jmTag(Member::maritalLabel($member['marital_status'], $member['gender']), $member['marital_status'] ? 't-grey' : 't-yellow') ?>
                        </div>
                    </div>
                    <?php if ($family): ?>
                        <a href="<?= $back ?>" class="jm-subject-family">
                            <i class="bi bi-house-heart fs-5 text-primary"></i>
                            <span class="min-w-0">
                                <span class="fw-bold d-block text-dark"><?= e($family['family_code']) ?></span>
                                <span class="d-block text-truncate">Kepala: <?= e($family['head_name'] ?: '-') ?></span>
                            </span>
                            <i class="bi bi-chevron-right ms-auto"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-lg-8 col-xl-9">
    <?php
}

function jmActionEnd(): void
{
    echo '</div></div>';
}

/**
 * Bilah aksi lengket di bawah form
 */
function jmActionBar(string $cancelUrl, string $submitLabel, string $icon = 'check-lg', string $info = '', string $btnClass = 'btn-primary', bool $floating = false): void
{
    ?>
    <div class="jm-actionbar <?= $floating ? 'is-floating' : '' ?>">
        <div class="jm-actionbar-info"><?= $info ?></div>
        <a href="<?= $cancelUrl ?>" class="btn btn-light">Batal</a>
        <button type="submit" class="btn <?= $btnClass ?>"><i class="bi bi-<?= $icon ?> me-2"></i><?= e($submitLabel) ?></button>
    </div>
    <?php
}

// ===================== Script bersama =====================

/**
 * Script bersama: pemilih jemaat/KK, pemilih kepala baru, label Janda/Duda (cukup sekali per halaman)
 */
function jmScripts(): void
{
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const esc = function (s) { const d = document.createElement('div'); d.textContent = s == null ? '' : s; return d.innerHTML; };
        const initials = function (name) {
            return (name || '?').trim().split(/\s+/).slice(0, 2).map(function (p) { return p.charAt(0).toUpperCase(); }).join('');
        };

        // ===== Label Janda/Duda mengikuti jenis kelamin (berlaku juga untuk kartu yang ditambah via JS) =====
        function syncWidow(person) {
            const g = person.querySelector('[data-gender] input:checked');
            person.querySelectorAll('[data-widow-label]').forEach(function (label) {
                label.textContent = g ? (g.value === 'F' ? 'Janda' : 'Duda') : 'Janda/Duda';
            });
        }
        document.addEventListener('change', function (e) {
            const person = e.target.closest('[data-person]');
            if (person && e.target.closest('[data-gender]')) syncWidow(person);
        });
        document.querySelectorAll('[data-person]').forEach(syncWidow);
        window.jmSyncWidow = syncWidow;

        // ===== Pencarian jemaat / KK =====
        document.querySelectorAll('[data-picker]').forEach(function (picker) {
            const input = picker.querySelector('[data-picker-input]');
            const hidden = picker.querySelector('[data-picker-value]');
            const results = picker.querySelector('[data-picker-results]');
            const search = picker.querySelector('[data-picker-search]');
            const selected = picker.querySelector('[data-picker-selected]');
            let timer = null;

            function choose(item) {
                hidden.value = item ? item.id : '';
                search.hidden = !!item;
                selected.hidden = !item;
                results.hidden = true;
                if (item) {
                    selected.querySelector('[data-picker-label]').textContent = item.label;
                    selected.querySelector('[data-picker-meta]').textContent = item.meta || '';
                    const av = selected.querySelector('[data-picker-avatar]');
                    av.textContent = picker.dataset.picker === 'family' ? 'KK' : initials(item.label);
                    av.classList.toggle('is-f', item.gender === 'F');
                } else {
                    input.value = '';
                    input.focus();
                }
                picker.dispatchEvent(new CustomEvent('picker:change', { detail: item }));
            }

            picker.querySelector('[data-picker-reset]').addEventListener('click', function () { choose(null); });

            input.addEventListener('input', function () {
                clearTimeout(timer);
                const q = input.value.trim();
                if (q.length < 2) { results.hidden = true; return; }

                timer = setTimeout(function () {
                    const url = '<?= url('admin/jemaat/cari') ?>?type=' + picker.dataset.picker
                        + '&exclude=' + picker.dataset.exclude + '&q=' + encodeURIComponent(q);
                    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(function (r) { return r.json(); })
                        .then(function (data) {
                            results.innerHTML = data.results.length ? '' : '<div class="jm-picker-empty">Tidak ditemukan. Pastikan ejaan nama benar.</div>';
                            data.results.forEach(function (item) {
                                const btn = document.createElement('button');
                                btn.type = 'button';
                                btn.className = 'jm-picker-item';
                                const isFamily = picker.dataset.picker === 'family';
                                btn.innerHTML = '<span class="jm-avatar jm-avatar-sm' + (item.gender === 'F' ? ' is-f' : '') + '">'
                                    + (isFamily ? 'KK' : esc(initials(item.label))) + '</span>'
                                    + '<span class="min-w-0"><span class="fw-bold d-block text-truncate">' + esc(item.label) + '</span>'
                                    + '<small class="text-muted d-block text-truncate">' + esc(item.meta || '') + '</small></span>';
                                btn.addEventListener('click', function () { choose(item); });
                                results.appendChild(btn);
                            });
                            results.hidden = false;
                        });
                }, 250);
            });

            document.addEventListener('click', function (e) {
                if (!picker.contains(e.target)) results.hidden = true;
            });
        });

        // ===== Pemilih kepala baru: usulan hubungan relatif terhadap kepala baru =====
        // Kunci luar = hubungan kepala baru terhadap kepala lama; kunci dalam = hubungan anggota terhadap kepala lama
        const REMAP = {
            spouse:  { head: 'spouse', child: 'child', child_in_law: 'child_in_law', grandchild: 'grandchild', parent: 'parent_in_law', parent_in_law: 'parent' },
            child:   { head: 'parent', spouse: 'parent', child: 'sibling' },
            sibling: { head: 'sibling', parent: 'parent', sibling: 'sibling' },
            parent:  { head: 'child', spouse: 'child_in_law', child: 'grandchild', sibling: 'child' }
        };

        document.querySelectorAll('[data-head-picker]').forEach(function (box) {
            const rows = Array.from(box.querySelectorAll('[data-member-row]'));

            function apply(headId, headRel, keepManual) {
                rows.forEach(function (row) {
                    const select = row.querySelector('[data-rel-select]');
                    const label = row.querySelector('[data-head-label]');
                    const isHead = row.dataset.id === String(headId);
                    row.classList.toggle('is-new-head', isHead);
                    select.hidden = isHead;
                    select.disabled = isHead;
                    label.hidden = !isHead;
                    if (isHead || (keepManual && select.hasAttribute('data-keep'))) return;

                    let rel = (REMAP[headRel] || {})[row.dataset.rel] || 'other';
                    if (row.dataset.spouse === String(headId)) rel = 'spouse';
                    select.value = rel;
                });
            }

            if (box.dataset.fixedHead) {
                apply(box.dataset.fixedHead, box.dataset.fixedRel, true);
                return;
            }
            box.querySelectorAll('input[name="new_head_id"]').forEach(function (radio) {
                radio.addEventListener('change', function () {
                    apply(radio.value, radio.closest('[data-member-row]').dataset.rel, false);
                });
            });
            const checked = box.querySelector('input[name="new_head_id"]:checked');
            if (checked) apply(checked.value, checked.closest('[data-member-row]').dataset.rel, true);
        });

        // ===== Panel yang tampil sesuai pilihan radio: data-show-for="nama=nilai[,nilai]" =====
        const toggles = document.querySelectorAll('[data-show-for]');
        function syncPanels() {
            toggles.forEach(function (panel) {
                const [name, values] = panel.dataset.showFor.split('=');
                const checked = document.querySelector('[name="' + name + '"]:checked') || document.querySelector('select[name="' + name + '"]');
                const parent = panel.parentElement.closest('[data-show-for]');
                const on = !!checked && values.split(',').indexOf(checked.value) !== -1 && !(parent && parent.hidden);
                panel.hidden = !on;
                panel.querySelectorAll('input, select, textarea').forEach(function (el) {
                    if (!el.closest('[data-show-for]') || el.closest('[data-show-for]') === panel) el.disabled = !on;
                });
            });
        }
        if (toggles.length) {
            document.addEventListener('change', function (e) { if (e.target.name) syncPanels(); });
            syncPanels();
        }
    });
    </script>
    <?php
}
