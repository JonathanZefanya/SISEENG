<?php
use App\Models\Member;
use App\Models\Family;
use App\Models\FamilyMember;

require_once __DIR__ . '/_helpers.php';

// Query filter mentah dari URL (untuk link chip, hapus filter & pagination)
$query = array_filter([
    'search' => $_GET['search'] ?? '',
    'status' => $_GET['status'] ?? '',
    'marital' => $_GET['marital'] ?? '',
    'condition' => $_GET['condition'] ?? '',
    'flag' => $_GET['flag'] ?? '',
], fn($v) => $v !== '');
$link = function (array $overrides = []) use ($query) {
    $params = array_filter(array_merge($query, $overrides), fn($v) => $v !== '' && $v !== null);
    unset($params['page']);
    if (isset($overrides['page']) && $overrides['page'] > 1) {
        $params['page'] = $overrides['page'];
    }
    return url('admin/jemaat') . ($params ? '?' . http_build_query($params) : '');
};

$needle = mb_strtolower(trim($filters['search']));
$matches = function (array $m) use ($needle) {
    return $needle !== '' && (mb_strpos(mb_strtolower($m['full_name']), $needle) !== false
        || ($m['phone'] && mb_strpos(mb_strtolower($m['phone']), $needle) !== false));
};

// Filter aktif (selain pencarian) untuk tag yang bisa dihapus
$activeTags = [];
if (!empty($query['status']) && $query['status'] !== 'active') {
    $activeTags[] = [$query['status'] === 'all' ? 'Semua status' : Member::statusLabel($query['status']), ['status' => null]];
}
if (!empty($query['marital']) && isset(Member::MARITAL_FILTERS[$query['marital']])) {
    $activeTags[] = [Member::MARITAL_FILTERS[$query['marital']], ['marital' => null]];
}
if (!empty($query['condition']) && isset(Family::CONDITIONS[$query['condition']])) {
    $activeTags[] = [Family::CONDITIONS[$query['condition']], ['condition' => null]];
}
if (!empty($query['flag']) && isset(Family::FLAGS[$query['flag']])) {
    $activeTags[] = [Family::FLAGS[$query['flag']], ['flag' => null]];
}
$sheetFilterCount = count($activeTags);
$hasFilter = !empty($query);
?>

<!-- Ringkasan -->
<section class="jm-hero mb-3">
    <div class="d-flex flex-wrap align-items-end justify-content-between gap-3">
        <div>
            <div class="jm-hero-label"><i class="bi bi-people-fill me-1"></i>Total Jemaat Aktif</div>
            <div class="jm-hero-value mt-1"><?= number_format($summary['active']) ?></div>
        </div>
        <a href="<?= url('admin/jemaat/tambah') ?>" class="btn btn-light d-none d-md-inline-flex align-items-center">
            <i class="bi bi-plus-lg me-2"></i>Tambah Keluarga
        </a>
    </div>
    <div class="jm-hero-stats mt-3">
        <div class="jm-hero-stat"><i class="bi bi-house-heart"></i><div><b><?= number_format($totalFamilies) ?></b><small>Kartu Keluarga</small></div></div>
        <div class="jm-hero-stat"><i class="bi bi-gender-male"></i><div><b><?= number_format($summary['male']) ?></b><small>Laki-laki</small></div></div>
        <div class="jm-hero-stat"><i class="bi bi-gender-female"></i><div><b><?= number_format($summary['female']) ?></b><small>Perempuan</small></div></div>
    </div>
</section>

<!-- Cari + filter -->
<div class="jm-toolbar mb-3">
    <form class="jm-search" method="GET" action="<?= url('admin/jemaat') ?>" role="search">
        <i class="bi bi-search"></i>
        <?php foreach (['status', 'marital', 'condition', 'flag'] as $key): ?>
            <?php if (!empty($query[$key])): ?><input type="hidden" name="<?= $key ?>" value="<?= e($query[$key]) ?>"><?php endif; ?>
        <?php endforeach; ?>
        <input type="search" class="form-control" name="search" value="<?= e($filters['search']) ?>"
               placeholder="Cari nama kepala / anggota, no. KK, telepon" aria-label="Cari jemaat">
        <?php if ($filters['search'] !== ''): ?>
            <a href="<?= $link(['search' => null]) ?>" class="jm-search-clear" aria-label="Hapus pencarian"><i class="bi bi-x-lg"></i></a>
        <?php endif; ?>
    </form>
    <button type="button" class="jm-filter-btn" data-bs-toggle="offcanvas" data-bs-target="#filterSheet" aria-controls="filterSheet">
        <i class="bi bi-sliders"></i><span class="jm-filter-label">Filter</span>
        <?php if ($sheetFilterCount): ?><span class="jm-filter-count"><?= $sheetFilterCount ?></span><?php endif; ?>
    </button>
