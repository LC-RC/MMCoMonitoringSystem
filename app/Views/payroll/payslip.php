<?php
$title = 'Payslip';
$pageTitle = 'Payslip - ' . htmlspecialchars($user->name ?? 'Employee');
ob_start();
?>

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-200" id="payslip">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold" style="color: <?= $theme['primary'] ?? '#1E3A8A' ?>">MM&Co Accounting Review Center</h1>
            <p class="text-gray-500 mt-1">Payslip</p>
        </div>
        <div class="grid grid-cols-2 gap-6 mb-8">
            <div>
                <p class="text-sm text-gray-500">Employee</p>
                <p class="font-semibold"><?= htmlspecialchars($user->name ?? '') ?></p>
                <p class="text-sm text-gray-600"><?= htmlspecialchars($user->department ?? '') ?></p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Pay Period</p>
                <p class="font-semibold"><?= date('M d', strtotime($record->period_start ?? '')) ?> - <?= date('M d, Y', strtotime($record->period_end ?? '')) ?></p>
            </div>
        </div>
        <table class="w-full">
            <tr class="border-b border-gray-200">
                <td class="py-3 text-gray-600">Regular Hours</td>
                <td class="py-3 text-right font-medium"><?= number_format((float)($record->regular_hours ?? 0), 1) ?>h</td>
            </tr>
            <tr class="border-b border-gray-200">
                <td class="py-3 text-gray-600">Overtime Hours</td>
                <td class="py-3 text-right font-medium"><?= number_format((float)($record->overtime_hours ?? 0), 1) ?>h</td>
            </tr>
            <tr class="border-b border-gray-200">
                <td class="py-3 text-gray-600">Gross Pay</td>
                <td class="py-3 text-right font-medium">₱<?= number_format((float)($record->gross_pay ?? 0), 2) ?></td>
            </tr>
            <tr class="border-b border-gray-200">
                <td class="py-3 text-gray-600">Deductions</td>
                <td class="py-3 text-right font-medium">₱<?= number_format((float)($record->deductions ?? 0), 2) ?></td>
            </tr>
            <tr class="bg-gray-50">
                <td class="py-4 font-semibold text-gray-800">Net Pay</td>
                <td class="py-4 text-right font-bold text-lg" style="color: <?= $theme['primary'] ?? '#1E3A8A' ?>">₱<?= number_format((float)($record->net_pay ?? 0), 2) ?></td>
            </tr>
        </table>
    </div>
    <div class="mt-6 text-center">
        <button onclick="window.print()" class="px-6 py-3 rounded-xl btn-primary text-white font-medium">Print / Save PDF</button>
        <a href="<?= base_path() . ($user && $user->role === 'admin' ? '/admin/payroll' : '/dashboard') ?>" class="ml-3 px-6 py-3 rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium inline-block">Back</a>
    </div>
</div>

<style media="print">
    body * { visibility: hidden; }
    #payslip, #payslip * { visibility: visible; }
    #payslip { position: absolute; left: 0; top: 0; }
</style>

<?php
$content = ob_get_clean();
$theme = \App\Core\ThemeHelper::getTheme($user ?? null);
require __DIR__ . '/../layouts/main.php';
?>
