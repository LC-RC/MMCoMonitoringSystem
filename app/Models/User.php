<?php
/**
 * User Model
 * MM&Co Accounting Review Center Management System
 */

namespace App\Models;

use App\Core\Model;
use PDO;

class User extends Model
{
    protected string $table = 'users';

    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?object
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = ?");
        $stmt->execute([$email]);
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result ?: null;
    }

    /**
     * Get all employees (non-admin)
     */
    public function getEmployees(): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM {$this->table} WHERE role = 'employee' ORDER BY department, name"
        );
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get OJT users
     */
    public function getOJTs(): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM {$this->table} WHERE is_ojt = 1 ORDER BY department, name"
        );
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Count total employees
     */
    public function countEmployees(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM {$this->table} WHERE role = 'employee'");
        return (int) $stmt->fetchColumn();
    }

    /**
     * Get display label (Staff vs OJT)
     */
    public static function getDisplayLabel(object $user): string
    {
        if ($user->is_ojt) {
            return $user->department . ' OJT';
        }
        return $user->department . ' Staff';
    }

    /**
     * Department name to single-letter code (for Employee ID format TDYY##)
     */
    public static function departmentToCode(string $department): string
    {
        $map = [
            'IT'         => 'I',
            'Accounting' => 'A',
            'HR'         => 'H',
        ];
        $d = trim($department);
        return $map[$d] ?? 'O'; // O = Other
    }

    /**
     * Get next Employee ID for given role and department. Format: TDYY## (T=Role, D=Dept, YY=year, ##=sequence).
     * Sequence never resets; continues across years.
     */
    public function getNextEmployeeId(string $roleType, string $department): string
    {
        $roleCode = ($roleType === 'ojt') ? 'O' : 'E';
        $deptCode = self::departmentToCode($department);
        $yy = date('y'); // 26 for 2026
        $prefix = $roleCode . $deptCode . $yy; // e.g. EI26, OI26

        $stmt = $this->db->prepare(
            "SELECT employee_id FROM {$this->table} WHERE employee_id IS NOT NULL AND employee_id LIKE ? ORDER BY employee_id DESC LIMIT 1"
        );
        $stmt->execute([$prefix . '%']);
        $last = $stmt->fetch(PDO::FETCH_OBJ);

        $nextSeq = 1;
        if ($last && preg_match('/^' . preg_quote($prefix, '/') . '(\d+)$/', $last->employee_id ?? '', $m)) {
            $nextSeq = (int) $m[1] + 1;
        }
        return $prefix . str_pad((string) $nextSeq, 2, '0', STR_PAD_LEFT);
    }
}