</div>

<!-- Filter cepat: kondisi keluarga -->
<nav class="jm-chips mb-2" aria-label="Filter kondisi keluarga">
    <a href="<?= $link(['condition' => null, 'flag' => null]) ?>" class="jm-chip <?= !$filters['condition'] && !$filters['flag'] ? 'active' : '' ?>">
        Semua <span class="jm-chip-count"><?= number_format($totalFamilies) ?></span>
    </a>
    <?php if ($recap['by_condition']['incomplete'] > 0): ?>
        <a href="<?= $link(['condition' => 'incomplete', 'flag' => null, 'status' => null]) ?>" class="jm-chip is-warning <?= $filters['condition'] === 'incomplete' ? 'active' : '' ?>">
            <i class="bi bi-exclamation-circle"></i>Perlu dilengkapi <span class="jm-chip-count"><?= number_format($recap['by_condition']['incomplete']) ?></span>
        </a>
    <?php endif; ?>
    <?php foreach (Family::CONDITIONS as $value => $label): ?>
        <?php if (in_array($value, ['incomplete', 'archive'], true) || ($value === 'other' && !$recap['by_condition'][$value])) continue; ?>
        <a href="<?= $link(['condition' => $value, 'flag' => null, 'status' => null]) ?>" class="jm-chip <?= $filters['condition'] === $value ? 'active' : '' ?>">
            <?= e($label) ?> <span class="jm-chip-count"><?= number_format($recap['by_condition'][$value]) ?></span>
        </a>
    <?php endforeach; ?>
    <?php foreach (Family::FLAGS as $value => $label): ?>
        <a href="<?= $link(['flag' => $value, 'condition' => null]) ?>" class="jm-chip <?= $filters['flag'] === $value ? 'active' : '' ?>">
            <?= e($label) ?> <span class="jm-chip-count"><?= number_format($recap['flags'][$value]) ?></span>
        </a>
    <?php endforeach; ?>
    <a href="<?= $link(['condition' => 'archive', 'flag' => null, 'status' => null]) ?>" class="jm-chip <?= $filters['condition'] === 'archive' ? 'active' : '' ?>">
        <i class="bi bi-archive"></i>Arsip <span class="jm-chip-count"><?= number_format($recap['by_condition']['archive']) ?></span>
    </a>
</nav>

<!-- Ringkasan hasil + filter aktif -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    <div class="jm-active-filters">
        <span class="fw-bold text-dark me-1"><?= number_format($pagination['total']) ?> KK</span>
        <?php if ($filters['search'] !== ''): ?>
            <a href="<?= $link(['search' => null]) ?>" class="jm-tag t-grey" title="Hapus pencarian"><i class="bi bi-search"></i>"<?= e($filters['search']) ?>"<i class="bi bi-x"></i></a>
        <?php endif; ?>
        <?php foreach ($activeTags as [$label, $remove]): ?>
            <a href="<?= $link($remove) ?>" class="jm-tag t-green" title="Hapus filter"><?= e($label) ?><i class="bi bi-x"></i></a>
        <?php endforeach; ?>
        <?php if ($hasFilter): ?>
            <a href="<?= url('admin/jemaat') ?>" class="small fw-bold ms-1">Reset semua</a>
        <?php endif; ?>
    </div>
</div>

