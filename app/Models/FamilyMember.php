<?php
namespace App\Models;

use Core\Model;
use Core\Database;

/**
 * =========================================================
 * FamilyMember Model (Keanggotaan KK + riwayatnya)
 * =========================================================
 *
 * Baris dengan left_at NULL = keanggotaan yang sedang berjalan.
 * Pindah KK tidak meng-update baris lama: baris lama ditutup,
 * baris baru dibuat, sehingga asal-usul keluarga tidak hilang.
 */
class FamilyMember extends Model
{
    protected $table = 'family_members';

    protected $fillable = [
        'family_id',
        'member_id',
        'relationship',
        'joined_at',
        'left_at',
        'left_reason',
    ];

    const RELATIONSHIPS = [
        'head' => 'Kepala Keluarga',
        'spouse' => 'Suami/Istri',
        'child' => 'Anak',
        'child_in_law' => 'Menantu',
        'grandchild' => 'Cucu',
        'parent' => 'Orang Tua',
        'parent_in_law' => 'Mertua',
        'sibling' => 'Saudara Kandung',
        'other' => 'Famili Lain',
    ];

    const LEFT_REASONS = [
        'married_out' => 'Menikah & pisah KK',
        'moved_family' => 'Pindah KK',
        'divorce' => 'Cerai',
        'status_change' => 'Perubahan status jemaat',
        'data_fix' => 'Koreksi data',
    ];

    /** Urutan tampil anggota di KK */
    const ORDER_SQL = "FIELD(fm.relationship, 'head','spouse','child','child_in_law','grandchild','parent','parent_in_law','sibling','other')";

    /**
     * Label hubungan; "Suami/Istri" diperjelas sesuai gender
     */
    public static function relationshipLabel(string $relationship, ?string $gender = null): string
    {
        if ($relationship === 'spouse' && $gender) {
            return $gender === 'F' ? 'Istri' : 'Suami';
        }
        return self::RELATIONSHIPS[$relationship] ?? $relationship;
    }

    /**
     * Keanggotaan KK yang sedang berjalan untuk seseorang
     *
     * @return array|false
     */
    public function current(int $memberId)
    {
        return Database::fetch(
            "SELECT * FROM {$this->table} WHERE member_id = :id AND left_at IS NULL",
            ['id' => $memberId]
        );
    }

    /**
     * Keanggotaan terakhir (terbuka atau tertutup) seseorang
     *
     * @return array|false
     */
    public function latest(int $memberId)
    {
        return Database::fetch(
            "SELECT * FROM {$this->table} WHERE member_id = :id
             ORDER BY left_at IS NULL DESC, left_at DESC, id DESC LIMIT 1",
            ['id' => $memberId]
        );
    }

    /**
     * Kepala keluarga yang aktif di KK
     *
     * @return array|false Baris keanggotaan
     */
    public function headOf(int $familyId)
    {
        return Database::fetch(
            "SELECT * FROM {$this->table} WHERE family_id = :id AND left_at IS NULL AND relationship = 'head'",
            ['id' => $familyId]
        );
    }

    /**
     * Anggota aktif sebuah KK (beserta data orangnya)
     */
    public function activeMembers(int $familyId): array
    {
        return Database::fetchAll(
            "SELECT m.*, fm.id AS membership_id, fm.relationship, fm.joined_at
             FROM {$this->table} fm
             JOIN members m ON m.id = fm.member_id
             WHERE fm.family_id = :id AND fm.left_at IS NULL
             ORDER BY " . self::ORDER_SQL . ", m.birth_date IS NULL, m.birth_date ASC",
            ['id' => $familyId]
        );
    }

    /**
     * Buka keanggotaan baru. Ditolak jika orang masih aktif di KK lain.
     *
     * @return int ID keanggotaan
     */
    public function open(int $familyId, int $memberId, string $relationship, ?string $date = null): int
    {
        if (!isset(self::RELATIONSHIPS[$relationship])) {
            throw new \DomainException('Hubungan keluarga tidak valid.');
        }
        if ($this->current($memberId)) {
            throw new \DomainException('Orang ini masih aktif di KK lain. Gunakan menu Pindahkan KK.');
        }
        if ($relationship === 'head' && $this->headOf($familyId)) {
            throw new \DomainException('KK ini sudah punya kepala keluarga.');
        }

        return (int) Database::insert($this->table, [
            'family_id' => $familyId,
            'member_id' => $memberId,
            'relationship' => $relationship,
            'joined_at' => $date ?: date('Y-m-d'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Tutup keanggotaan (keluar KK)
     */
    public function close(int $membershipId, string $date, string $reason): void
    {
        Database::update($this->table, [
            'left_at' => $date,
            'left_reason' => $reason,
        ], 'id = :id', ['id' => $membershipId]);
    }

    /**
     * Ubah hubungan keluarga pada keanggotaan yang berjalan
     */
    public function setRelationship(int $membershipId, string $relationship): void
    {
        if (!isset(self::RELATIONSHIPS[$relationship])) {
            throw new \DomainException('Hubungan keluarga tidak valid.');
        }
        Database::update($this->table, ['relationship' => $relationship], 'id = :id', ['id' => $membershipId]);
    }
}
