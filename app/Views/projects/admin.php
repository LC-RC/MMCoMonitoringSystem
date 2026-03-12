<?php
$title = 'Projects';
$pageTitle = 'Projects';
ob_start();
?>

<div class="space-y-6">
    <!-- Add Project -->
    <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
        <h3 class="font-semibold text-gray-800 mb-4" style="color: <?= $theme['primary'] ?? '#1E3A8A' ?>">Create Project</h3>
        <form action="<?= base_path() ?>/admin/projects" method="POST" class="flex flex-wrap gap-4 items-end">
            <input type="hidden" name="_token" value="<?= \App\Core\Auth::csrfToken() ?>">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Project Name</label>
                <input type="text" name="name" required class="w-full px-4 py-2 rounded-xl border border-gray-300" placeholder="e.g. Website Redesign">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deadline</label>
                <input type="date" name="deadline" class="px-4 py-2 rounded-xl border border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">&nbsp;</label>
                <button type="submit" class="px-4 py-2 rounded-xl btn-primary text-white font-medium">Create</button>
            </div>
        </form>
    </div>

    <!-- Projects Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($projects as $p): ?>
        <a href="<?= base_path() ?>/admin/projects/<?= $p->id ?>" class="block bg-white rounded-2xl shadow-sm p-6 border border-gray-100 hover:border-gray-200 hover:shadow-md transition-all">
            <div class="flex justify-between items-start mb-4">
                <h3 class="font-semibold text-gray-800"><?= htmlspecialchars($p->name) ?></h3>
                <span class="text-sm font-bold px-2 py-1 rounded-lg badge-accent"><?= $p->completion_percent ?? 0 ?>%</span>
            </div>
            <div class="h-2 bg-gray-200 rounded-full overflow-hidden mb-2">
                <div class="h-full rounded-full transition-all" style="width: <?= $p->completion_percent ?? 0 ?>%; background: <?= $theme['accent'] ?? '#FACC15' ?>"></div>
            </div>
            <p class="text-sm text-gray-500"><?= $p->completed_tasks ?? 0 ?> / <?= $p->task_count ?? 0 ?> tasks</p>
            <?php if ($p->deadline): ?>
            <p class="text-xs text-gray-400 mt-1">Due: <?= date('M d, Y', strtotime($p->deadline)) ?></p>
            <?php endif; ?>
        </a>
        <?php endforeach; ?>
        <?php if (empty($projects)): ?>
        <div class="col-span-full text-center py-12 text-gray-500">No active projects. Create one above.</div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
$theme = \App\Core\ThemeHelper::getTheme($user);
require __DIR__ . '/../layouts/main.php';
?>