<!-- Daftar KK -->
<?php if (empty($families)): ?>
    <div class="jm-empty">
        <div class="jm-empty-icon"><i class="bi bi-<?= $hasFilter ? 'search' : 'people' ?>"></i></div>
        <h2 class="h5 fw-bold"><?= $hasFilter ? 'Tidak ada KK yang cocok' : 'Belum ada data jemaat' ?></h2>
        <p class="text-muted mb-3"><?= $hasFilter ? 'Coba kata kunci lain atau longgarkan filter.' : 'Mulai dengan menambahkan keluarga pertama.' ?></p>
        <?php if ($hasFilter): ?>
            <a href="<?= url('admin/jemaat') ?>" class="btn btn-light">Reset filter</a>
        <?php else: ?>
            <a href="<?= url('admin/jemaat/tambah') ?>" class="btn btn-primary"><i class="bi bi-plus-lg me-2"></i>Tambah Keluarga</a>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="jm-list">
        <?php foreach ($families as $family): ?>
            <?php
            $list = $members[$family['id']] ?? [];
            $current = array_values(array_filter($list, fn($m) => $m['is_current']));
            $expanded = $needle !== '' && (bool) array_filter($list, $matches);
            $collapseId = 'fam-' . $family['id'];
            $isArchive = (int) $family['active_count'] === 0;
            ?>
            <article class="jm-family <?= $isArchive ? 'is-archive' : '' ?>">
                <button type="button" class="jm-family-row <?= $expanded ? '' : 'collapsed' ?>" data-bs-toggle="collapse"
                        data-bs-target="#<?= $collapseId ?>" aria-expanded="<?= $expanded ? 'true' : 'false' ?>" aria-controls="<?= $collapseId ?>">
                    <?= jmAvatar($family['head_name'], $family['head_gender'], '', $isArchive) ?>
                    <span class="jm-family-main">
                        <span class="jm-family-name d-block"><?= e($family['head_name'] ?: '(tanpa kepala)') ?></span>
                        <span class="jm-family-sub">
                            <span><?= e($family['family_code']) ?></span>
                            <span class="d-md-none"><i class="bi bi-people me-1"></i><?= (int) $family['active_count'] ?> anggota<?= (int) $family['archived_count'] ? ' · ' . (int) $family['archived_count'] . ' arsip' : '' ?></span>
                            <?php if ($family['address']): ?><span><i class="bi bi-geo-alt me-1"></i><?= e($family['address']) ?></span><?php endif; ?>
                        </span>
                        <span class="jm-family-tags">
                            <?= jmConditionTag($family['family_condition']) ?>
                            <?= jmFlagTags($family) ?>
                        </span>
                    </span>
                    <span class="jm-family-side">
                        <span class="jm-stack" aria-hidden="true">
                            <?php foreach (array_slice($current, 0, 4) as $m): ?>
                                <?= jmAvatar($m['full_name'], $m['gender']) ?>
                            <?php endforeach; ?>
                            <?php if (count($current) > 4): ?><span class="jm-avatar jm-stack-more">+<?= count($current) - 4 ?></span><?php endif; ?>
                        </span>
                        <span class="jm-family-count">
                            <b><?= (int) $family['active_count'] ?></b>
                            <small>anggota<?= (int) $family['archived_count'] ? ' · ' . (int) $family['archived_count'] . ' arsip' : '' ?></small>
                        </span>
                        <span class="jm-chevron"><i class="bi bi-chevron-down"></i></span>
                    </span>
                </button>

                <div class="collapse <?= $expanded ? 'show' : '' ?>" id="<?= $collapseId ?>">
                    <div class="jm-members">
                        <?php foreach ($list as $m): ?>
                            <div class="jm-member <?= $m['is_archived'] ? 'is-off' : '' ?> <?= $matches($m) ? 'is-match' : '' ?>">
                                <?= jmAvatar($m['full_name'], $m['gender'], 'sm', $m['is_archived']) ?>
                                <div class="jm-member-main">
                                    <div class="jm-member-name text-truncate"><?= e($m['full_name']) ?></div>
                                    <div class="jm-member-meta">
                                        <?= e(FamilyMember::relationshipLabel($m['relationship'], $m['gender'])) ?>
                                        · <?= jmAge($m['birth_date']) ?>
                                        · <?= e(Member::maritalLabel($m['marital_status'], $m['gender'])) ?>
                                    </div>
                                </div>
                                <?php if ($m['status'] !== 'active' || $m['is_archived']): ?>
                                    <?= jmStatusTag($m['status']) ?>
                                <?php elseif ($m['relationship'] === 'head'): ?>
                                    <span class="jm-tag t-green"><i class="bi bi-star-fill"></i>Kepala</span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                        <div class="jm-members-foot">
                            <a href="<?= url('admin/jemaat/detail/' . $family['id']) ?>" class="btn btn-sm btn-outline-primary">
                                Kelola keluarga<i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <?php if ($pagination['last_page'] > 1): ?>
        <nav class="mt-4" aria-label="Navigasi halaman">
            <ul class="pagination justify-content-center mb-0">
                <li class="page-item <?= $pagination['current_page'] <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= $link(['page' => $pagination['current_page'] - 1]) ?>" aria-label="Sebelumnya"><i class="bi bi-chevron-left"></i></a>
                </li>
                <?php for ($i = 1; $i <= $pagination['last_page']; $i++): ?>
                    <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                        <a class="page-link" href="<?= $link(['page' => $i]) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $pagination['current_page'] >= $pagination['last_page'] ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= $link(['page' => $pagination['current_page'] + 1]) ?>" aria-label="Berikutnya"><i class="bi bi-chevron-right"></i></a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
