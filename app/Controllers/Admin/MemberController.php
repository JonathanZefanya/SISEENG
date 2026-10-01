<?php
namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\Member;
use App\Models\Family;
use App\Models\FamilyMember;
use App\Models\MemberStatusLog;
use App\Models\ActivityLog;
use App\Middleware\RoleMiddleware;
use Core\Security;

/**
 * =========================================================
 * Admin Member Controller (Data Jemaat berbasis Keluarga)
 * =========================================================
 *
 * Daftar ditampilkan per Kartu Keluarga. Perubahan status, pernikahan,
 * dan perpindahan KK dijalankan lewat App\Models\Family agar aturan
 * bisnis & riwayatnya konsisten.
 */
class MemberController extends Controller
{
    protected $layout = 'admin';
    private $memberModel;
    private $familyModel;
    private $membership;

    /** Field data pribadi yang bisa diisi dari form */
    const PERSON_FIELDS = [
        'full_name', 'gender', 'marital_status', 'birth_date', 'birth_place',
        'phone', 'email', 'baptism_date', 'membership_date', 'notes',
    ];

    public function __construct()
    {
        RoleMiddleware::requireAdmin();
        $this->memberModel = new Member();
        $this->familyModel = new Family();
        $this->membership = new FamilyMember();
    }

    // =====================================================
    // DAFTAR & DETAIL
    // =====================================================

    /**
     * Daftar KK (1 baris per kepala keluarga)
     */
    public function index(): void
    {
        $page = max(1, (int) ($this->get('page') ?? 1));
        $condition = $this->get('condition', '');

        // Default hanya KK yang punya anggota aktif; filter Arsip otomatis melihat semua status
        $status = $this->get('status') ?: ($condition === 'archive' ? 'all' : 'active');

        $filters = [
            'search' => $this->get('search', ''),
            'status' => $status,
            'marital' => $this->get('marital', ''),
            'condition' => $condition,
            'flag' => $this->get('flag', ''),
        ];

        $families = $this->familyModel->getWithPagination($page, ITEMS_PER_PAGE, $filters);
        $members = $this->familyModel->membersFor(array_column($families['data'], 'id'));

        $this->view('admin/members/index', [
            'title' => 'Data Jemaat - ' . APP_NAME,
            'families' => $families['data'],
            'members' => $members,
            'pagination' => $families,
            'filters' => $filters,
            'summary' => $this->memberModel->getSummary(),
            'recap' => $this->familyModel->getRecap(),
            'totalFamilies' => $this->familyModel->countActive(),
        ]);
    }

    /**
     * Detail KK
     */
    public function show(int $id = 0): void
    {
        $family = $this->findFamilyOrRedirect($id);

        $this->view('admin/members/show', [
            'title' => 'Detail KK ' . $family['family_code'] . ' - ' . APP_NAME,
            'family' => $family,
            'members' => $this->familyModel->membersFor([$id], true)[$id] ?? [],
            'origin' => $family['origin_family_id'] ? $this->familyModel->findSummary((int) $family['origin_family_id']) : null,
            'descendants' => $this->familyModel->descendants($id),
            'logs' => (new MemberStatusLog())->forFamily($id),
        ]);
    }

    /**
     * Link lama per orang (admin/members/show/{id}) → detail KK orang tersebut
     */
    public function showMember(int $id = 0): void
    {
        $row = $this->membership->latest($id);
        if (!$row) {
            setFlash('error', 'Data jemaat tidak ditemukan.');
            $this->redirect('admin/jemaat');
            return;
        }
        $this->redirect('admin/jemaat/detail/' . $row['family_id']);
    }

    /**
     * Pencarian untuk kolom pilih jemaat / pilih KK (JSON)
     */
    public function search(): void
    {
        $q = trim((string) $this->get('q', ''));
        $exclude = (int) $this->get('exclude', 0) ?: null;

        if (mb_strlen($q) < 2) {
            $this->json(['results' => []]);
        }

        if ($this->get('type') === 'family') {
            $results = array_map(fn($f) => [
                'id' => (int) $f['id'],
                'label' => $f['family_code'] . ' — ' . ($f['head_name'] ?: '-'),
                'meta' => $f['active_count'] . ' anggota aktif' . ($f['address'] ? ' · ' . mb_substr($f['address'], 0, 40) : ''),
            ], $this->familyModel->searchActive($q, $exclude));
        } else {
            $results = array_map(fn($m) => [
                'id' => (int) $m['id'],
                'label' => $m['full_name'],
                'gender' => $m['gender'],
                'meta' => FamilyMember::relationshipLabel($m['relationship'], $m['gender']) . ' di ' . $m['family_code']
                    . ' · ' . Member::maritalLabel($m['marital_status'], $m['gender']),
            ], $this->memberModel->searchActive($q, $exclude));
        }

        $this->json(['results' => $results]);
    }

