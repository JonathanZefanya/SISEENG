<?php
use App\Models\Member;
use App\Models\Family;
use App\Models\FamilyMember;
use App\Models\MemberStatusLog;

require_once __DIR__ . '/_helpers.php';

$active = array_values(array_filter($members, fn($m) => $m['is_current']));
$archived = array_values(array_filter($members, fn($m) => !$m['is_current']));
$names = array_column($members, 'full_name', 'id');
$superAdmin = isSuperAdmin();
$isArchive = (int) $family['active_count'] === 0;

$logIcon = function (array $log) {
    switch ($log['field']) {
        case 'status':
            return $log['new_value'] === 'deceased' ? ['flower1', 't-dark'] : ['person-gear', 't-blue'];
        case 'marital_status':
            return ['heart', 't-pink'];
        case 'relationship':
            return ['diagram-3', 't-purple'];
        default:
            return $log['new_value'] ? ['box-arrow-in-right', 't-green'] : ['box-arrow-right', 't-orange'];
    }
};
?>
<a href="<?= url('admin/jemaat') ?>" class="d-inline-flex align-items-center gap-1 small fw-bold mb-3 text-decoration-none">
    <i class="bi bi-arrow-left"></i>Data Jemaat
</a>

<!-- Hero keluarga -->
<section class="jm-hero jm-hero-family mb-4">
    <div class="d-flex flex-wrap gap-3 align-items-start justify-content-between">
        <div class="d-flex gap-3 align-items-start min-w-0 flex-grow-1">
        <?= jmAvatar($family['head_name'], $family['head_gender'], 'lg') ?>
        <div class="flex-grow-1 min-w-0">
            <div class="jm-hero-meta"><?= e($family['family_code']) ?> · Kepala keluarga</div>
            <h1 class="text-truncate"><?= e($family['head_name'] ?: '(tanpa kepala)') ?></h1>
            <div class="d-flex flex-wrap gap-2 mt-2">
                <?= jmConditionTag($family['family_condition']) ?>
                <?php if ((int) $family['adult_child_count'] > 0 && !$isArchive): ?>
                    <span class="jm-tag"><i class="bi bi-person-check"></i><?= e(Family::FLAGS['adult_child']) ?></span>
                <?php endif; ?>
                <?php if ((int) $family['married_child_count'] > 0): ?>
                    <span class="jm-tag"><i class="bi bi-diagram-3"></i><?= e(Family::FLAGS['married_child']) ?></span>
                <?php endif; ?>
            </div>
        </div>
        </div>
        <div class="jm-hero-actions">
            <?php if (!$isArchive): ?>
                <a href="<?= url('admin/jemaat/anggota/tambah/' . $family['id']) ?>" class="btn btn-light">
                    <i class="bi bi-person-plus me-2"></i>Tambah anggota
                </a>
            <?php endif; ?>
            <a href="<?= url('admin/jemaat/edit-kk/' . $family['id']) ?>" class="btn btn-ghost">
                <i class="bi bi-pencil me-2"></i>Edit KK
            </a>
            <?php if ($superAdmin): ?>
                <div class="dropdown">
                    <button class="btn btn-ghost btn-icon" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Aksi lain">
                        <i class="bi bi-three-dots"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <button type="button" class="dropdown-item text-danger"
                                    data-delete-url="<?= url('admin/jemaat/hapus-kk/' . $family['id']) ?>"
                                    data-delete-name="<?= e($family['family_code']) ?>"
                                    data-delete-label="KK <?= e($family['family_code']) ?> beserta anggota yang riwayatnya hanya di KK ini">
                                <i class="bi bi-trash me-2"></i>Hapus KK permanen
                            </button>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="jm-hero-stats mt-3">
        <div class="jm-hero-stat"><i class="bi bi-people"></i><div><b><?= (int) $family['active_count'] ?></b><small>Anggota aktif</small></div></div>
        <div class="jm-hero-stat"><i class="bi bi-archive"></i><div><b><?= (int) $family['archived_count'] ?></b><small>Arsip</small></div></div>
        <div class="jm-hero-stat"><i class="bi bi-diagram-3"></i><div><b><?= count($descendants) ?></b><small>KK turunan</small></div></div>
    </div>
</section>

