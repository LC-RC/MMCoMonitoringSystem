<?php
/**
 * Project Model
 * MM&Co Accounting Review Center Management System
 */

namespace App\Models;

use App\Core\Model;
use PDO;

class Project extends Model
{
    protected string $table = 'projects';

    /**
     * Get active projects with task counts
     */
    public function getActiveProjects(): array
    {
        $stmt = $this->db->query(
            "SELECT p.*,
             (SELECT COUNT(*) FROM tasks t WHERE t.project_id = p.id) as task_count,
             (SELECT COUNT(*) FROM tasks t WHERE t.project_id = p.id AND t.phase = 'Completed') as completed_tasks
             FROM {$this->table} p
             WHERE p.status = 'active'
             ORDER BY p.deadline ASC"
        );
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get project with tasks
     */
    public function getWithTasks(int $id): ?object
    {
        $project = $this->find($id);
        if (!$project) return null;

        $stmt = $this->db->prepare(
            "SELECT * FROM tasks WHERE project_id = ? ORDER BY FIELD(phase, 'To Do', 'In Progress', 'Review', 'Completed'), sort_order"
        );
        $stmt->execute([$id]);
        $project->tasks = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $project;
    }

    /**
     * Calculate completion percentage
     */
    public function getCompletionPercent(object $project): float
    {
        if (empty($project->task_count) || $project->task_count == 0) {
            return 0;
        }
        $completed = $project->completed_tasks ?? 0;
        return round(($completed / $project->task_count) * 100, 1);
    }
}