    // =====================================================
    // TAMBAH & EDIT KELUARGA
    // =====================================================

    /**
     * Form tambah keluarga (kepala + anggota dalam satu alur)
     */
    public function create(): void
    {
        $this->view('admin/members/create', [
            'title' => 'Tambah Keluarga - ' . APP_NAME,
        ]);
    }

    /**
     * Simpan keluarga baru
     */
    public function store(): void
    {
        if (!$this->isPost()) {
            $this->redirect('admin/jemaat');
            return;
        }

        $this->validateCsrf();

        $family = [
            'address' => $this->post('address'),
            'phone' => $this->post('phone'),
            'notes' => $this->post('family_notes'),
        ];
        $head = $this->personFromInput($this->post('head') ?: []);
        $others = [];
        foreach (($this->post('members') ?: []) as $input) {
            if (!is_array($input) || trim($input['full_name'] ?? '') === '' && empty($input['gender'])) {
                continue; // kartu anggota kosong diabaikan
            }
            $person = $this->personFromInput($input);
            $person['relationship'] = $input['relationship'] ?? '';
            $others[] = $person;
        }

        $errors = $this->validatePerson($head, 'Kepala keluarga');
        $spouseCount = 0;
        foreach ($others as $i => $person) {
            $label = 'Anggota ' . ($i + 1) . ($person['full_name'] ? " ({$person['full_name']})" : '');
            $errors = array_merge($errors, $this->validatePerson($person, $label));
            if (!isset(FamilyMember::RELATIONSHIPS[$person['relationship']]) || $person['relationship'] === 'head') {
                $errors[] = "{$label}: hubungan keluarga tidak valid.";
            }
            if ($person['relationship'] === 'spouse') {
                $spouseCount++;
                if ($person['gender'] && $person['gender'] === $head['gender']) {
                    $errors[] = "{$label}: jenis kelamin pasangan harus berbeda dengan kepala keluarga.";
                }
            }
        }
        if ($spouseCount > 1) {
            $errors[] = 'Satu KK hanya boleh punya satu Suami/Istri dari kepala keluarga.';
        }

        if ($errors) {
            $this->failBack($errors, 'admin/jemaat/tambah');
            return;
        }

        try {
            $familyId = $this->familyModel->createFamily($family, $head, $others, date('Y-m-d'));
            $code = $this->familyModel->find($familyId)['family_code'];
            ActivityLog::log(auth('id'), 'create_family', "Menambah keluarga {$code}: {$head['full_name']} (" . (count($others) + 1) . ' orang)');
            setFlash('success', "Keluarga {$code} berhasil ditambahkan.");
            $this->clearOldInput();
            $this->redirect('admin/jemaat/detail/' . $familyId);
        } catch (\DomainException $e) {
            $this->failBack([$e->getMessage()], 'admin/jemaat/tambah');
        } catch (\Exception $e) {
            error_log('createFamily: ' . $e->getMessage());
            $this->failBack(['Gagal menyimpan data keluarga.'], 'admin/jemaat/tambah');
        }
    }

    /**
     * Edit data KK (alamat, telepon, catatan)
     */
    public function editFamily(int $id = 0): void
    {
        $family = $this->findFamilyOrRedirect($id);

        if ($this->isPost()) {
            $this->validateCsrf();
            $this->familyModel->updateFamily($id, [
                'address' => $this->post('address'),
                'phone' => $this->post('phone'),
                'notes' => $this->post('notes'),
            ]);
            ActivityLog::log(auth('id'), 'update_family', "Mengupdate data {$family['family_code']}");
            setFlash('success', 'Data KK berhasil diupdate.');
            $this->redirect('admin/jemaat/detail/' . $id);
            return;
        }

        $this->view('admin/members/family-edit', [
            'title' => 'Edit ' . $family['family_code'] . ' - ' . APP_NAME,
            'family' => $family,
            'familyRow' => $this->familyModel->find($id),
        ]);
    }

