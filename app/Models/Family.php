<?php
namespace App\Models;

use Core\Model;
use Core\Database;

/**
 * =========================================================
 * Family Model (Kartu Keluarga)
 * =========================================================
 *
 * - Kondisi keluarga TIDAK disimpan: dihitung saat query dari anggota
 *   aktif (umur anak berubah seiring waktu, nilai tersimpan bisa basi).
 * - Semua operasi yang menyentuh beberapa tabel berjalan dalam satu
 *   transaksi. Pelanggaran aturan bisnis dilempar sebagai
 *   \DomainException berisi pesan untuk ditampilkan ke admin.
 */
class Family extends Model
{
    protected $table = 'families';

    protected $fillable = [
        'family_code',
        'address',
        'phone',
        'origin_family_id',
        'notes',
        'created_by',
    ];

    /** Kategori utama kondisi keluarga (eksklusif) */
    const CONDITIONS = [
        'single' => 'Single',
        'married_no_child' => 'Menikah, belum punya anak',
        'married_with_child' => 'Menikah, punya anak',
        'widowed_no_child' => 'Janda/Duda tanpa anak',
        'widowed_with_child' => 'Janda/Duda dengan anak',
        'other' => 'Lainnya',
        'incomplete' => 'Belum lengkap',
        'archive' => 'Arsip',
    ];

    /** Warna tag (class .t-*) & ikon per kondisi */
    const CONDITION_TONES = [
        'single' => ['t-teal', 'person'],
        'married_no_child' => ['t-blue', 'heart'],
        'married_with_child' => ['t-green', 'people'],
        'widowed_no_child' => ['t-purple', 'person-heart'],
        'widowed_with_child' => ['t-purple', 'people'],
        'other' => ['t-grey', 'house'],
        'incomplete' => ['t-yellow', 'exclamation-circle'],
        'archive' => ['t-dark', 'archive'],
    ];

    /** Penanda tambahan (boleh lebih dari satu) */
    const FLAGS = [
        'adult_child' => 'Anak dewasa serumah',
        'married_child' => 'Anak sudah berkeluarga',
    ];

    private $memberModel;
    private $membership;

    public function __construct()
    {
        parent::__construct();
        $this->memberModel = new Member();
        $this->membership = new FamilyMember();
    }

    // =====================================================
    // QUERY & REKAP
    // =====================================================

    /**
     * SELECT ringkasan per KK: kepala, jumlah anggota aktif/arsip, dan kondisi turunan
     */
    private function summarySql(): string
    {
        $adultAge = Member::ADULT_AGE;

        return "SELECT f.id, f.family_code, f.address, f.phone, f.notes, f.origin_family_id, f.created_at,
                       h.id AS head_id, h.full_name AS head_name, h.gender AS head_gender,
                       h.marital_status AS head_marital, h.status AS head_status,
                       COALESCE(s.active_count, 0) AS active_count,
                       COALESCE(s.archived_count, 0) AS archived_count,
                       COALESCE(s.child_count, 0) AS child_count,
                       COALESCE(s.adult_child_count, 0) AS adult_child_count,
                       COALESCE(s.married_child_count, 0) AS married_child_count,
                       CASE
                           WHEN COALESCE(s.active_count, 0) = 0 THEN 'archive'
                           WHEN h.marital_status IS NULL THEN 'incomplete'
                           WHEN h.marital_status = 'widowed'
                               THEN IF(COALESCE(s.child_count, 0) > 0, 'widowed_with_child', 'widowed_no_child')
                           WHEN h.marital_status = 'married'
                               THEN IF(COALESCE(s.child_count, 0) > 0, 'married_with_child', 'married_no_child')
                           WHEN s.active_count = 1 THEN 'single'
                           ELSE 'other'
                       END AS family_condition
                FROM families f
                LEFT JOIN (
                    SELECT fm.family_id,
                           COUNT(DISTINCT CASE WHEN fm.left_at IS NULL THEN fm.member_id END) AS active_count,
                           COUNT(DISTINCT CASE WHEN fm.left_reason = 'status_change' AND m.status <> 'active'
                                               THEN fm.member_id END) AS archived_count,
                           COUNT(DISTINCT CASE WHEN fm.left_at IS NULL AND fm.relationship = 'child'
                                               THEN fm.member_id END) AS child_count,
                           COUNT(DISTINCT CASE WHEN fm.left_at IS NULL AND fm.relationship = 'child'
                                                    AND TIMESTAMPDIFF(YEAR, m.birth_date, CURDATE()) >= {$adultAge}
                                               THEN fm.member_id END) AS adult_child_count,
                           COUNT(DISTINCT CASE WHEN fm.left_reason = 'married_out' AND fm.relationship = 'child'
                                               THEN fm.member_id END) AS married_child_count
                    FROM family_members fm
                    JOIN members m ON m.id = fm.member_id
                    GROUP BY fm.family_id
                ) s ON s.family_id = f.id
                LEFT JOIN members h ON h.id = (
                    SELECT x.member_id FROM family_members x
                    WHERE x.family_id = f.id AND x.relationship = 'head'
                    ORDER BY x.left_at IS NULL DESC, x.left_at DESC, x.id DESC
                    LIMIT 1
                )";
    }

    /**
     * Daftar KK dengan filter & pagination
     *
     * @param array $filters search, status, marital, condition, flag
     */
    public function getWithPagination(int $page = 1, int $perPage = ITEMS_PER_PAGE, array $filters = []): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;
        $params = [];
        $conditions = [];