<div class="row g-4">
    <div class="col-lg-8">
        <ul class="nav jm-tabs mb-3" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tabMembers" type="button" role="tab" aria-controls="tabMembers" aria-selected="true">
                    Anggota <span class="jm-tab-count"><?= count($active) ?></span>
                </button>
            </li>
            <?php if ($archived): ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tabArchive" type="button" role="tab" aria-controls="tabArchive" aria-selected="false">
                        Arsip &amp; keluar <span class="jm-tab-count"><?= count($archived) ?></span>
                    </button>
                </li>
            <?php endif; ?>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tabHistory" type="button" role="tab" aria-controls="tabHistory" aria-selected="false">
                    Riwayat <span class="jm-tab-count"><?= count($logs) ?></span>
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <!-- Anggota aktif -->
            <div class="tab-pane fade show active" id="tabMembers" role="tabpanel" tabindex="0">
                <?php if (!$active): ?>
                    <div class="jm-empty">
                        <div class="jm-empty-icon"><i class="bi bi-archive"></i></div>
                        <h2 class="h6 fw-bold">KK ini sudah tidak punya anggota aktif</h2>
                        <p class="text-muted small mb-0">Datanya tetap tersimpan sebagai arsip.</p>
                    </div>
                <?php else: ?>
                    <div class="jm-people">
                        <?php foreach ($active as $m): ?>
                            <div class="jm-person-card <?= $m['relationship'] === 'head' ? 'is-head' : '' ?>">
                                <?= jmAvatar($m['full_name'], $m['gender']) ?>
                                <div class="jm-person-main">
                                    <div class="jm-person-rel"><?= e(FamilyMember::relationshipLabel($m['relationship'], $m['gender'])) ?></div>
                                    <div class="jm-person-name"><?= e($m['full_name']) ?></div>
                                    <div class="jm-person-facts">
                                        <span><i class="bi bi-cake2"></i><?= jmAge($m['birth_date']) ?></span>
                                        <span><i class="bi bi-heart"></i><?= e(Member::maritalLabel($m['marital_status'], $m['gender'])) ?><?= $m['spouse_id'] && isset($names[$m['spouse_id']]) ? ' · ' . e(strtok($names[$m['spouse_id']], ' ')) : '' ?></span>
                                        <?php if ($m['phone']): ?><span><i class="bi bi-telephone"></i><?= e($m['phone']) ?></span><?php endif; ?>
                                    </div>
                                    <?php if ($m['marital_status'] === null || $m['relationship'] === 'head'): ?>
                                        <div class="jm-person-tags">
                                            <?php if ($m['relationship'] === 'head'): ?><span class="jm-tag t-green"><i class="bi bi-star-fill"></i>Kepala keluarga</span><?php endif; ?>
                                            <?php if ($m['marital_status'] === null): ?>
                                                <a href="<?= url('admin/jemaat/anggota/edit/' . $m['id']) ?>" class="jm-tag t-yellow text-decoration-none"><i class="bi bi-exclamation-circle"></i>Lengkapi status pernikahan</a>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="jm-person-menu dropdown">
                                    <button class="btn btn-light btn-icon" type="button" data-bs-toggle="dropdown" data-bs-popper-config='{"strategy":"fixed"}'
                                            aria-expanded="false" aria-label="Aksi untuk <?= e($m['full_name']) ?>">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="<?= url('admin/jemaat/anggota/edit/' . $m['id']) ?>"><i class="bi bi-pencil me-2"></i>Edit data pribadi</a></li>
                                        <li><a class="dropdown-item" href="<?= url('admin/jemaat/anggota/status/' . $m['id']) ?>"><i class="bi bi-person-gear me-2"></i>Ubah status jemaat</a></li>
                                        <li><a class="dropdown-item" href="<?= url('admin/jemaat/anggota/pernikahan/' . $m['id']) ?>"><i class="bi bi-heart me-2"></i>Ubah status pernikahan</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <?php if ($m['marital_status'] !== 'married'): ?>
                                            <li><a class="dropdown-item" href="<?= url('admin/jemaat/anggota/menikah/' . $m['id']) ?>"><i class="bi bi-hearts me-2"></i>Catat pernikahan</a></li>
                                        <?php else: ?>
                                            <li><a class="dropdown-item" href="<?= url('admin/jemaat/anggota/cerai/' . $m['id']) ?>"><i class="bi bi-heartbreak me-2"></i>Catat perceraian</a></li>
                                        <?php endif; ?>
                                        <?php if ($m['relationship'] !== 'head'): ?>
                                            <li><a class="dropdown-item" href="<?= url('admin/jemaat/anggota/kepala/' . $m['id']) ?>"><i class="bi bi-star me-2"></i>Jadikan kepala keluarga</a></li>
                                        <?php endif; ?>
                                        <li><a class="dropdown-item" href="<?= url('admin/jemaat/anggota/pindah/' . $m['id']) ?>"><i class="bi bi-box-arrow-right me-2"></i>Pindahkan ke KK lain</a></li>
                                        <?php if ($superAdmin): ?>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <button type="button" class="dropdown-item text-danger"
                                                        data-delete-url="<?= url('admin/jemaat/anggota/hapus/' . $m['id']) ?>"
                                                        data-delete-name="<?= e($m['full_name']) ?>"
                                                        data-delete-label="data <?= e($m['full_name']) ?> beserta riwayatnya">
                                                    <i class="bi bi-trash me-2"></i>Hapus permanen
                                                </button>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php if (!$isArchive): ?>
                            <a href="<?= url('admin/jemaat/anggota/tambah/' . $family['id']) ?>" class="jm-add-tile text-decoration-none">
                                <i class="bi bi-person-plus"></i>Tambah anggota
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Arsip & keluar KK -->
            <?php if ($archived): ?>
                <div class="tab-pane fade" id="tabArchive" role="tabpanel" tabindex="0">
                    <div class="jm-people">
                        <?php foreach ($archived as $m): ?>
                            <div class="jm-person-card is-off">
                                <?= jmAvatar($m['full_name'], $m['gender'], '', true) ?>
                                <div class="jm-person-main">
                                    <div class="jm-person-rel"><?= e(FamilyMember::relationshipLabel($m['relationship'], $m['gender'])) ?></div>
                                    <div class="jm-person-name"><?= e($m['full_name']) ?></div>
                                    <div class="jm-person-facts">
                                        <span><i class="bi bi-box-arrow-right"></i><?= e(FamilyMember::LEFT_REASONS[$m['left_reason']] ?? '-') ?></span>
                                        <?php if ($m['left_at']): ?><span><i class="bi bi-calendar3"></i><?= formatDate($m['left_at']) ?></span><?php endif; ?>
                                    </div>
                                    <div class="jm-person-tags">
                                        <?= jmStatusTag($m['status']) ?>
                                        <?php if ($m['current_family_id']): ?>
                                            <a href="<?= url('admin/jemaat/detail/' . $m['current_family_id']) ?>" class="jm-tag t-outline text-decoration-none">
                                                <i class="bi bi-house"></i>Sekarang di <?= e($m['current_family_code']) ?>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($m['is_archived']): ?>
                                        <a href="<?= url('admin/jemaat/anggota/status/' . $m['id']) ?>" class="btn btn-sm btn-light mt-2">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i>Ubah status / aktifkan
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Riwayat -->
            <div class="tab-pane fade" id="tabHistory" role="tabpanel" tabindex="0">
                <div class="jm-panel">
                    <?php if (!$logs): ?>
                        <p class="text-muted mb-0">Belum ada riwayat.</p>
                    <?php else: ?>
                        <ul class="jm-timeline">
                            <?php foreach ($logs as $log): ?>
                                <?php [$icon, $tone] = $logIcon($log); ?>
                                <li>
                                    <span class="jm-timeline-icon <?= $tone ?>"><i class="bi bi-<?= $icon ?>"></i></span>
                                    <div class="jm-timeline-body">
                                        <div class="jm-timeline-title"><strong><?= e($log['full_name']) ?></strong> — <?= e(MemberStatusLog::describe($log)) ?></div>
                                        <div class="jm-timeline-meta">
                                            <?= formatDate($log['changed_at']) ?>
                                            <?= $log['reason'] ? ' · ' . e($log['reason']) : '' ?>
                                            <?= $log['related_name'] ? ' · terkait ' . e($log['related_name']) : '' ?>
                                            <?= $log['user_name'] ? ' · oleh ' . e($log['user_name']) : '' ?>
                                        </div>
                                        <?php if ($log['note']): ?><div class="small text-muted fst-italic mt-1">"<?= e($log['note']) ?>"</div><?php endif; ?>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="jm-panel">
            <div class="jm-panel-title"><i class="bi bi-house-heart text-primary"></i>Info KK</div>
            <div class="jm-info">
                <div class="jm-info-row">
                    <span class="jm-info-icon"><i class="bi bi-geo-alt"></i></span>
                    <div class="min-w-0"><div class="jm-info-label">Alamat</div><div class="jm-info-value"><?= $family['address'] ? nl2br(e($family['address'])) : '<span class="text-muted">Belum diisi</span>' ?></div></div>
                </div>
                <div class="jm-info-row">
                    <span class="jm-info-icon"><i class="bi bi-telephone"></i></span>
                    <div class="min-w-0"><div class="jm-info-label">Telepon rumah</div><div class="jm-info-value"><?= $family['phone'] ? e($family['phone']) : '<span class="text-muted">Belum diisi</span>' ?></div></div>
                </div>
                <?php if ($family['notes']): ?>
                    <div class="jm-info-row">
                        <span class="jm-info-icon"><i class="bi bi-sticky"></i></span>
                        <div class="min-w-0"><div class="jm-info-label">Catatan</div><div class="jm-info-value"><?= e($family['notes']) ?></div></div>
                    </div>
                <?php endif; ?>
                <div class="jm-info-row">
                    <span class="jm-info-icon"><i class="bi bi-calendar-plus"></i></span>
                    <div class="min-w-0"><div class="jm-info-label">Tercatat sejak</div><div class="jm-info-value"><?= formatDate($family['created_at']) ?></div></div>
                </div>
            </div>
        </div>

        <div class="jm-panel">
            <div class="jm-panel-title"><i class="bi bi-diagram-3 text-primary"></i>Asal-usul keluarga</div>
            <div class="jm-tree">
                <?php if ($origin): ?>
                    <a href="<?= url('admin/jemaat/detail/' . $origin['id']) ?>" class="jm-tree-node">
                        <?= jmAvatar($origin['head_name'], $origin['head_gender'], 'sm') ?>
                        <span class="min-w-0"><?= e($origin['head_name'] ?: '-') ?><small><?= e($origin['family_code']) ?> · KK asal</small></span>
                    </a>
                    <div class="jm-tree-arrow"><i class="bi bi-arrow-down"></i></div>
                <?php endif; ?>
                <div class="jm-tree-node is-self">
                    <?= jmAvatar($family['head_name'], $family['head_gender'], 'sm') ?>
                    <span class="min-w-0"><?= e($family['head_name'] ?: '-') ?><small><?= e($family['family_code']) ?> · KK ini</small></span>
                </div>
                <?php if ($descendants): ?>
                    <div class="jm-tree-children">
                        <?php foreach ($descendants as $d): ?>
                            <a href="<?= url('admin/jemaat/detail/' . $d['id']) ?>" class="jm-tree-node">
                                <?= jmAvatar($d['head_name'], $d['head_gender'], 'sm') ?>
                                <span class="min-w-0"><?= e($d['head_name'] ?: '-') ?><small><?= e($d['family_code']) ?> · <?= e(Family::CONDITIONS[$d['family_condition']]) ?></small></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php elseif (!$origin): ?>
                    <p class="form-text mb-0">Anak yang menikah dan membuat KK baru akan muncul di bawah KK ini.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if ($superAdmin): ?>
    <!-- Konfirmasi hapus permanen (ketik nama/no. KK) -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" method="POST" id="deleteForm">
                <?= csrfField() ?>
                <div class="modal-body text-center pt-4">
                    <div class="jm-empty-icon" style="background:#fdecee;color:#c0283a"><i class="bi bi-trash3"></i></div>
                    <h2 class="h5 fw-bold" id="deleteModalTitle">Hapus permanen?</h2>
                    <p class="text-muted">Anda akan menghapus <strong id="deleteLabel"></strong>. Tindakan ini tidak bisa dibatalkan. Untuk jemaat yang meninggal/pindah, cukup ubah statusnya.</p>
                    <label class="form-label" for="confirmName">Ketik <strong id="deleteName"></strong> untuk konfirmasi</label>
                    <input type="text" class="form-control text-center" id="confirmName" name="confirm_name" autocomplete="off" required>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light flex-fill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger flex-fill" id="deleteSubmit" disabled><i class="bi bi-trash me-2"></i>Hapus permanen</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('deleteForm');
        const input = document.getElementById('confirmName');
        const submit = document.getElementById('deleteSubmit');
        let expected = '';

        document.querySelectorAll('[data-delete-url]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                form.action = btn.dataset.deleteUrl;
                expected = btn.dataset.deleteName.trim().toLowerCase();
                document.getElementById('deleteLabel').textContent = btn.dataset.deleteLabel;
                document.getElementById('deleteName').textContent = btn.dataset.deleteName;
                input.value = '';
                submit.disabled = true;
                bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteModal')).show();
            });
        });
        input.addEventListener('input', function () {
            submit.disabled = input.value.trim().toLowerCase() !== expected;
        });
    });
    </script>
<?php endif; ?>