    /**
     * Tambah anggota ke KK (orang baru atau jemaat yang sudah ada)
     */
    public function addMember(int $familyId = 0): void
    {
        $family = $this->findFamilyOrRedirect($familyId);
        $back = 'admin/jemaat/anggota/tambah/' . $familyId;

        if ($this->isPost()) {
            $this->validateCsrf();
            $relationship = $this->post('relationship', '');
            $date = $this->postDate('date');

            try {
                if ($this->post('mode') === 'existing') {
                    $memberId = (int) $this->post('existing_id');
                    $member = $this->memberModel->find($memberId);
                    if (!$member) {
                        throw new \DomainException('Pilih jemaat yang akan dipindahkan ke KK ini.');
                    }
                    $current = $this->membership->current($memberId);
                    if ($current && $current['relationship'] === 'head' && count($this->membership->activeMembers((int) $current['family_id'])) > 1) {
                        throw new \DomainException("{$member['full_name']} adalah kepala keluarga di KK lain yang masih punya anggota. Buka KK tersebut dan gunakan menu Pindahkan KK agar bisa memilih kepala baru.");
                    }
                    $this->familyModel->moveMember($memberId, $familyId, $relationship, $date, 'moved_family', $this->post('note'));
                    $name = $member['full_name'];
                } else {
                    $person = $this->personFromInput($this->post('person') ?: []);
                    $errors = $this->validatePerson($person, 'Anggota baru');
                    if ($errors) {
                        $this->failBack($errors, $back);
                        return;
                    }
                    $this->familyModel->addNewMember($familyId, $person, $relationship, $date);
                    $name = $person['full_name'];
                }

                ActivityLog::log(auth('id'), 'add_family_member', "Menambah {$name} ke {$family['family_code']}");
                setFlash('success', "{$name} berhasil ditambahkan ke {$family['family_code']}.");
                $this->clearOldInput();
                $this->redirect('admin/jemaat/detail/' . $familyId);
            } catch (\DomainException $e) {
                $this->failBack([$e->getMessage()], $back);
            } catch (\Exception $e) {
                error_log('addMember: ' . $e->getMessage());
                $this->failBack(['Gagal menambahkan anggota.'], $back);
            }
            return;
        }

        $this->view('admin/members/member-add', [
            'title' => 'Tambah Anggota ' . $family['family_code'] . ' - ' . APP_NAME,
            'family' => $family,
        ]);
    }

    /**
     * Edit data pribadi anggota
     */
    public function editMember(int $id = 0): void
    {
        [$member, $row] = $this->findMemberOrRedirect($id);
        $back = 'admin/jemaat/anggota/edit/' . $id;

        if ($this->isPost()) {
            $this->validateCsrf();
            $data = $this->personFromInput($this->post());

            // Status pernikahan hanya boleh diisi di sini bila masih kosong (data migrasi)
            if ($member['marital_status'] !== null) {
                $data['marital_status'] = $member['marital_status'];
            }

            $errors = $this->validatePerson($data, 'Data jemaat');
            if ($errors) {
                $this->failBack($errors, $back);
                return;
            }

            try {
                $newMarital = $data['marital_status'];
                unset($data['marital_status']);
                $this->memberModel->update($id, $data);
                if ($member['marital_status'] === null && $newMarital) {
                    $this->familyModel->changeMarital($id, $newMarital, date('Y-m-d'), 'Melengkapi data');
                }
                ActivityLog::log(auth('id'), 'update_member', "Mengupdate jemaat: {$data['full_name']}");
                setFlash('success', 'Data jemaat berhasil diupdate.');
                $this->clearOldInput();
                $this->redirect('admin/jemaat/detail/' . $row['family_id']);
            } catch (\DomainException $e) {
                $this->failBack([$e->getMessage()], $back);
            } catch (\Exception $e) {
                error_log('editMember: ' . $e->getMessage());
                $this->failBack(['Gagal mengupdate data jemaat.'], $back);
            }
            return;
        }

        $this->view('admin/members/edit', [
            'title' => 'Edit Jemaat - ' . APP_NAME,
            'member' => $member,
            'row' => $row,
            'family' => $this->familyModel->findSummary((int) $row['family_id']),
        ]);
    }

    // =====================================================
    // AKSI PER ANGGOTA
    // =====================================================