        $status = $filters['status'] ?? 'active';
        $search = trim((string) ($filters['search'] ?? ''));

        if ($search !== '') {
            // Cocok ke no. KK / telp KK / nama & telp anggota (aktif atau arsip di KK ini)
            $conditions[] = "(fs.family_code LIKE :s1 OR fs.phone LIKE :s2 OR EXISTS (
                SELECT 1 FROM family_members sf JOIN members sm ON sm.id = sf.member_id
                WHERE sf.family_id = fs.id
                  AND (sf.left_at IS NULL OR sf.left_reason = 'status_change')
                  AND (sm.full_name LIKE :s3 OR sm.phone LIKE :s4)))";
            foreach (['s1', 's2', 's3', 's4'] as $key) {
                $params[$key] = '%' . $search . '%';
            }
        }

        // Orang yang "dilihat" di KK: anggota aktif, atau arsip berstatus tertentu
        if ($status === 'all') {
            $scope = "(pf.left_at IS NULL OR (pf.left_reason = 'status_change' AND pm.status <> 'active'))";
        } elseif ($status !== 'active' && isset(Member::STATUSES[$status])) {
            $scope = "pf.left_reason = 'status_change' AND pm.status = :status
                      AND NOT EXISTS (SELECT 1 FROM family_members pl WHERE pl.member_id = pf.member_id AND pl.id > pf.id)";
            $params['status'] = $status;
        } else {
            $scope = "pf.left_at IS NULL";
        }

        $maritalSql = [
            'single' => "pm.marital_status = 'single'",
            'married' => "pm.marital_status = 'married'",
            'janda' => "pm.marital_status = 'widowed' AND pm.gender = 'F'",
            'duda' => "pm.marital_status = 'widowed' AND pm.gender = 'M'",
            'none' => "pm.marital_status IS NULL",
        ];
        $marital = $filters['marital'] ?? '';

        if ($status !== 'all' || isset($maritalSql[$marital])) {
            $personCond = $scope . (isset($maritalSql[$marital]) ? ' AND ' . $maritalSql[$marital] : '');
            $conditions[] = "EXISTS (SELECT 1 FROM family_members pf JOIN members pm ON pm.id = pf.member_id
                                     WHERE pf.family_id = fs.id AND {$personCond})";
        }

        if (!empty($filters['condition']) && isset(self::CONDITIONS[$filters['condition']])) {
            $conditions[] = 'fs.family_condition = :cond';
            $params['cond'] = $filters['condition'];
        }

        if (($filters['flag'] ?? '') === 'adult_child') {
            $conditions[] = 'fs.adult_child_count > 0';
        } elseif (($filters['flag'] ?? '') === 'married_child') {
            $conditions[] = 'fs.married_child_count > 0';
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
        $base = "FROM ({$this->summarySql()}) fs {$where}";

        $total = (int) Database::fetchColumn("SELECT COUNT(*) {$base}", $params);

        $stmt = Database::getInstance()->prepare(
            "SELECT fs.* {$base} ORDER BY fs.active_count = 0, fs.head_name ASC, fs.id ASC LIMIT :limit OFFSET :offset"
        );
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data' => $stmt->fetchAll(),
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => (int) ceil($total / $perPage),
        ];
    }

    /**
     * Ringkasan satu KK (kepala, jumlah anggota, kondisi)
     *
     * @return array|false
     */
    public function findSummary(int $id)
    {
        return Database::fetch("SELECT * FROM ({$this->summarySql()}) fs WHERE fs.id = :id", ['id' => $id]);
    }

    /**
     * Anggota beberapa KK sekaligus (1 query), dikelompokkan per KK.
     * Tiap orang muncul sekali per KK: keanggotaan terbuka didahulukan.
     *
     * @param int[] $familyIds
     * @param bool $withMovedOut Sertakan orang yang sudah pindah KK (untuk detail KK)
     * @return array [family_id => [anggota...]]
     */
    public function membersFor(array $familyIds, bool $withMovedOut = false): array
    {
        $familyIds = array_values(array_unique(array_map('intval', $familyIds)));
        if (!$familyIds) {
            return [];
        }

        $in = implode(',', $familyIds);
        $rows = Database::fetchAll(
            "SELECT m.*, fm.id AS membership_id, fm.family_id, fm.relationship, fm.joined_at, fm.left_at, fm.left_reason,
                    cf.id AS current_family_id, cf.family_code AS current_family_code
             FROM family_members fm
             JOIN members m ON m.id = fm.member_id
             LEFT JOIN family_members cfm ON cfm.member_id = m.id AND cfm.left_at IS NULL
             LEFT JOIN families cf ON cf.id = cfm.family_id
             WHERE fm.family_id IN ({$in})
             ORDER BY fm.family_id, fm.left_at IS NULL DESC, fm.left_at DESC, " . FamilyMember::ORDER_SQL . ",
                      m.birth_date IS NULL, m.birth_date ASC, fm.id DESC"
        );

        $grouped = array_fill_keys($familyIds, []);
        $seen = [];
        foreach ($rows as $row) {
            $key = $row['family_id'] . ':' . $row['id'];
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;

            $row['is_current'] = $row['left_at'] === null;
            $row['is_archived'] = !$row['is_current'] && $row['left_reason'] === 'status_change' && $row['status'] !== 'active';
            if (!$row['is_current'] && !$row['is_archived'] && !$withMovedOut) {
                continue;
            }
            $grouped[$row['family_id']][] = $row;
        }

        return $grouped;
    }

    /**
     * KK turunan (dibentuk dari KK ini: anak menikah / cerai)
     */
    public function descendants(int $familyId): array
    {
        return Database::fetchAll(
            "SELECT * FROM ({$this->summarySql()}) fs WHERE fs.origin_family_id = :id ORDER BY fs.created_at",
            ['id' => $familyId]
        );
    }

    /**
     * Rekap KK: total KK aktif, per kondisi, per penanda
     */
    public function getRecap(): array
    {
        $rows = Database::fetchAll(
            "SELECT fs.family_condition, COUNT(*) AS total,
                    SUM(fs.adult_child_count > 0) AS adult_child,
                    SUM(fs.married_child_count > 0 AND fs.active_count > 0) AS married_child
             FROM ({$this->summarySql()}) fs
             GROUP BY fs.family_condition"
        );

        $recap = [
            'total' => 0,
            'by_condition' => array_fill_keys(array_keys(self::CONDITIONS), 0),
            'flags' => array_fill_keys(array_keys(self::FLAGS), 0),
        ];
        foreach ($rows as $row) {
            $recap['by_condition'][$row['family_condition']] = (int) $row['total'];
            if ($row['family_condition'] !== 'archive') {
                $recap['total'] += (int) $row['total'];
            }
            $recap['flags']['adult_child'] += (int) $row['adult_child'];
            $recap['flags']['married_child'] += (int) $row['married_child'];
        }

        return $recap;
    }

    /**
     * TOTAL KK = KK yang punya minimal 1 anggota aktif
     */
    public function countActive(): int
    {
        return (int) Database::fetchColumn(
            "SELECT COUNT(DISTINCT family_id) FROM family_members WHERE left_at IS NULL"
        );
    }

    /**
     * Cari KK untuk kolom pencarian (tujuan pindah / cerai)
     */
    public function searchActive(string $keyword, ?int $excludeId = null): array
    {
        $sql = "SELECT fs.id, fs.family_code, fs.head_name, fs.address, fs.active_count
                FROM ({$this->summarySql()}) fs
                WHERE fs.active_count > 0 AND (fs.family_code LIKE :k1 OR fs.head_name LIKE :k2)";
        $params = ['k1' => '%' . $keyword . '%', 'k2' => '%' . $keyword . '%'];
        if ($excludeId) {
            $sql .= ' AND fs.id <> :ex';
            $params['ex'] = $excludeId;
        }
        return Database::fetchAll($sql . ' ORDER BY fs.head_name LIMIT 15', $params);
    }

    /**
     * Usulan kepala keluarga baru bila kepala sekarang keluar:
     * pasangan aktif → anak tertua ≥17 → anggota tertua lainnya
     *
     * @return int|null member_id
     */
    public function suggestHead(int $familyId, int $excludeMemberId): ?int
    {
        $candidates = array_filter(
            $this->membership->activeMembers($familyId),
            fn($m) => (int) $m['id'] !== $excludeMemberId
        );
        if (!$candidates) {
            return null;
        }

        foreach ($candidates as $m) {
            if ($m['relationship'] === 'spouse') {
                return (int) $m['id'];
            }
        }

        $byAge = $candidates;
        usort($byAge, function ($a, $b) {
            // Tanggal lahir kosong di belakang; yang lebih tua di depan
            if (!$a['birth_date'] || !$b['birth_date']) {
                return $a['birth_date'] ? -1 : ($b['birth_date'] ? 1 : 0);
            }
            return strcmp($a['birth_date'], $b['birth_date']);
        });

        foreach ($byAge as $m) {
            if ($m['relationship'] === 'child' && (Member::age($m['birth_date']) ?? 0) >= Member::ADULT_AGE) {
                return (int) $m['id'];
            }
        }

        return (int) $byAge[0]['id'];
    }

    // =====================================================
    // OPERASI (semua dalam transaksi)
    // =====================================================

    /**
     * Buat keluarga baru: KK + kepala + anggota dalam satu transaksi
     *
     * @param array $family address, phone, notes
     * @param array $head data pribadi kepala (wajib marital_status)
     * @param array $others [['relationship' => ..., data pribadi...], ...]
     * @return int ID KK
     */
    public function createFamily(array $family, array $head, array $others, string $date): int
    {
        return $this->transaction(function () use ($family, $head, $others, $date) {
            $familyId = $this->createFamilyRow($family);

            $headId = $this->createPerson($head);
            $this->joinFamily($familyId, $headId, 'head', $date, 'Data baru');

            foreach ($others as $person) {
                $relationship = $person['relationship'];
                $memberId = $this->createPerson($person);
                $this->joinFamily($familyId, $memberId, $relationship, $date, 'Data baru');

                if ($relationship === 'spouse') {
                    $this->linkSpouses($headId, $memberId, $date, $familyId, 'Data baru');
                }
            }

            return $familyId;
        });
    }

    /**
     * Ubah data KK (alamat, telepon, catatan)
     */
    public function updateFamily(int $id, array $data): void
    {
        $this->update($id, [
            'address' => $data['address'] ?? null,
            'phone' => $data['phone'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);
    }

    /**
     * Tambah orang BARU ke KK
     *
     * @return int ID orang
     */
    public function addNewMember(int $familyId, array $person, string $relationship, string $date): int
    {
        return $this->transaction(function () use ($familyId, $person, $relationship, $date) {
            $this->assertFamilyActive($familyId);
            $this->assertNotHeadRelationship($relationship);

            $memberId = $this->createPerson($person);
            $this->joinFamily($familyId, $memberId, $relationship, $date, 'Anggota baru');

            if ($relationship === 'spouse') {
                $head = $this->membership->headOf($familyId);
                $this->linkSpouses((int) $head['member_id'], $memberId, $date, $familyId, 'Anggota baru');
            }

            return $memberId;
        });
    }

    /**
     * Ubah status jemaat (Aktif / Tidak Aktif / Meninggal / Pindah Gereja / Pindah Agama)
     *
     * @param int|null $newHeadId Wajib bila yang keluar adalah kepala & KK masih punya anggota aktif
     * @param array $relationships [member_id => relationship] penyesuaian hubungan setelah ganti kepala
     * @param string $reactivateTo 'last' (KK terakhir) atau 'new' (KK baru) saat diaktifkan kembali
     */
    public function changeStatus(
        int $memberId,
        string $status,
        string $date,
        ?string $note,
        ?int $newHeadId = null,
        array $relationships = [],
        string $reactivateTo = 'last'
    ): void {
        $this->transaction(function () use ($memberId, $status, $date, $note, $newHeadId, $relationships, $reactivateTo) {
            $member = $this->findMember($memberId);
            if (!isset(Member::STATUSES[$status])) {
                throw new \DomainException('Status jemaat tidak valid.');
            }
            if ($member['status'] === $status) {
                throw new \DomainException('Status jemaat tidak berubah.');
            }

            $current = $this->membership->current($memberId);
            $latest = $this->membership->latest($memberId);
            $familyId = $current ? (int) $current['family_id'] : ($latest ? (int) $latest['family_id'] : null);

            $this->memberModel->update($memberId, [
                'status' => $status,
                'status_date' => $date,
                'status_note' => $note ?: null,
            ]);
            MemberStatusLog::add($memberId, $familyId, 'status', $member['status'], $status, $date, null, $note);

            if ($status !== 'active') {
                // Keluar dari KK sebagai arsip; kepala diganti bila perlu
                if ($current) {
                    $this->leaveFamily($current, $date, 'status_change', $newHeadId, $relationships);
                }

                // Pasangan meninggal → yang hidup jadi Janda/Duda
                if ($status === 'deceased' && $member['spouse_id']) {
                    $spouseId = (int) $member['spouse_id'];
                    $this->unlinkSpouses($memberId, $spouseId);
                    $spouseMembership = $this->membership->current($spouseId);
                    $this->setMarital(
                        $spouseId, 'widowed', $date, 'Pasangan meninggal', null, $memberId,
                        $spouseMembership ? (int) $spouseMembership['family_id'] : $familyId
                    );
                }
                return;
            }

            // Diaktifkan kembali: buka keanggotaan baru
            if ($current) {
                return;
            }
            if ($reactivateTo === 'new' || !$latest) {
                $newFamilyId = $this->createFamilyRow(['address' => $member['address']], $latest ? (int) $latest['family_id'] : null);
                $this->joinFamily($newFamilyId, $memberId, 'head', $date, 'Aktif kembali');
                return;
            }

            $targetId = (int) $latest['family_id'];
            $relationship = $latest['relationship'];
            if (!$this->membership->headOf($targetId)) {
                $relationship = 'head';
            } elseif ($relationship === 'head') {
                $relationship = 'other';
            }
            $this->joinFamily($targetId, $memberId, $relationship, $date, 'Aktif kembali');
        });
    }

    /**
     * Ubah status pernikahan (koreksi / kasus di luar menikah-cerai-meninggal)
     *
     * @param int|null $spouseId Pasangan jemaat (opsional) bila status jadi Menikah
     */
    public function changeMarital(int $memberId, string $marital, string $date, ?string $note, ?int $spouseId = null): void
    {
        $this->transaction(function () use ($memberId, $marital, $date, $note, $spouseId) {
            $member = $this->findMember($memberId);
            if (!isset(Member::maritalOptions()[$marital])) {
                throw new \DomainException('Status pernikahan tidak valid.');
            }
            if ($member['spouse_id']) {
                throw new \DomainException('Orang ini tercatat menikah dengan jemaat lain. Gunakan menu Catat Perceraian, atau ubah status jemaat pasangannya menjadi Meninggal.');
            }

            $current = $this->membership->current($memberId);
            $familyId = $current ? (int) $current['family_id'] : null;

            if ($marital === 'married' && $spouseId) {
                $spouse = $this->findMember($spouseId);
                $this->assertCanMarry($member, $spouse);
                $this->linkSpouses($memberId, $spouseId, $date, $familyId, 'Koreksi data', $note);
                return;
            }

            if ($member['marital_status'] === $marital) {
                throw new \DomainException('Status pernikahan tidak berubah.');
            }
            $this->setMarital($memberId, $marital, $date, 'Koreksi data', $note, null, $familyId);
        });
    }

    /**
     * Jadikan anggota sebagai kepala keluarga (kepala lama tetap di KK)
     *
     * @param array $relationships [member_id => relationship], wajib berisi kepala lama
     */
    public function makeHead(int $memberId, array $relationships, string $date): void
    {
        $this->transaction(function () use ($memberId, $relationships, $date) {
            $row = $this->membership->current($memberId);
            if (!$row) {
                throw new \DomainException('Orang ini tidak aktif di KK mana pun.');
            }
            if ($row['relationship'] === 'head') {
                throw new \DomainException('Orang ini sudah menjadi kepala keluarga.');
            }

            $familyId = (int) $row['family_id'];
            $oldHead = $this->membership->headOf($familyId);
            if ($oldHead) {
                $oldRel = $relationships[$oldHead['member_id']] ?? '';
                $this->assertNotHeadRelationship($oldRel);
                $this->changeRelationship($oldHead, $oldRel, $date, 'Ganti kepala keluarga');
            }

            $this->promoteHead($familyId, $memberId, $relationships, $date);
        });
    }

    /**
     * Pindahkan anggota ke KK lain atau ke KK baru (dia jadi kepala)
     *
     * @param int|null $targetFamilyId null = buat KK baru
     */
    public function moveMember(
        int $memberId,
        ?int $targetFamilyId,
        string $relationship,
        string $date,
        string $reason,
        ?string $note,
        ?string $newAddress = null,
        ?int $newHeadId = null,
        array $relationships = []
    ): int {
        return $this->transaction(function () use ($memberId, $targetFamilyId, $relationship, $date, $reason, $note, $newAddress, $newHeadId, $relationships) {
            $current = $this->membership->current($memberId);
            if (!$current) {
                throw new \DomainException('Hanya jemaat aktif yang bisa dipindahkan.');
            }
            if (!isset(FamilyMember::LEFT_REASONS[$reason]) || $reason === 'status_change') {
                throw new \DomainException('Alasan pindah tidak valid.');
            }
            $oldFamilyId = (int) $current['family_id'];
            if ($targetFamilyId === $oldFamilyId) {
                throw new \DomainException('KK tujuan sama dengan KK sekarang.');
            }

            if ($targetFamilyId) {
                $this->assertFamilyActive($targetFamilyId);
                $this->assertNotHeadRelationship($relationship);
            }

            $this->leaveFamily($current, $date, $reason, $newHeadId, $relationships, $note);

            if (!$targetFamilyId) {
                $targetFamilyId = $this->createFamilyRow(['address' => $newAddress], $oldFamilyId);
                $relationship = 'head';
            }
            $this->joinFamily($targetFamilyId, $memberId, $relationship, $date, FamilyMember::LEFT_REASONS[$reason], $note);

            return $targetFamilyId;
        });
    }

    /**
     * Catat pernikahan
     *
     * @param int|null $spouseId Pasangan yang sudah jadi jemaat
     * @param array|null $newSpouse Data pasangan baru (bila bukan jemaat lama)
     * @param string $mode 'new_family' | 'join_member' (pasangan masuk KK orang ini) | 'join_spouse'
     * @param string $headChoice 'member' | 'spouse' (untuk mode new_family)
     * @return int ID KK tempat pasangan tercatat
     */
    public function marry(
        int $memberId,
        ?int $spouseId,
        ?array $newSpouse,
        string $date,
        string $mode,
        string $headChoice,
        ?string $note,
        ?string $newAddress = null
    ): int {
        return $this->transaction(function () use ($memberId, $spouseId, $newSpouse, $date, $mode, $headChoice, $note, $newAddress) {
            $member = $this->findMember($memberId);
            if ($member['status'] !== 'active') {
                throw new \DomainException('Hanya jemaat aktif yang bisa dicatat menikah.');
            }

            if ($spouseId) {
                $spouse = $this->findMember($spouseId);
            } elseif ($newSpouse) {
                $newSpouse['marital_status'] = 'single';
                $spouseId = $this->createPerson($newSpouse);
                $spouse = $this->findMember($spouseId);
            } else {
                throw new \DomainException('Pilih atau isi data pasangan.');
            }
            $this->assertCanMarry($member, $spouse);

            $memberRow = $this->membership->current($memberId);
            $spouseRow = $this->membership->current($spouseId);

            if ($mode === 'new_family') {
                $this->assertCanLeaveAsHead($memberRow, $member);
                $this->assertCanLeaveAsHead($spouseRow, $spouse);

                // KK asal: KK orang yang menikah (biasanya KK orang tuanya)
                $originId = $memberRow ? (int) $memberRow['family_id'] : ($spouseRow ? (int) $spouseRow['family_id'] : null);
                foreach ([$memberRow, $spouseRow] as $row) {
                    if ($row) {
                        $this->leaveFamily($row, $date, 'married_out', null, [], $note);
                    }
                }

                $familyId = $this->createFamilyRow(['address' => $newAddress], $originId);
                $headId = $headChoice === 'spouse' ? $spouseId : $memberId;
                $otherId = $headId === $memberId ? $spouseId : $memberId;
                $this->joinFamily($familyId, $headId, 'head', $date, 'Menikah', $note);
                $this->joinFamily($familyId, $otherId, 'spouse', $date, 'Menikah', $note);
            } else {
                // Satu orang masuk ke KK pasangannya; pasangan itu harus kepala KK tersebut
                [$hostId, $hostRow, $guestId, $guestRow, $guest] = $mode === 'join_spouse'
                    ? [$spouseId, $spouseRow, $memberId, $memberRow, $member]
                    : [$memberId, $memberRow, $spouseId, $spouseRow, $spouse];

                if (!$hostRow || $hostRow['relationship'] !== 'head') {
                    throw new \DomainException('Untuk bergabung ke KK pasangan, pasangan tersebut harus menjadi kepala KK-nya. Pilih "Buat KK baru" atau jadikan dia kepala keluarga dulu.');
                }
                $familyId = (int) $hostRow['family_id'];
                if ($guestRow) {
                    $this->assertCanLeaveAsHead($guestRow, $guest);
                    $this->leaveFamily($guestRow, $date, 'married_out', null, [], $note);
                }
                $this->joinFamily($familyId, $guestId, 'spouse', $date, 'Menikah', $note);
            }

            $this->linkSpouses($memberId, $spouseId, $date, $familyId, 'Menikah', $note);

            return $familyId;
        });
    }

    /**
     * Catat perceraian: keduanya jadi Janda/Duda, salah satu (opsional) keluar KK
     *
     * @param int|null $leaverId Yang keluar KK (null = tidak ada yang pindah)
     * @param int|null $targetFamilyId null = buat KK baru untuk yang keluar
     * @param int[] $childIds Anak yang ikut pindah bersama yang keluar
     * @return int ID KK yang ditampilkan setelahnya
     */
    public function divorce(
        int $memberId,
        string $date,
        ?string $note,
        ?int $leaverId,
        ?int $targetFamilyId,
        string $relationship,
        array $childIds,
        ?string $newAddress = null
    ): int {
        return $this->transaction(function () use ($memberId, $date, $note, $leaverId, $targetFamilyId, $relationship, $childIds, $newAddress) {
            $member = $this->findMember($memberId);
            if ($member['marital_status'] !== 'married') {
                throw new \DomainException('Perceraian hanya bisa dicatat untuk jemaat yang berstatus Menikah.');
            }
            $spouseId = $member['spouse_id'] ? (int) $member['spouse_id'] : null;

            $memberRow = $this->membership->current($memberId);
            $resultFamilyId = $memberRow ? (int) $memberRow['family_id'] : 0;

            // 1. Status pernikahan keduanya → Janda/Duda (penyebab: cerai)
            if ($spouseId) {
                $this->unlinkSpouses($memberId, $spouseId);
            }
            $this->setMarital($memberId, 'widowed', $date, 'Cerai', $note, $spouseId, $resultFamilyId ?: null);
            if ($spouseId) {
                $spouseRow = $this->membership->current($spouseId);
                $this->setMarital($spouseId, 'widowed', $date, 'Cerai', $note, $memberId, $spouseRow ? (int) $spouseRow['family_id'] : null);
            }

            // 2. Yang keluar KK (opsional)
            if (!$leaverId) {
                return $resultFamilyId;
            }
            if ($leaverId !== $memberId && $leaverId !== $spouseId) {
                throw new \DomainException('Yang keluar KK harus salah satu dari pasangan yang bercerai.');
            }
            $leaverRow = $this->membership->current($leaverId);
            if (!$leaverRow) {
                return $resultFamilyId;
            }
            $oldFamilyId = (int) $leaverRow['family_id'];
            if ($targetFamilyId === $oldFamilyId) {
                throw new \DomainException('KK tujuan sama dengan KK sekarang.');
            }
            if ($targetFamilyId) {
                $this->assertFamilyActive($targetFamilyId);
                $this->assertNotHeadRelationship($relationship);
            }

            // Anak yang ikut harus anak aktif di KK yang sama
            $children = [];
            foreach (array_unique(array_map('intval', $childIds)) as $childId) {
                $childRow = $this->membership->current($childId);
                if (!$childRow || (int) $childRow['family_id'] !== $oldFamilyId || $childRow['relationship'] !== 'child') {
                    throw new \DomainException('Anak yang dipilih tidak terdaftar sebagai anak di KK ini.');
                }
                $children[] = $childRow;
            }
            foreach ($children as $childRow) {
                $this->leaveFamily($childRow, $date, 'divorce', null, [], $note);
            }

            // Kepala keluar → mantan pasangan (bila masih di KK) jadi kepala, selain itu usulan otomatis
            $newHeadId = null;
            if ($leaverRow['relationship'] === 'head') {
                $otherId = $leaverId === $memberId ? $spouseId : $memberId;
                $otherRow = $otherId ? $this->membership->current($otherId) : null;
                $newHeadId = $otherRow && (int) $otherRow['family_id'] === $oldFamilyId
                    ? $otherId
                    : $this->suggestHead($oldFamilyId, $leaverId);
            }
            $this->leaveFamily($leaverRow, $date, 'divorce', $newHeadId, [], $note);

            if (!$targetFamilyId) {
                $targetFamilyId = $this->createFamilyRow(['address' => $newAddress], $oldFamilyId);
                $relationship = 'head';
            }
            $this->joinFamily($targetFamilyId, $leaverId, $relationship, $date, 'Cerai', $note);

            $childRelationship = $relationship === 'head' ? 'child' : ($relationship === 'child' ? 'grandchild' : 'other');
            foreach ($children as $childRow) {
                $this->joinFamily($targetFamilyId, (int) $childRow['member_id'], $childRelationship, $date, 'Ikut orang tua (cerai)', $note);
            }

            return $resultFamilyId ?: $targetFamilyId;
        });
    }

    /**
     * Hapus permanen satu orang (khusus Super Admin, untuk salah input)
     *
     * @return int|null KK tempat orang ini terakhir tercatat (bila masih ada)
     */
    public function deleteMember(int $memberId): ?int
    {
        return $this->transaction(function () use ($memberId) {
            $member = $this->findMember($memberId);
            $current = $this->membership->current($memberId);
            if ($current && $current['relationship'] === 'head' && count($this->membership->activeMembers((int) $current['family_id'])) > 1) {
                throw new \DomainException("{$member['full_name']} adalah kepala keluarga yang masih punya anggota aktif. Jadikan anggota lain kepala keluarga dulu.");
            }

            $familyIds = array_map('intval', array_column(
                Database::fetchAll("SELECT DISTINCT family_id FROM family_members WHERE member_id = :id", ['id' => $memberId]),
                'family_id'
            ));

            // spouse_id pasangan & related_member_id di riwayat dikosongkan oleh FK (SET NULL), riwayat orang ini ikut terhapus (CASCADE)
            Database::delete('family_members', 'member_id = :id', ['id' => $memberId]);
            $this->memberModel->delete($memberId);

            // KK yang jadi kosong sama sekali ikut dihapus
            $remaining = null;
            foreach ($familyIds as $familyId) {
                $left = (int) Database::fetchColumn("SELECT COUNT(*) FROM family_members WHERE family_id = :id", ['id' => $familyId]);
                if ($left === 0) {
                    $this->delete($familyId);
                } elseif ($current && (int) $current['family_id'] === $familyId) {
                    $remaining = $familyId;
                }
            }

            return $remaining;
        });
    }

    /**
     * Hapus permanen KK beserta orang-orang yang riwayatnya hanya di KK ini (khusus Super Admin)
     */
    public function deleteFamily(int $familyId): void
    {
        $this->transaction(function () use ($familyId) {
            $memberIds = array_map('intval', array_column(
                Database::fetchAll("SELECT DISTINCT member_id FROM family_members WHERE family_id = :id", ['id' => $familyId]),
                'member_id'
            ));

            if ($memberIds) {
                $in = implode(',', $memberIds);
                $elsewhere = Database::fetchAll(
                    "SELECT DISTINCT m.full_name FROM family_members fm JOIN members m ON m.id = fm.member_id
                     WHERE fm.member_id IN ({$in}) AND fm.family_id <> :id",
                    ['id' => $familyId]
                );
                if ($elsewhere) {
                    throw new \DomainException('KK tidak bisa dihapus karena anggota berikut punya riwayat di KK lain: '
                        . implode(', ', array_column($elsewhere, 'full_name')) . '. Pindahkan atau hapus mereka satu per satu dulu.');
                }
            }

            Database::delete('family_members', 'family_id = :id', ['id' => $familyId]);
            foreach ($memberIds as $memberId) {
                $this->memberModel->delete($memberId);
            }
            $this->delete($familyId);
        });
    }

    // =====================================================
    // HELPER INTERNAL
    // =====================================================

    /**
     * Jalankan dalam transaksi; pelanggaran UNIQUE diterjemahkan ke pesan yang jelas
     */
    private function transaction(callable $callback)
    {
        Database::beginTransaction();
        try {
            $result = $callback();
            Database::commit();
            return $result;
        } catch (\Throwable $e) {
            Database::rollback();
            if ($e instanceof \PDOException && strpos($e->getMessage(), 'uq_current_member') !== false) {
                throw new \DomainException('Satu orang tidak boleh aktif di dua KK sekaligus.', 0, $e);
            }
            if ($e instanceof \PDOException && strpos($e->getMessage(), 'uq_current_head') !== false) {
                throw new \DomainException('Satu KK hanya boleh punya satu kepala keluarga.', 0, $e);
            }
            throw $e;
        }
    }

    private function findMember(int $id): array
    {
        $member = $this->memberModel->find($id);
        if (!$member) {
            throw new \DomainException('Data jemaat tidak ditemukan.');
        }
        return $member;
    }

    /**
     * Buat baris KK; no. KK = KK-xxxxx dari ID
     */
    private function createFamilyRow(array $data, ?int $originFamilyId = null): int
    {
        $id = (int) $this->create([
            'family_code' => 'TMP-' . bin2hex(random_bytes(6)),
            'address' => $data['address'] ?? null,
            'phone' => $data['phone'] ?? null,
            'notes' => $data['notes'] ?? null,
            'origin_family_id' => $originFamilyId,
            'created_by' => auth('id'),
        ]);
        Database::update($this->table, ['family_code' => sprintf('KK-%05d', $id)], 'id = :id', ['id' => $id]);
        return $id;
    }

    /**
     * Buat data orang baru (status Aktif)
     */
    private function createPerson(array $data): int
    {
        $data['status'] = 'active';
        $data['spouse_id'] = null;
        $data['created_by'] = auth('id');
        return (int) $this->memberModel->create($data);
    }

    private function familyCode(int $familyId): string
    {
        return (string) Database::fetchColumn("SELECT family_code FROM families WHERE id = :id", ['id' => $familyId]);
    }

    private function joinFamily(int $familyId, int $memberId, string $relationship, string $date, string $reason, ?string $note = null): void
    {
        $this->membership->open($familyId, $memberId, $relationship, $date);
        MemberStatusLog::add($memberId, $familyId, 'family', null, $this->familyCode($familyId), $date, $reason, $note);
    }

    /**
     * Keluar dari KK. Bila yang keluar kepala dan masih ada anggota aktif lain,
     * kepala baru wajib ditentukan (lalu hubungan anggota lain disesuaikan).
     */
    private function leaveFamily(array $row, string $date, string $reason, ?int $newHeadId, array $relationships, ?string $note = null): void
    {
        $familyId = (int) $row['family_id'];
        $memberId = (int) $row['member_id'];

        $promote = null;
        if ($row['relationship'] === 'head') {
            $others = array_filter(
                $this->membership->activeMembers($familyId),
                fn($m) => (int) $m['id'] !== $memberId
            );
            if ($others) {
                if (!$newHeadId || !in_array($newHeadId, array_map('intval', array_column($others, 'id')), true)) {
                    throw new \DomainException('Kepala keluarga keluar dari KK. Pilih kepala keluarga baru dari anggota yang masih aktif.');
                }
                $promote = $newHeadId;
            }
        }

        $this->membership->close((int) $row['id'], $date, $reason);
        MemberStatusLog::add($memberId, $familyId, 'family', $this->familyCode($familyId), null, $date, FamilyMember::LEFT_REASONS[$reason], $note);

        if ($promote) {
            $this->promoteHead($familyId, $promote, $relationships, $date);
        }
    }

    /**
     * Jadikan anggota kepala + terapkan penyesuaian hubungan anggota lain.
     * Kepala lama harus sudah keluar / sudah diganti hubungannya.
     */
    private function promoteHead(int $familyId, int $newHeadId, array $relationships, string $date): void
    {
        foreach ($relationships as $rel) {
            if ($rel !== '' && $rel !== null) {
                $this->assertNotHeadRelationship($rel);
            }
        }

        $newHeadRow = $this->membership->current($newHeadId);
        if (!$newHeadRow || (int) $newHeadRow['family_id'] !== $familyId) {
            throw new \DomainException('Kepala keluarga baru harus anggota aktif KK ini.');
        }
        $this->changeRelationship($newHeadRow, 'head', $date, 'Ganti kepala keluarga');

        foreach ($this->membership->activeMembers($familyId) as $m) {
            $rel = $relationships[$m['id']] ?? null;
            if ($rel && $rel !== $m['relationship'] && (int) $m['id'] !== $newHeadId) {
                $row = ['id' => $m['membership_id'], 'member_id' => $m['id'], 'family_id' => $familyId, 'relationship' => $m['relationship']];
                $this->changeRelationship($row, $rel, $date, 'Ganti kepala keluarga');
            }
        }
    }

    private function changeRelationship(array $row, string $relationship, string $date, string $reason): void
    {
        if ($row['relationship'] === $relationship) {
            return;
        }
        $this->membership->setRelationship((int) $row['id'], $relationship);
        MemberStatusLog::add((int) $row['member_id'], (int) $row['family_id'], 'relationship', $row['relationship'], $relationship, $date, $reason);
    }

    private function setMarital(int $memberId, string $marital, string $date, string $reason, ?string $note, ?int $relatedId, ?int $familyId): void
    {
        $member = $this->findMember($memberId);
        if ($member['marital_status'] === $marital) {
            return;
        }
        $this->memberModel->update($memberId, ['marital_status' => $marital]);
        MemberStatusLog::add($memberId, $familyId, 'marital_status', $member['marital_status'], $marital, $date, $reason, $note, $relatedId);
    }

    private function linkSpouses(int $aId, int $bId, string $date, ?int $familyId, string $reason, ?string $note = null): void
    {
        $this->setMarital($aId, 'married', $date, $reason, $note, $bId, $familyId);
        $this->setMarital($bId, 'married', $date, $reason, $note, $aId, $familyId);
        Database::update('members', ['spouse_id' => $bId], 'id = :id', ['id' => $aId]);
        Database::update('members', ['spouse_id' => $aId], 'id = :id', ['id' => $bId]);
    }

    private function unlinkSpouses(int $aId, int $bId): void
    {
        Database::query("UPDATE members SET spouse_id = NULL WHERE id IN (:a, :b)", ['a' => $aId, 'b' => $bId]);
    }

    private function assertCanMarry(array $member, array $spouse): void
    {
        if ((int) $member['id'] === (int) $spouse['id']) {
            throw new \DomainException('Pasangan tidak boleh orang yang sama.');
        }
        if ($spouse['status'] !== 'active') {
            throw new \DomainException('Pasangan harus jemaat berstatus Aktif.');
        }
        if ($member['gender'] === $spouse['gender']) {
            throw new \DomainException('Jenis kelamin pasangan harus berbeda.');
        }
        foreach ([$member, $spouse] as $person) {
            if ($person['marital_status'] === 'married' || $person['spouse_id']) {
                throw new \DomainException("{$person['full_name']} masih berstatus Menikah.");
            }
        }
    }

    /**
     * Kepala KK yang masih punya anggota aktif tidak boleh keluar tanpa ganti kepala
     */
    private function assertCanLeaveAsHead($row, array $person): void
    {
        if ($row && $row['relationship'] === 'head' && count($this->membership->activeMembers((int) $row['family_id'])) > 1) {
            throw new \DomainException("{$person['full_name']} adalah kepala KK yang masih punya anggota aktif. Jadikan anggota lain kepala keluarga dulu, atau pilih opsi bergabung ke KK-nya.");
        }
    }

    private function assertFamilyActive(int $familyId): void
    {
        if (!$this->find($familyId)) {
            throw new \DomainException('KK tidak ditemukan.');
        }
        if (!$this->membership->headOf($familyId)) {
            throw new \DomainException('KK ini sudah tidak punya anggota aktif (Arsip).');
        }
    }

    private function assertNotHeadRelationship(?string $relationship): void
    {
        if (!$relationship || !isset(FamilyMember::RELATIONSHIPS[$relationship])) {
            throw new \DomainException('Hubungan keluarga tidak valid.');
        }
        if ($relationship === 'head') {
            throw new \DomainException('Kepala keluarga hanya bisa diganti lewat menu Jadikan Kepala Keluarga.');
        }
    }
}
