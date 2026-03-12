<?php
/**
 * Attendance Model
 * MM&Co Accounting Review Center Management System
 */

namespace App\Models;

use App\Core\Model;
use PDO;

class Attendance extends Model
{
    protected string $table = 'attendance';

    /**
     * Get today's attendance for user
     */
    public function getTodayForUser(int $userId): ?object
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE user_id = ? AND date = CURDATE()"
        );
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result ?: null;
    }

    /**
     * Check if user has clocked in today
     */
    public function hasClockedInToday(int $userId): bool
    {
        return $this->getTodayForUser($userId) !== null;
    }

    /**
     * Get open session (clocked in but not out)
     */
    public function getOpenSession(int $userId): ?object
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE user_id = ? AND clock_out_time IS NULL AND date = CURDATE()"
        );
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result ?: null;
    }

    /**
     * Get attendance records for user
     */
    public function getByUser(int $userId, int $limit = 30): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE user_id = ? ORDER BY date DESC LIMIT ?"
        );
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get total hours for user in date range
     */
    public function getTotalHoursForUser(int $userId, string $start, string $end): float
    {
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(total_hours), 0) FROM {$this->table}
             WHERE user_id = ? AND date BETWEEN ? AND ? AND clock_out_time IS NOT NULL"
        );
        $stmt->execute([$userId, $start, $end]);
        return (float) $stmt->fetchColumn();
    }

    /**
     * Get this week's hours for user
     */
    public function getWeekHoursForUser(int $userId): float
    {
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(total_hours), 0) FROM {$this->table}
             WHERE user_id = ? AND YEARWEEK(date) = YEARWEEK(CURDATE()) AND clock_out_time IS NOT NULL"
        );
        $stmt->execute([$userId]);
        return (float) $stmt->fetchColumn();
    }

    /**
     * Get all attendance with filters (admin)
     */
    public function getAllFiltered(array $filters = []): array
    {
        $sql = "SELECT a.*, u.name, u.department, u.is_ojt FROM {$this->table} a
                JOIN users u ON a.user_id = u.id WHERE 1=1";
        $params = [];

        if (!empty($filters['department'])) {
            $sql .= " AND u.department = ?";
            $params[] = $filters['department'];
        }
        if (isset($filters['is_ojt']) && $filters['is_ojt'] !== '') {
            $sql .= " AND u.is_ojt = ?";
            $params[] = (int) $filters['is_ojt'];
        }
        if (!empty($filters['date_from'])) {
            $sql .= " AND a.date >= ?";
            $params[] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $sql .= " AND a.date <= ?";
            $params[] = $filters['date_to'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (u.name LIKE ? OR u.email LIKE ?)";
            $term = '%' . $filters['search'] . '%';
            $params[] = $term;
            $params[] = $term;
        }

        $sql .= " ORDER BY a.date DESC, a.clock_in_time DESC LIMIT 500";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Count employees clocked in today
     */
    public function countClockedInToday(): int
    {
        $stmt = $this->db->query(
            "SELECT COUNT(DISTINCT user_id) FROM {$this->table}
             WHERE date = CURDATE() AND clock_out_time IS NULL"
        );
        return (int) $stmt->fetchColumn();
    }

    /**
     * Get attendance summary by department for today
     */
    public function getTodaySummaryByDepartment(): array
    {
        $stmt = $this->db->query(
            "SELECT u.department, COUNT(*) as count
             FROM {$this->table} a
             JOIN users u ON a.user_id = u.id
             WHERE a.date = CURDATE() AND a.clock_out_time IS NULL
             GROUP BY u.department"
        );
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
