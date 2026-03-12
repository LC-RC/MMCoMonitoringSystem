<?php
/**
 * Payroll Record Model
 * MM&Co Accounting Review Center Management System
 */

namespace App\Models;

use App\Core\Model;
use PDO;

class PayrollRecord extends Model
{
    protected string $table = 'payroll_records';

    /**
     * Get records for period
     */
    public function getByPeriod(string $start, string $end): array
    {
        $stmt = $this->db->prepare(
            "SELECT pr.*, u.name, u.department FROM {$this->table} pr
             JOIN users u ON pr.user_id = u.id
             WHERE pr.period_start = ? AND pr.period_end = ?
             ORDER BY u.name"
        );
        $stmt->execute([$start, $end]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get or create record for user/period
     */
    public function getOrCreateForUser(int $userId, string $start, string $end): ?object
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE user_id = ? AND period_start = ? AND period_end = ?"
        );
        $stmt->execute([$userId, $start, $end]);
        return $stmt->fetch(PDO::FETCH_OBJ) ?: null;
    }
}
