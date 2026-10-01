<?php
namespace App\Models;

use Core\Model;
use Core\Database;

/**
 * =========================================================
 * Member Model (Data Jemaat - per orang)
 * =========================================================
 *
 * Setiap orang tercatat di tepat satu KK aktif (lihat FamilyMember).
 * Status jemaat & status pernikahan diubah lewat Family agar
 * riwayat dan efeknya ke anggota lain ikut tercatat.
 */
class Member extends Model
{
    protected $table = 'members';

    protected $fillable = [
        'full_name',
        'gender',
        'marital_status',
        'spouse_id',
        'email',
        'phone',
        'address',
        'birth_date',
        'birth_place',
        'baptism_date',
        'membership_date',
        'status',
        'status_date',
        'status_note',
        'notes',
        'created_by'
    ];

    /** Umur minimal "anak dewasa" (umur KTP) */
    const ADULT_AGE = 17;

    const STATUSES = [
        'active' => 'Aktif',
        'inactive' => 'Tidak Aktif',
        'deceased' => 'Meninggal',
        'moved_church' => 'Pindah Gereja',
        'moved_religion' => 'Pindah Agama',
    ];

    /** Warna tag (class .t-*) & ikon per status */
    const STATUS_TONES = [
        'active' => ['t-green', 'check-circle-fill'],
        'inactive' => ['t-grey', 'pause-circle'],
        'deceased' => ['t-dark', 'flower1'],
        'moved_church' => ['t-blue', 'signpost-split'],
        'moved_religion' => ['t-orange', 'arrow-left-right'],
    ];

    /** Nilai filter status pernikahan; janda/duda = widowed + gender */
    const MARITAL_FILTERS = [
        'single' => 'Belum Menikah',
        'married' => 'Menikah',
        'janda' => 'Janda',
        'duda' => 'Duda',
        'none' => 'Belum diisi',
    ];

    /**
     * Label status pernikahan (widowed → Janda/Duda sesuai gender)
     */
    public static function maritalLabel(?string $marital, ?string $gender): string
    {
        switch ($marital) {
            case 'single':
                return 'Belum Menikah';
            case 'married':
                return 'Menikah';
            case 'widowed':
                return $gender === 'F' ? 'Janda' : 'Duda';
            default:
                return 'Belum diisi';
        }
    }

    /**
     * Pilihan status pernikahan untuk form (label menyesuaikan gender jika diketahui)
     */
    public static function maritalOptions(?string $gender = null): array
    {
        $widowed = $gender === 'F' ? 'Janda' : ($gender === 'M' ? 'Duda' : 'Janda/Duda');
        return ['single' => 'Belum Menikah', 'married' => 'Menikah', 'widowed' => $widowed];
    }

    public static function statusLabel(string $status): string
    {
        return self::STATUSES[$status] ?? $status;
    }

    /**
     * Umur dalam tahun, null jika tanggal lahir kosong
     */
    public static function age(?string $birthDate): ?int
    {
        if (!$birthDate) {
            return null;
        }
        return (new \DateTime($birthDate))->diff(new \DateTime('today'))->y;
    }

    /**
     * Hitung total jemaat aktif (TOTAL JEMAAT = orang berstatus Aktif)
     */
    public function countActive(): int
    {
        return $this->count('status', 'active');
    }

    /**
     * Rekap jemaat: total aktif, per gender (aktif), dan per status
     */
    public function getSummary(): array
    {
        $rows = Database::fetchAll(
            "SELECT status, gender, COUNT(*) AS total FROM {$this->table} GROUP BY status, gender"
        );

        $summary = [
            'active' => 0,
            'male' => 0,
            'female' => 0,
            'by_status' => array_fill_keys(array_keys(self::STATUSES), 0),
        ];

        foreach ($rows as $row) {
            $summary['by_status'][$row['status']] += (int) $row['total'];
            if ($row['status'] === 'active') {
                $summary['active'] += (int) $row['total'];
                $summary[$row['gender'] === 'M' ? 'male' : 'female'] += (int) $row['total'];
            }
        }

        return $summary;
    }

    /**
     * Cari jemaat AKTIF untuk kolom pencarian (pilih pasangan / pindahkan ke KK)
     *
     * @param string $keyword
     * @param int|null $excludeFamilyId Jangan tampilkan anggota KK ini
     * @return array
     */
    public function searchActive(string $keyword, ?int $excludeFamilyId = null): array
    {
        $sql = "SELECT m.id, m.full_name, m.gender, m.birth_date, m.marital_status, m.spouse_id,
                       f.id AS family_id, f.family_code, fm.relationship
                FROM {$this->table} m
                JOIN family_members fm ON fm.member_id = m.id AND fm.left_at IS NULL
                JOIN families f ON f.id = fm.family_id
                WHERE m.status = 'active' AND (m.full_name LIKE :kw1 OR m.phone LIKE :kw2)";
        $params = ['kw1' => '%' . $keyword . '%', 'kw2' => '%' . $keyword . '%'];

        if ($excludeFamilyId) {
            $sql .= " AND f.id <> :exclude";
            $params['exclude'] = $excludeFamilyId;
        }

        return Database::fetchAll($sql . " ORDER BY m.full_name ASC LIMIT 15", $params);
    }
}
