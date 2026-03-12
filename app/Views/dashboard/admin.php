<?php
$title = 'Admin Dashboard';
$pageTitle = 'Admin Dashboard';
ob_start();
?>

<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Total Employees</p>
                    <p class="text-3xl font-bold mt-1" style="color: <?= $theme['primary'] ?? '#1E3A8A' ?>"><?= $totalEmployees ?></p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: <?= $theme['accent_light'] ?? '#FDE68A' ?>">
                    <svg class="w-6 h-6" style="color: <?= $theme['primary'] ?? '#1E3A8A' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Clocked In Today</p>
                    <p class="text-3xl font-bold mt-1" style="color: <?= $theme['primary'] ?? '#1E3A8A' ?>"><?= $clockedInToday ?></p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center badge-accent">
                    <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Active Projects</p>
                    <p class="text-3xl font-bold mt-1" style="color: <?= $theme['primary'] ?? '#1E3A8A' ?>"><?= count($activeProjects) ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Low Stock Alerts</p>
                    <p class="text-3xl font-bold mt-1 <?= count($lowStockItems) > 0 ? 'text-amber-600' : 'text-gray-400' ?>"><?= count($lowStockItems) ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Department Overview -->
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
            <h3 class="font-semibold text-gray-800 mb-4" style="color: <?= $theme['primary'] ?? '#1E3A8A' ?>">Department Overview (Today)</h3>
            <div class="space-y-3">
                <?php 
                $deptCounts = [];
                foreach ($departmentSummary as $d) $deptCounts[$d->department] = $d->count;
                foreach (['IT', 'Accounting', 'HR'] as $dept): 
                    $count = $deptCounts[$dept] ?? 0;
                ?>
                <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50">
                    <span class="font-medium"><?= $dept ?></span>
                    <span class="px-3 py-1 rounded-full text-sm font-medium badge-accent"><?= $count ?> clocked in</span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- OJT Progress Overview -->
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
            <h3 class="font-semibold text-gray-800 mb-4" style="color: <?= $theme['primary'] ?? '#1E3A8A' ?>">OJT Progress Overview</h3>
            <?php if (!empty($ojtProgressData)): ?>
            <div class="space-y-4">
                <?php foreach ($ojtProgressData as $ojt): ?>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium"><?= htmlspecialchars($ojt['user']->name) ?></span>
                        <span><?= $ojt['percent'] ?>%</span>
                    </div>
                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all" style="width: <?= $ojt['percent'] ?>%; background: <?= $theme['accent'] ?? '#FACC15' ?>"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1"><?= number_format($ojt['completed'], 1) ?>h / <?= $ojt['user']->required_hours ?>h</p>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p class="text-gray-500">No OJT records</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Low Stock Alerts -->
    <?php if (!empty($lowStockItems)): ?>
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6">
        <h3 class="font-semibold text-amber-800 mb-4">⚠ Low Stock Alerts</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($lowStockItems as $item): ?>
            <a href="<?= base_path() ?>/admin/inventory?search=<?= urlencode($item->name) ?>" class="flex items-center justify-between p-4 bg-white rounded-xl border border-amber-100 hover:border-amber-300 transition-colors">
                <span class="font-medium"><?= htmlspecialchars($item->name) ?></span>
                <span class="px-2 py-1 rounded-lg bg-amber-200 text-amber-900 text-sm font-medium"><?= $item->quantity ?> / <?= $item->low_stock_threshold ?></span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Active Projects & Payroll -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
            <h3 class="font-semibold text-gray-800 mb-4" style="color: <?= $theme['primary'] ?? '#1E3A8A' ?>">Active Projects</h3>
            <?php if (!empty($activeProjects)): ?>
            <div class="space-y-3">
                <?php foreach (array_slice($activeProjects, 0, 5) as $p): 
                    $percent = $p->task_count > 0 ? round(($p->completed_tasks / $p->task_count) * 100, 1) : 0;
                ?>
                <a href="<?= base_path() ?>/admin/projects/<?= $p->id ?>" class="block p-4 rounded-xl border border-gray-100 hover:border-gray-200 transition-colors">
                    <div class="flex justify-between items-start">
                        <span class="font-medium"><?= htmlspecialchars($p->name) ?></span>
                        <span class="text-sm text-gray-500"><?= $percent ?>%</span>
                    </div>
                    <div class="mt-2 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full rounded-full" style="width: <?= $percent ?>%; background: <?= $theme['accent'] ?? '#FACC15' ?>"></div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
            <a href="<?= base_path() ?>/admin/projects" class="inline-block mt-4 text-sm font-medium" style="color: <?= $theme['primary'] ?? '#1E3A8A' ?>">View all →</a>
            <?php else: ?>
            <p class="text-gray-500">No active projects</p>
            <?php endif; ?>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
            <h3 class="font-semibold text-gray-800 mb-4" style="color: <?= $theme['primary'] ?? '#1E3A8A' ?>">Payroll Summary</h3>
            <p class="text-2xl font-bold" style="color: <?= $theme['primary'] ?? '#1E3A8A' ?>">₱<?= number_format($payrollTotal ?? 0, 2) ?></p>
            <p class="text-sm text-gray-500 mt-1">Current period (<?= date('M Y') ?>)</p>
            <a href="<?= base_path() ?>/admin/payroll" class="inline-block mt-4 px-4 py-2 rounded-xl font-medium text-white btn-primary">View Payroll</a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$theme = \App\Core\ThemeHelper::getTheme($user);
require __DIR__ . '/../layouts/main.php';
?>