    /**
     * Ubah status jemaat (Aktif / Tidak Aktif / Meninggal / Pindah Gereja / Pindah Agama)
     */
    public function status(int $id = 0): void
    {
        [$member, $row] = $this->findMemberOrRedirect($id);
        $back = 'admin/jemaat/anggota/status/' . $id;

        if ($this->isPost()) {
            $this->validateCsrf();
            $status = $this->post('status', '');

            $this->runAction(function () use ($id, $member, $status) {
                $this->familyModel->changeStatus(
                    $id,
                    $status,
                    $this->postDate('date'),
                    $this->post('note'),
                    (int) $this->post('new_head_id') ?: null,
                    $this->postRelationships(),
                    $this->post('reactivate_to', 'last')
                );
                ActivityLog::log(auth('id'), 'member_status', "Status {$member['full_name']}: "
                    . Member::statusLabel($member['status']) . ' → ' . Member::statusLabel($status));
                return 'Status jemaat berhasil diubah.';
            }, $back, (int) $row['family_id']);
            return;
        }

        $this->view('admin/members/status', [
            'title' => 'Ubah Status Jemaat - ' . APP_NAME,
            'member' => $member,
            'row' => $row,
            'family' => $this->familyModel->findSummary((int) $row['family_id']),
            'headPicker' => $this->headPickerData($member, $row),
            'spouse' => $member['spouse_id'] ? $this->memberModel->find((int) $member['spouse_id']) : null,
        ]);
    }

    /**
     * Ubah status pernikahan (koreksi data)
     */
    public function marital(int $id = 0): void
    {
        [$member, $row] = $this->findMemberOrRedirect($id);
        $back = 'admin/jemaat/anggota/pernikahan/' . $id;

        if ($this->isPost()) {
            $this->validateCsrf();
            $this->runAction(function () use ($id, $member) {
                $this->familyModel->changeMarital(
                    $id,
                    $this->post('marital_status', ''),
                    $this->postDate('date'),
                    $this->post('note'),
                    (int) $this->post('spouse_id') ?: null
                );
                ActivityLog::log(auth('id'), 'member_marital', "Mengubah status pernikahan {$member['full_name']}");
                return 'Status pernikahan berhasil diubah.';
            }, $back, (int) $row['family_id']);
            return;
        }

        $this->view('admin/members/marital', [
            'title' => 'Ubah Status Pernikahan - ' . APP_NAME,
            'member' => $member,
            'row' => $row,
            'family' => $this->familyModel->findSummary((int) $row['family_id']),
            'spouse' => $member['spouse_id'] ? $this->memberModel->find((int) $member['spouse_id']) : null,
        ]);
    }

    /**
     * Pindahkan anggota ke KK lain / KK baru
     */
    public function move(int $id = 0): void
    {
        [$member, $row] = $this->findMemberOrRedirect($id, true);
        $back = 'admin/jemaat/anggota/pindah/' . $id;

        if ($this->isPost()) {
            $this->validateCsrf();
            $this->runAction(function () use ($id, $member, $row) {
                $target = $this->post('target') === 'existing' ? ((int) $this->post('target_family_id') ?: null) : null;
                if ($this->post('target') === 'existing' && !$target) {
                    throw new \DomainException('Pilih KK tujuan.');
                }
                $familyId = $this->familyModel->moveMember(
                    $id,
                    $target,
                    $this->post('relationship', 'other'),
                    $this->postDate('date'),
                    $this->post('reason', 'moved_family'),
                    $this->post('note'),
                    $this->post('new_address'),
                    (int) $this->post('new_head_id') ?: null,
                    $this->postRelationships()
                );
                ActivityLog::log(auth('id'), 'move_member', "Memindahkan {$member['full_name']} dari KK #{$row['family_id']} ke KK #{$familyId}");
                return ['Anggota berhasil dipindahkan.', $familyId];
            }, $back, (int) $row['family_id']);
            return;
        }

        $this->view('admin/members/move', [
            'title' => 'Pindahkan KK - ' . APP_NAME,
            'member' => $member,
            'row' => $row,
            'family' => $this->familyModel->findSummary((int) $row['family_id']),
            'headPicker' => $this->headPickerData($member, $row),
        ]);
    }

