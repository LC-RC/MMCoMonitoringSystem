<?php
$title = 'Attendance';
$pageTitle = 'Attendance';
ob_start();
?>

<div class="space-y-6">
    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
        <form method="GET" action="<?= base_path() ?>/admin/attendance" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                <select name="department" class="px-4 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500">
                    <option value="">All</option>
                    <?php foreach (['IT', 'Accounting', 'HR'] as $d): ?>
                    <option value="<?= $d ?>" <?= ($filters['department'] ?? '') === $d ? 'selected' : '' ?>><?= $d ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">OJT</label>
                <select name="is_ojt" class="px-4 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500">
                    <option value="">All</option>
                    <option value="1" <?= ($filters['is_ojt'] ?? '') === '1' ? 'selected' : '' ?>>OJT Only</option>
                    <option value="0" <?= ($filters['is_ojt'] ?? '') === '0' ? 'selected' : '' ?>>Non-OJT</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                <input type="date" name="date_from" value="<?= htmlspecialchars($filters['date_from'] ?? '') ?>" class="px-4 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                <input type="date" name="date_to" value="<?= htmlspecialchars($filters['date_to'] ?? '') ?>" class="px-4 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" value="<?= htmlspecialchars($filters['search'] ?? '') ?>" placeholder="Name or email" class="px-4 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" class="px-4 py-2 rounded-xl btn-primary text-white font-medium">Filter</button>
            <a href="<?= base_path() ?>/admin/attendance?<?= http_build_query(array_merge($filters, ['export' => 'csv'])) ?>" class="px-4 py-2 rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium">Export CSV</a>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200" style="background: <?= $theme['primary'] ?? '#1E3A8A' ?>;">
                        <th class="text-left py-4 px-6 text-sm font-medium text-white">Date</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-white">Name</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-white">Department</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-white">OJT</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-white">Clock In</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-white">Clock Out</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-white">Total Hrs</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-white">OT Hrs</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $r): ?>
                    <tr class="border-b border-gray-50 hover:bg-gray-50/50">
                        <td class="py-4 px-6"><?= date('M d, Y', strtotime($r->date)) ?></td>
                        <td class="py-4 px-6 font-medium"><?= htmlspecialchars($r->name ?? '') ?></td>
                        <td class="py-4 px-6"><?= htmlspecialchars($r->department ?? '') ?></td>
                        <td class="py-4 px-6"><?= $r->is_ojt ? '<span class="px-2 py-1 rounded badge-accent text-sm">OJT</span>' : '-' ?></td>
                        <td class="py-4 px-6"><?= $r->clock_in_time ? date('h:i A', strtotime($r->clock_in_time)) : '-' ?></td>
                        <td class="py-4 px-6"><?= $r->clock_out_time ? date('h:i A', strtotime($r->clock_out_time)) : '-' ?></td>
                        <td class="py-4 px-6 font-medium"><?= $r->total_hours ? number_format((float)$r->total_hours, 1) : '-' ?></td>
                        <td class="py-4 px-6"><?= $r->overtime_hours ? number_format((float)$r->overtime_hours, 1) : '-' ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($records)): ?>
                    <tr><td colspan="8" class="py-12 text-center text-gray-500">No attendance records found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$theme = \App\Core\ThemeHelper::getTheme($user);
require __DIR__ . '/../layouts/main.php';
?>
