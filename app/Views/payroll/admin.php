<?php
$title = 'Payroll';
$pageTitle = 'Payroll';
ob_start();
?>

<div class="space-y-6">
    <!-- Period Filter -->
    <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
        <div class="flex flex-wrap gap-4 items-end">
            <form method="GET" action="<?= base_path() ?>/admin/payroll" class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Period Start</label>
                    <input type="date" name="period_start" value="<?= htmlspecialchars($periodStart ?? '') ?>" class="px-4 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Period End</label>
                    <input type="date" name="period_end" value="<?= htmlspecialchars($periodEnd ?? '') ?>" class="px-4 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500">
                </div>
                <button type="submit" class="px-4 py-2 rounded-xl btn-primary text-white font-medium">View</button>
            </form>
            <form action="<?= base_path() ?>/admin/payroll/generate" method="POST" class="inline">
                <input type="hidden" name="_token" value="<?= \App\Core\Auth::csrfToken() ?>">
                <input type="hidden" name="period_start" value="<?= htmlspecialchars($periodStart ?? '') ?>">
                <input type="hidden" name="period_end" value="<?= htmlspecialchars($periodEnd ?? '') ?>">
                <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-gray-900 font-medium">Generate Payroll</button>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200" style="background: <?= $theme['primary'] ?? '#1E3A8A' ?>;">
                        <th class="text-left py-4 px-6 text-sm font-medium text-white">Employee</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-white">Department</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-white">Regular Hrs</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-white">OT Hrs</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-white">Gross Pay</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-white">Deductions</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-white">Net Pay</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-white">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $r): ?>
                    <tr class="border-b border-gray-50 hover:bg-gray-50/50">
                        <td class="py-4 px-6 font-medium"><?= htmlspecialchars($r->name ?? '') ?></td>
                        <td class="py-4 px-6"><?= htmlspecialchars($r->department ?? '') ?></td>
                        <td class="py-4 px-6"><?= number_format((float)($r->regular_hours ?? 0), 1) ?></td>
                        <td class="py-4 px-6"><?= number_format((float)($r->overtime_hours ?? 0), 1) ?></td>
                        <td class="py-4 px-6 font-medium">₱<?= number_format((float)($r->gross_pay ?? 0), 2) ?></td>
                        <td class="py-4 px-6">₱<?= number_format((float)($r->deductions ?? 0), 2) ?></td>
                        <td class="py-4 px-6 font-bold">₱<?= number_format((float)($r->net_pay ?? 0), 2) ?></td>
                        <td class="py-4 px-6">
                            <a href="<?= base_path() ?>/admin/payroll/payslip/<?= $r->id ?>" class="text-sm font-medium" style="color: <?= $theme['primary'] ?? '#1E3A8A' ?>">Payslip</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($records)): ?>
                    <tr><td colspan="8" class="py-12 text-center text-gray-500">No payroll records for this period. Click "Generate Payroll" to create.</td></tr>
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