    /**
     * Jadikan kepala keluarga
     */
    public function head(int $id = 0): void
    {
        [$member, $row] = $this->findMemberOrRedirect($id, true);
        $back = 'admin/jemaat/anggota/kepala/' . $id;

        if ($row['relationship'] === 'head') {
            setFlash('error', "{$member['full_name']} sudah menjadi kepala keluarga.");
            $this->redirect('admin/jemaat/detail/' . $row['family_id']);
            return;
        }

        if ($this->isPost()) {
            $this->validateCsrf();
            $this->runAction(function () use ($id, $member) {
                $this->familyModel->makeHead($id, $this->postRelationships(), $this->postDate('date'));
                ActivityLog::log(auth('id'), 'change_head', "Menjadikan {$member['full_name']} kepala keluarga");
                return "{$member['full_name']} sekarang kepala keluarga.";
            }, $back, (int) $row['family_id']);
            return;
        }

        $this->view('admin/members/head', [
            'title' => 'Jadikan Kepala Keluarga - ' . APP_NAME,
            'member' => $member,
            'row' => $row,
            'family' => $this->familyModel->findSummary((int) $row['family_id']),
            'others' => array_values(array_filter(
                $this->membership->activeMembers((int) $row['family_id']),
                fn($m) => (int) $m['id'] !== $id
            )),
        ]);
    }

    /**
     * Catat pernikahan
     */
    public function marry(int $id = 0): void
    {
        [$member, $row] = $this->findMemberOrRedirect($id, true);
        $back = 'admin/jemaat/anggota/menikah/' . $id;

        if ($this->isPost()) {
            $this->validateCsrf();
            $this->runAction(function () use ($id, $member) {
                $spouseId = null;
                $newSpouse = null;
                if ($this->post('spouse_mode') === 'new') {
                    $newSpouse = $this->personFromInput($this->post('spouse') ?: []);
                    $newSpouse['marital_status'] = 'single';
                    $errors = $this->validatePerson($newSpouse, 'Pasangan');
                    if ($errors) {
                        throw new \DomainException(implode(' ', $errors));
                    }
                } else {
                    $spouseId = (int) $this->post('spouse_id') ?: null;
                }

                $familyId = $this->familyModel->marry(
                    $id,
                    $spouseId,
                    $newSpouse,
                    $this->postDate('date'),
                    $this->post('mode', 'new_family'),
                    $this->post('head_choice', 'member'),
                    $this->post('note'),
                    $this->post('new_address')
                );
                ActivityLog::log(auth('id'), 'member_marry', "Mencatat pernikahan {$member['full_name']}");
                return ['Pernikahan berhasil dicatat.', $familyId];
            }, $back, (int) $row['family_id']);
            return;
        }

        $this->view('admin/members/marry', [
            'title' => 'Catat Pernikahan - ' . APP_NAME,
            'member' => $member,
            'row' => $row,
            'family' => $this->familyModel->findSummary((int) $row['family_id']),
        ]);
    }

    /**
     * Catat perceraian
     */
    public function divorce(int $id = 0): void
    {
        [$member, $row] = $this->findMemberOrRedirect($id, true);
        $back = 'admin/jemaat/anggota/cerai/' . $id;

        if ($member['marital_status'] !== 'married') {
            setFlash('error', 'Perceraian hanya bisa dicatat untuk jemaat yang berstatus Menikah.');
            $this->redirect('admin/jemaat/detail/' . $row['family_id']);
            return;
        }

        if ($this->isPost()) {
            $this->validateCsrf();
            $this->runAction(function () use ($id, $member) {
                $leaver = $this->post('leaver', '');
                $target = $this->post('target') === 'existing' ? ((int) $this->post('target_family_id') ?: null) : null;
                if ($leaver !== '' && $this->post('target') === 'existing' && !$target) {
                    throw new \DomainException('Pilih KK tujuan.');
                }

                $familyId = $this->familyModel->divorce(
                    $id,
                    $this->postDate('date'),
                    $this->post('note'),
                    $leaver !== '' ? (int) $leaver : null,
                    $target,
                    $this->post('relationship', 'other'),
                    (array) ($this->post('children') ?: []),
                    $this->post('new_address')
                );
                ActivityLog::log(auth('id'), 'member_divorce', "Mencatat perceraian {$member['full_name']}");
                return ['Perceraian berhasil dicatat.', $familyId];
            }, $back, (int) $row['family_id']);
            return;
        }

        $spouse = $member['spouse_id'] ? $this->memberModel->find((int) $member['spouse_id']) : null;
        $spouseRow = $spouse ? $this->membership->current((int) $spouse['id']) : null;

        $this->view('admin/members/divorce', [
            'title' => 'Catat Perceraian - ' . APP_NAME,
            'member' => $member,
            'row' => $row,
            'family' => $this->familyModel->findSummary((int) $row['family_id']),
            'spouse' => $spouse,
            'spouseInSameFamily' => $spouseRow && (int) $spouseRow['family_id'] === (int) $row['family_id'],
            'children' => array_values(array_filter(
                $this->membership->activeMembers((int) $row['family_id']),
                fn($m) => $m['relationship'] === 'child'
            )),
        ]);
    }