<?php endif; ?>

<a href="<?= url('admin/jemaat/tambah') ?>" class="jm-fab" aria-label="Tambah keluarga"><i class="bi bi-plus-lg"></i></a>

<!-- Filter lengkap: bottom sheet (mobile) / drawer (desktop) -->
<div class="offcanvas offcanvas-end jm-sheet" tabindex="-1" id="filterSheet" aria-labelledby="filterSheetTitle">
    <form method="GET" action="<?= url('admin/jemaat') ?>" class="d-flex flex-column h-100">
        <?php if ($filters['search'] !== ''): ?><input type="hidden" name="search" value="<?= e($filters['search']) ?>"><?php endif; ?>
        <div class="offcanvas-header">
            <h2 class="offcanvas-title h5" id="filterSheetTitle">Filter Jemaat</h2>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
        </div>
        <div class="offcanvas-body">
            <div class="jm-sheet-group">
                <div class="jm-sheet-title">Status jemaat</div>
                <div class="jm-pills">
                    <?php foreach (Member::STATUSES as $value => $label): ?>
                        <input type="radio" class="btn-check" name="status" id="fs_<?= $value ?>" value="<?= $value ?>" <?= $filters['status'] === $value ? 'checked' : '' ?>>
                        <label class="jm-pill" for="fs_<?= $value ?>"><?= e($label) ?> <span class="jm-pill-count"><?= number_format($summary['by_status'][$value]) ?></span></label>
                    <?php endforeach; ?>
                    <input type="radio" class="btn-check" name="status" id="fs_all" value="all" <?= $filters['status'] === 'all' ? 'checked' : '' ?>>
                    <label class="jm-pill" for="fs_all">Semua</label>
                </div>
                <p class="form-text mb-0">Selain Aktif menampilkan KK tempat jemaat tersebut tercatat sebagai arsip.</p>
            </div>
            <div class="jm-sheet-group">
                <div class="jm-sheet-title">Status pernikahan</div>
                <div class="jm-pills">
                    <input type="radio" class="btn-check" name="marital" id="fm_any" value="" <?= $filters['marital'] === '' ? 'checked' : '' ?>>
                    <label class="jm-pill" for="fm_any">Semua</label>
                    <?php foreach (Member::MARITAL_FILTERS as $value => $label): ?>
                        <input type="radio" class="btn-check" name="marital" id="fm_<?= $value ?>" value="<?= $value ?>" <?= $filters['marital'] === $value ? 'checked' : '' ?>>
                        <label class="jm-pill" for="fm_<?= $value ?>"><?= e($label) ?></label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="jm-sheet-group">
                <div class="jm-sheet-title">Kondisi keluarga</div>
                <div class="jm-pills">
                    <input type="radio" class="btn-check" name="condition" id="fc_any" value="" <?= $filters['condition'] === '' ? 'checked' : '' ?>>
                    <label class="jm-pill" for="fc_any">Semua</label>
                    <?php foreach (Family::CONDITIONS as $value => $label): ?>
                        <input type="radio" class="btn-check" name="condition" id="fc_<?= $value ?>" value="<?= $value ?>" <?= $filters['condition'] === $value ? 'checked' : '' ?>>
                        <label class="jm-pill" for="fc_<?= $value ?>"><?= e($label) ?> <span class="jm-pill-count"><?= number_format($recap['by_condition'][$value]) ?></span></label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="jm-sheet-group">
                <div class="jm-sheet-title">Penanda</div>
                <div class="jm-pills">
                    <input type="radio" class="btn-check" name="flag" id="ff_any" value="" <?= $filters['flag'] === '' ? 'checked' : '' ?>>
                    <label class="jm-pill" for="ff_any">Semua</label>
                    <?php foreach (Family::FLAGS as $value => $label): ?>
                        <input type="radio" class="btn-check" name="flag" id="ff_<?= $value ?>" value="<?= $value ?>" <?= $filters['flag'] === $value ? 'checked' : '' ?>>
                        <label class="jm-pill" for="ff_<?= $value ?>"><?= e($label) ?></label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="jm-sheet-footer">
            <a href="<?= url('admin/jemaat') ?>" class="btn btn-light">Reset</a>
            <button type="submit" class="btn btn-primary">Terapkan</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Jangan kirim parameter kosong / default agar URL tetap bersih
    document.querySelector('#filterSheet form').addEventListener('submit', function () {
        this.querySelectorAll('input[type=radio]:checked').forEach(function (r) {
            if (r.value === '' || (r.name === 'status' && r.value === 'active')) r.disabled = true;
        });
    });
});
</script>
