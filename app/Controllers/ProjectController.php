<?php
/**
 * Project Controller
 * MM&Co Accounting Review Center Management System
 */

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Project;
use App\Models\User;
use App\Core\Database;

class ProjectController extends Controller
{
    /**
     * Admin: Project list
     */
    public function adminIndex(): void
    {
        Auth::requireAdmin();

        $projectModel = new Project();
        $projects = $projectModel->getActiveProjects();

        foreach ($projects as $p) {
            $p->completion_percent = $projectModel->getCompletionPercent($p);
        }

        $this->view('projects.admin', ['projects' => $projects]);
    }

    /**
     * Show project detail / Kanban
     */
    public function show(int $id): void
    {
        Auth::requireAdmin();

        $projectModel = new Project();
        $project = $projectModel->getWithTasks($id);
        if (!$project) {
            $_SESSION['error'] = 'Project not found.';
            $this->redirect('/admin/projects');
        }

        $userModel = new User();
        $employees = $userModel->getEmployees();

        // Group tasks by phase
        $phases = ['To Do' => [], 'In Progress' => [], 'Review' => [], 'Completed' => []];
        foreach ($project->tasks ?? [] as $task) {
            $phases[$task->phase][] = $task;
        }

        $this->view('projects.show', [
            'project' => $project,
            'phases' => $phases,
            'employees' => $employees,
        ]);
    }

    /**
     * Store project
     */
    public function store(): void
    {
        Auth::requireAdmin();
        $this->requireCsrf();

        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $deadline = $_POST['deadline'] ?? null;

        if (empty($name)) {
            $_SESSION['error'] = 'Project name is required.';
            $this->redirect('/admin/projects');
        }

        $projectModel = new Project();
        $projectModel->create([
            'name' => $name,
            'description' => $description ?: null,
            'deadline' => $deadline ?: null,
            'status' => 'active',
            'created_by' => $this->user->id,
        ]);

        $_SESSION['success'] = 'Project created successfully.';
        $this->redirect('/admin/projects');
    }

    /**
     * Update task phase
     */
    public function updateTaskPhase(int $taskId): void
    {
        Auth::requireAdmin();
        $this->requireCsrf();

        $phase = $_POST['phase'] ?? '';
        $valid = ['To Do', 'In Progress', 'Review', 'Completed'];
        if (!in_array($phase, $valid)) {
            $this->json(['error' => 'Invalid phase'], 400);
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE tasks SET phase = ? WHERE id = ?");
        $stmt->execute([$phase, $taskId]);

        $this->json(['success' => true]);
    }

    /**
     * Store task
     */
    public function storeTask(int $projectId): void
    {
        Auth::requireAdmin();
        $this->requireCsrf();

        $title = trim($_POST['title'] ?? '');
        if (empty($title)) {
            $_SESSION['error'] = 'Task title is required.';
            $this->redirect("/admin/projects/{$projectId}");
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO tasks (project_id, title, phase) VALUES (?, ?, 'To Do')");
        $stmt->execute([$projectId, $title]);

        $_SESSION['success'] = 'Task added.';
        $this->redirect("/admin/projects/{$projectId}");
    }
}