    // =====================================================
    // HAPUS PERMANEN (SUPER ADMIN)
    // =====================================================

    /**
     * Hapus permanen satu orang (salah input)
     */
    public function deleteMember(int $id = 0): void
    {
        if (!$this->isPost()) {
            $this->redirect('admin/jemaat');
            return;
        }
        $this->validateCsrf();
        RoleMiddleware::requireSuperAdmin();

        [$member, $row] = $this->findMemberOrRedirect($id);
        if (!$this->confirmName($member['full_name'])) {
            setFlash('error', 'Nama konfirmasi tidak sesuai. Data tidak dihapus.');
            $this->redirect('admin/jemaat/detail/' . $row['family_id']);
            return;
        }

        try {
            $familyId = $this->familyModel->deleteMember($id);
            ActivityLog::log(auth('id'), 'delete_member', "Menghapus permanen jemaat: {$member['full_name']}");
            setFlash('success', "Data {$member['full_name']} berhasil dihapus permanen.");
            $this->redirect($familyId ? 'admin/jemaat/detail/' . $familyId : 'admin/jemaat');
        } catch (\DomainException $e) {
            setFlash('error', $e->getMessage());
            $this->redirect('admin/jemaat/detail/' . $row['family_id']);
        } catch (\Exception $e) {
            error_log('deleteMember: ' . $e->getMessage());
            setFlash('error', 'Gagal menghapus data jemaat.');
            $this->redirect('admin/jemaat/detail/' . $row['family_id']);
        }
    }

    /**
     * Hapus permanen KK (salah input)
     */
    public function deleteFamily(int $id = 0): void
    {
        if (!$this->isPost()) {
            $this->redirect('admin/jemaat');
            return;
        }
        $this->validateCsrf();
        RoleMiddleware::requireSuperAdmin();

        $family = $this->findFamilyOrRedirect($id);
        if (!$this->confirmName($family['family_code'])) {
            setFlash('error', 'No. KK konfirmasi tidak sesuai. Data tidak dihapus.');
            $this->redirect('admin/jemaat/detail/' . $id);
            return;
        }

        try {
            $this->familyModel->deleteFamily($id);
            ActivityLog::log(auth('id'), 'delete_family', "Menghapus permanen {$family['family_code']} ({$family['head_name']})");
            setFlash('success', "{$family['family_code']} berhasil dihapus permanen.");
            $this->redirect('admin/jemaat');
        } catch (\DomainException $e) {
            setFlash('error', $e->getMessage());
            $this->redirect('admin/jemaat/detail/' . $id);
        } catch (\Exception $e) {
            error_log('deleteFamily: ' . $e->getMessage());
            setFlash('error', 'Gagal menghapus KK.');
            $this->redirect('admin/jemaat/detail/' . $id);
        }
    }

    // =====================================================
    // HELPER
    // =====================================================

    /**
     * Jalankan aksi; sukses → detail KK, gagal → kembali ke form dengan pesan
     *
     * @param callable $action return string pesan, atau [pesan, family_id tujuan]
     */
    private function runAction(callable $action, string $back, int $familyId): void
    {
        try {
            $result = $action();
            [$message, $target] = is_array($result) ? $result : [$result, $familyId];
            setFlash('success', $message);
            $this->clearOldInput();
            $this->redirect('admin/jemaat/detail/' . ($target ?: $familyId));
        } catch (\DomainException $e) {
            $this->failBack([$e->getMessage()], $back);
        } catch (\Exception $e) {
            error_log('MemberController: ' . $e->getMessage());
            $this->failBack(['Terjadi kesalahan saat menyimpan perubahan.'], $back);
        }
    }

    private function failBack(array $errors, string $url): void
    {
        setFlash('error', implode('<br>', array_map('e', $errors)));
        $this->saveOldInput();
        $this->redirect($url);
    }

