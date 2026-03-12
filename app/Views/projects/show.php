<?php
$title = $project->name ?? 'Project';
$pageTitle = htmlspecialchars($project->name ?? 'Project');
ob_start();
?>

<div class="space-y-6">
    <!-- Project Header -->
    <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
        <div class="flex justify-between items-start flex-wrap gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($project->name) ?></h2>
                <?php if ($project->deadline): ?>
                <p class="text-gray-500 mt-1">Deadline: <?= date('M d, Y', strtotime($project->deadline)) ?></p>
                <?php endif; ?>
            </div>
            <?php
            $totalTasks = count($project->tasks ?? []);
            $completedTasks = count(array_filter($project->tasks ?? [], fn($t) => $t->phase === 'Completed'));
            $percent = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;
            ?>
            <div class="text-right">
                <p class="text-2xl font-bold" style="color: <?= $theme['primary'] ?? '#1E3A8A' ?>"><?= $percent ?>%</p>
                <p class="text-sm text-gray-500"><?= $completedTasks ?> / <?= $totalTasks ?> tasks</p>
            </div>
        </div>
        <div class="mt-4 h-3 bg-gray-200 rounded-xl overflow-hidden">
            <div class="h-full rounded-xl transition-all" style="width: <?= $percent ?>%; background: <?= $theme['accent'] ?? '#FACC15' ?>"></div>
        </div>
    </div>

    <!-- Add Task -->
    <div class="bg-white rounded-2xl shadow-sm p-4 border border-gray-100">
        <form action="<?= base_path() ?>/admin/projects/<?= $project->id ?>/task" method="POST" class="flex gap-2">
            <input type="hidden" name="_token" value="<?= \App\Core\Auth::csrfToken() ?>">
            <input type="text" name="title" required placeholder="New task..." class="flex-1 px-4 py-2 rounded-xl border border-gray-300">
            <button type="submit" class="px-4 py-2 rounded-xl btn-primary text-white font-medium">Add Task</button>
        </form>
    </div>

    <!-- Kanban Board -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php foreach ($phases as $phase => $tasks): ?>
        <div class="bg-gray-50 rounded-2xl p-4 border border-gray-200 min-h-[200px]">
            <h3 class="font-semibold text-gray-800 mb-4" style="color: <?= $theme['primary'] ?? '#1E3A8A' ?>"><?= htmlspecialchars($phase) ?></h3>
            <div class="space-y-3">
                <?php foreach ($tasks as $task): ?>
                <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 group">
                    <p class="font-medium text-gray-800"><?= htmlspecialchars($task->title) ?></p>
                    <div class="mt-2 flex flex-wrap gap-1">
                        <?php foreach (['To Do', 'In Progress', 'Review', 'Completed'] as $p): ?>
                        <form action="<?= base_path() ?>/admin/projects/task/<?= $task->id ?>/phase" method="POST" class="inline">
                            <input type="hidden" name="_token" value="<?= \App\Core\Auth::csrfToken() ?>">
                            <input type="hidden" name="phase" value="<?= $p ?>">
                            <button type="submit" class="text-xs px-2 py-1 rounded <?= $task->phase === $p ? 'badge-accent font-medium' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' ?>"><?= $p ?></button>
                        </form>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($tasks)): ?>
                <p class="text-sm text-gray-400 italic">No tasks</p>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="mt-6">
    <a href="<?= base_path() ?>/admin/projects" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-800">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        Back to Projects
    </a>
</div>

<?php
$content = ob_get_clean();
$theme = \App\Core\ThemeHelper::getTheme($user);
require __DIR__ . '/../layouts/main.php';
?>
