<?php
/**
 * User Model
 * MM&Co Accounting Review Center Management System
 *
 * Database table: users
 * Registration columns (must match DB after migrations – see database/ folder):
 *   Base: name, email, password, role, department, is_ojt, required_hours, ojt_start_date
 *   Extended: first_name, middle_name, last_name, personal_email, contact_number,
 *   birthday, address, gender, civil_status, id_number, profile_photo_path,
 *   school_name, ojt_kind, ojt_end_date, employee_id
 */

namespace App\Models;

use App\Core\Model;
use PDO;

class User extends Model
{
    protected string $table = 'users';

    /**
     * Normalize email for storage and lookup (trim + lowercase).
     * Ensures registration and login use the same format.
     */
    public static function normalizeEmail(string $email): string
    {
        return strtolower(trim($email));
    }

    /**
     * Find user by email (uses normalized email; matches registration storage).
     */
    public function findByEmail(string $email): ?object
    {
        $email = self::normalizeEmail($email);
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
     * Uses employee_id column if present; falls back to id_number, then to prefix+01 if neither exists (e.g. pre-migration DB).
     */
    public function getNextEmployeeId(string $roleType, string $department): string
    {
        $roleCode = ($roleType === 'ojt') ? 'O' : 'E';
        $deptCode = self::departmentToCode($department);
        $yy = date('y'); // 26 for 2026
        $prefix = $roleCode . $deptCode . $yy; // e.g. EI26, OI26

        foreach (['employee_id', 'id_number'] as $column) {
            try {
                $stmt = $this->db->prepare(
                    "SELECT {$column} FROM {$this->table} WHERE {$column} IS NOT NULL AND {$column} != '' AND {$column} LIKE ? ORDER BY {$column} DESC LIMIT 1"
                );
                $stmt->execute([$prefix . '%']);
                $last = $stmt->fetch(PDO::FETCH_OBJ);
                $nextSeq = 1;
                if ($last && isset($last->{$column}) && preg_match('/^' . preg_quote($prefix, '/') . '(\d+)$/', (string) $last->{$column}, $m)) {
                    $nextSeq = (int) $m[1] + 1;
                }
                return $prefix . str_pad((string) $nextSeq, 2, '0', STR_PAD_LEFT);
            } catch (\PDOException $e) {
                // Column may not exist (e.g. migration not run); try next column or fallback
                continue;
            }
        }

        return $prefix . '01';
    }
}
