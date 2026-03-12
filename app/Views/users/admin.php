<?php
$title = 'Users';
$pageTitle = 'Users';
ob_start();
?>

<div class="space-y-6">
    <!-- Add User -->
    <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
        <h3 class="font-semibold text-gray-800 mb-4" style="color: <?= $theme['primary'] ?? '#1E3A8A' ?>">Add New User</h3>
        <form action="<?= base_path() ?>/admin/users" method="POST" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <input type="hidden" name="_token" value="<?= \App\Core\Auth::csrfToken() ?>">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" name="name" required class="w-full px-4 py-2 rounded-xl border border-gray-300" placeholder="Juan Dela Cruz">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" required class="w-full px-4 py-2 rounded-xl border border-gray-300" placeholder="juan@mmco.com">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" required class="w-full px-4 py-2 rounded-xl border border-gray-300" placeholder="••••••••">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                <select name="department" required class="w-full px-4 py-2 rounded-xl border border-gray-300">
                    <option value="">Select</option>
                    <?php foreach (['IT', 'Accounting', 'HR'] as $d): ?>
                    <option value="<?= $d ?>"><?= $d ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_ojt" value="1" id="isOjt" class="rounded">
                    <span class="text-sm font-medium text-gray-700">OJT</span>
                </label>
            </div>
            <div id="ojtFields" class="md:col-span-2 hidden space-y-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Required Hours</label>
                    <input type="number" name="required_hours" min="0" class="w-full px-4 py-2 rounded-xl border border-gray-300" placeholder="e.g. 500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" name="ojt_start_date" class="w-full px-4 py-2 rounded-xl border border-gray-300">
                </div>
            </div>
            <div class="md:col-span-4">
                <button type="submit" class="px-4 py-2 rounded-xl btn-primary text-white font-medium">Add User</button>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
        <h3 class="font-semibold text-gray-800 p-6 pb-0" style="color: <?= $theme['primary'] ?? '#1E3A8A' ?>">Employees</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200" style="background: <?= $theme['primary'] ?? '#1E3A8A' ?>;">
                        <th class="text-left py-4 px-6 text-sm font-medium text-white">Name</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-white">Email</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-white">Department</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-white">Type</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-white">OJT Hours</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr class="border-b border-gray-50 hover:bg-gray-50/50">
                        <td class="py-4 px-6 font-medium"><?= htmlspecialchars($u->name) ?></td>
                        <td class="py-4 px-6"><?= htmlspecialchars($u->email) ?></td>
                        <td class="py-4 px-6"><?= htmlspecialchars($u->department) ?></td>
                        <td class="py-4 px-6">
                            <?php if ($u->is_ojt): ?>
                            <span class="px-2 py-1 rounded-lg <?= \App\Core\ThemeHelper::getOjtBadgeColor($u->department) ?> text-white text-sm font-medium">OJT</span>
                            <?php else: ?>
                            <span class="px-2 py-1 rounded-lg bg-gray-200 text-gray-700 text-sm">Staff</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-4 px-6"><?= $u->is_ojt ? ($u->required_hours ?? '-') . 'h' : '-' ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($users)): ?>
                    <tr><td colspan="5" class="py-12 text-center text-gray-500">No employees</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
(function() {
    var isOjt = document.getElementById('isOjt');
    var ojtFields = document.getElementById('ojtFields');
    if (isOjt && ojtFields) {
        isOjt.addEventListener('change', function() {
            ojtFields.classList.toggle('hidden', !this.checked);
        });
    }
})();
</script>

<?php
$content = ob_get_clean();
$theme = \App\Core\ThemeHelper::getTheme($user);
require __DIR__ . '/../layouts/main.php';
?>
