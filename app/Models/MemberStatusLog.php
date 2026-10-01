<?php
namespace App\Models;

use Core\Model;
use Core\Database;

/**
 * =========================================================
 * MemberStatusLog Model (Riwayat perubahan jemaat)
 * =========================================================
 *
 * Append-only: setiap perubahan status jemaat, status pernikahan,
 * hubungan keluarga, dan perpindahan KK dicatat di sini.
 */
class MemberStatusLog extends Model
{
    protected $table = 'member_status_logs';

    const FIELDS = [
        'status' => 'Status jemaat',
        'marital_status' => 'Status pernikahan',
        'relationship' => 'Hubungan keluarga',
        'family' => 'Kartu Keluarga',
    ];

    /**
     * Catat satu perubahan
     */
    public static function add(
        int $memberId,
        ?int $familyId,
        string $field,
        ?string $oldValue,
        ?string $newValue,
        string $date,
        ?string $reason = null,
        ?string $note = null,
        ?int $relatedMemberId = null
    ): void {
        Database::insert('member_status_logs', [
            'member_id' => $memberId,
            'family_id' => $familyId,
            'field' => $field,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'changed_at' => $date,
            'reason' => $reason,
            'note' => $note ?: null,
            'related_member_id' => $relatedMemberId,
            'created_by' => auth('id'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Riwayat untuk timeline detail KK
     */
    public function forFamily(int $familyId, int $limit = 50): array
    {
        $sql = "SELECT l.*, m.full_name, m.gender, r.full_name AS related_name, u.name AS user_name
                FROM {$this->table} l
                JOIN members m ON m.id = l.member_id
                LEFT JOIN members r ON r.id = l.related_member_id
                LEFT JOIN users u ON u.id = l.created_by
                WHERE l.family_id = :id
                ORDER BY l.changed_at DESC, l.id DESC
                LIMIT " . (int) $limit;

        return Database::fetchAll($sql, ['id' => $familyId]);
    }

    /**
     * Teks yang mudah dibaca untuk satu baris riwayat
     */
    public static function describe(array $log): string
    {
        $gender = $log['gender'] ?? null;
        $value = function ($v) use ($log, $gender) {
            if ($v === null || $v === '') {
                return '-';
            }
            switch ($log['field']) {
                case 'status':
                    return Member::statusLabel($v);
                case 'marital_status':
                    return Member::maritalLabel($v, $gender);
                case 'relationship':
                    return FamilyMember::relationshipLabel($v, $gender);
                default:
                    return $v;
            }
        };

        $label = self::FIELDS[$log['field']] ?? $log['field'];
        if ($log['field'] === 'family') {
            if ($log['old_value'] && $log['new_value']) {
                return "Pindah KK: {$log['old_value']} → {$log['new_value']}";
            }
            return $log['new_value'] ? "Masuk {$log['new_value']}" : "Keluar dari {$log['old_value']}";
        }

        return "{$label}: {$value($log['old_value'])} → {$value($log['new_value'])}";
    }
}