    private function findFamilyOrRedirect(int $id): array
    {
        $family = $id ? $this->familyModel->findSummary($id) : false;
        if (!$family) {
            setFlash('error', 'Data KK tidak ditemukan.');
            $this->redirect('admin/jemaat');
        }
        return $family;
    }

    /**
     * @param bool $mustBeActive Aksi yang hanya untuk anggota aktif di KK
     * @return array [member, keanggotaan (aktif, atau terakhir bila non-aktif)]
     */
    private function findMemberOrRedirect(int $id, bool $mustBeActive = false): array
    {
        $member = $id ? $this->memberModel->find($id) : false;
        $row = $member ? ($this->membership->current($id) ?: $this->membership->latest($id)) : false;

        if (!$member || !$row) {
            setFlash('error', 'Data jemaat tidak ditemukan.');
            $this->redirect('admin/jemaat');
        }
        if ($mustBeActive && $row['left_at'] !== null) {
            setFlash('error', "{$member['full_name']} tidak aktif di KK mana pun. Aktifkan kembali lewat menu Ubah Status.");
            $this->redirect('admin/jemaat/detail/' . $row['family_id']);
        }

        return [$member, $row];
    }

    /**
     * Data untuk memilih kepala baru bila orang ini kepala & KK masih punya anggota aktif lain
     */
    private function headPickerData(array $member, array $row): ?array
    {
        if ($row['left_at'] !== null || $row['relationship'] !== 'head') {
            return null;
        }
        $others = array_values(array_filter(
            $this->membership->activeMembers((int) $row['family_id']),
            fn($m) => (int) $m['id'] !== (int) $member['id']
        ));
        if (!$others) {
            return null;
        }

        return [
            'others' => $others,
            'suggested' => $this->familyModel->suggestHead((int) $row['family_id'], (int) $member['id']),
        ];
    }

    /**
     * Ambil data pribadi dari input form
     */
    private function personFromInput(array $input): array
    {
        $person = [];
        foreach (self::PERSON_FIELDS as $field) {
            $value = isset($input[$field]) && !is_array($input[$field]) ? trim((string) $input[$field]) : '';
            $person[$field] = $value === '' ? null : $value;
        }
        $person['full_name'] = $person['full_name'] ?? '';
        return $person;
    }

    private function validatePerson(array $person, string $label): array
    {
        $errors = [];

        if ($person['full_name'] === '') {
            $errors[] = "{$label}: nama wajib diisi.";
        }
        if (!in_array($person['gender'], ['M', 'F'], true)) {
            $errors[] = "{$label}: jenis kelamin wajib dipilih.";
        }
        if (!isset(Member::maritalOptions()[$person['marital_status'] ?? ''])) {
            $errors[] = "{$label}: status pernikahan wajib dipilih.";
        }
        if (!empty($person['email']) && !Security::validateEmail($person['email'])) {
            $errors[] = "{$label}: format email tidak valid.";
        }
        foreach (['birth_date' => 'tanggal lahir', 'baptism_date' => 'tanggal baptis', 'membership_date' => 'tanggal bergabung'] as $field => $name) {
            if (!empty($person[$field]) && !$this->isValidDate($person[$field])) {
                $errors[] = "{$label}: {$name} tidak valid.";
            }
        }
        if (!empty($person['birth_date']) && $this->isValidDate($person['birth_date']) && $person['birth_date'] > date('Y-m-d')) {
            $errors[] = "{$label}: tanggal lahir tidak boleh di masa depan.";
        }

        return $errors;
    }

    private function isValidDate(string $date): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    /**
     * Tanggal kejadian dari form; kosong/tidak valid → hari ini
     */
    private function postDate(string $key): string
    {
        $date = (string) $this->post($key, '');
        return $this->isValidDate($date) ? $date : date('Y-m-d');
    }

    /**
     * Penyesuaian hubungan anggota: rel[member_id] = relationship
     */
    private function postRelationships(): array
    {
        $result = [];
        foreach ((array) ($this->post('rel') ?: []) as $memberId => $relationship) {
            if ((int) $memberId > 0 && is_string($relationship) && $relationship !== '') {
                $result[(int) $memberId] = $relationship;
            }
        }
        return $result;
    }

    private function confirmName(string $expected): bool
    {
        return mb_strtolower(trim((string) $this->post('confirm_name'))) === mb_strtolower(trim($expected));
    }
}
