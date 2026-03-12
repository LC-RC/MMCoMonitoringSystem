<?php
$title = 'Inventory';
$pageTitle = 'Inventory';
ob_start();
?>

<div class="space-y-6">
    <!-- Add Item Modal Trigger + Filters -->
    <div class="flex flex-wrap justify-between gap-4 items-center">
        <form method="GET" action="<?= base_path() ?>/admin/inventory" class="flex flex-wrap gap-4 items-center">
            <input type="text" name="search" value="<?= htmlspecialchars($filters['search'] ?? '') ?>" placeholder="Search..." class="px-4 py-2 rounded-xl border border-gray-300">
            <input type="text" name="category" value="<?= htmlspecialchars($filters['category'] ?? '') ?>" placeholder="Category" class="px-4 py-2 rounded-xl border border-gray-300">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="low_stock" value="1" <?= ($filters['low_stock'] ?? false) ? 'checked' : '' ?> class="rounded">
                <span class="text-sm">Low stock only</span>
            </label>
            <button type="submit" class="px-4 py-2 rounded-xl btn-primary text-white font-medium">Filter</button>
        </form>
        <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="px-4 py-2 rounded-xl btn-primary text-white font-medium">+ Add Item</button>
    </div>

    <!-- Add Item Modal -->
    <div id="addModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
            <h3 class="text-xl font-semibold mb-4">Add Inventory Item</h3>
            <form action="<?= base_path() ?>/admin/inventory" method="POST">
                <input type="hidden" name="_token" value="<?= \App\Core\Auth::csrfToken() ?>">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" name="name" required class="w-full px-4 py-2 rounded-xl border border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <input type="text" name="category" required class="w-full px-4 py-2 rounded-xl border border-gray-300">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                            <input type="number" name="quantity" value="0" min="0" class="w-full px-4 py-2 rounded-xl border border-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Low Stock Threshold</label>
                            <input type="number" name="low_stock_threshold" value="10" min="0" class="w-full px-4 py-2 rounded-xl border border-gray-300">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                        <input type="text" name="unit" value="pcs" class="w-full px-4 py-2 rounded-xl border border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="notes" rows="2" class="w-full px-4 py-2 rounded-xl border border-gray-300"></textarea>
                    </div>
                </div>
                <div class="flex gap-2 mt-6">
                    <button type="submit" class="flex-1 px-4 py-2 rounded-xl btn-primary text-white font-medium">Add</button>
                    <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-gray-200 hover:bg-gray-300">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200" style="background: <?= $theme['primary'] ?? '#1E3A8A' ?>;">
                        <th class="text-left py-4 px-6 text-sm font-medium text-white">Name</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-white">Category</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-white">Quantity</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-white">Threshold</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-white">Status</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-white">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr class="border-b border-gray-50 hover:bg-gray-50/50">
                        <td class="py-4 px-6 font-medium"><?= htmlspecialchars($item->name) ?></td>
                        <td class="py-4 px-6"><?= htmlspecialchars($item->category) ?></td>
                        <td class="py-4 px-6"><?= $item->quantity ?> <?= htmlspecialchars($item->unit) ?></td>
                        <td class="py-4 px-6"><?= $item->low_stock_threshold ?></td>
                        <td class="py-4 px-6">
                            <?php if ($item->quantity <= $item->low_stock_threshold): ?>
                            <span class="px-2 py-1 rounded-lg bg-amber-100 text-amber-800 text-sm font-medium">Low Stock</span>
                            <?php else: ?>
                            <span class="px-2 py-1 rounded-lg bg-green-100 text-green-800 text-sm font-medium">In Stock</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-4 px-6">
                            <button onclick="document.getElementById('edit<?= $item->id ?>').classList.remove('hidden')" class="text-sm font-medium mr-2" style="color: <?= $theme['primary'] ?? '#1E3A8A' ?>">Edit</button>
                            <form action="<?= base_path() ?>/admin/inventory/<?= $item->id ?>/delete" method="POST" class="inline" onsubmit="return confirm('Delete this item?')">
                                <input type="hidden" name="_token" value="<?= \App\Core\Auth::csrfToken() ?>">
                                <button type="submit" class="text-sm font-medium text-red-600">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <!-- Edit Modal for item -->
                    <div id="edit<?= $item->id ?>" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
                            <h3 class="text-xl font-semibold mb-4">Edit Item</h3>
                            <form action="<?= base_path() ?>/admin/inventory/<?= $item->id ?>" method="POST">
                                <input type="hidden" name="_token" value="<?= \App\Core\Auth::csrfToken() ?>">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                        <input type="text" name="name" value="<?= htmlspecialchars($item->name) ?>" required class="w-full px-4 py-2 rounded-xl border border-gray-300">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                        <input type="text" name="category" value="<?= htmlspecialchars($item->category) ?>" required class="w-full px-4 py-2 rounded-xl border border-gray-300">
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                                            <input type="number" name="quantity" value="<?= $item->quantity ?>" min="0" class="w-full px-4 py-2 rounded-xl border border-gray-300">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Low Stock Threshold</label>
                                            <input type="number" name="low_stock_threshold" value="<?= $item->low_stock_threshold ?>" min="0" class="w-full px-4 py-2 rounded-xl border border-gray-300">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                                        <input type="text" name="unit" value="<?= htmlspecialchars($item->unit ?? 'pcs') ?>" class="w-full px-4 py-2 rounded-xl border border-gray-300">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                        <textarea name="notes" rows="2" class="w-full px-4 py-2 rounded-xl border border-gray-300"><?= htmlspecialchars($item->notes ?? '') ?></textarea>
                                    </div>
                                </div>
                                <div class="flex gap-2 mt-6">
                                    <button type="submit" class="flex-1 px-4 py-2 rounded-xl btn-primary text-white font-medium">Save</button>
                                    <button type="button" onclick="document.getElementById('edit<?= $item->id ?>').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-gray-200 hover:bg-gray-300">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($items)): ?>
                    <tr><td colspan="6" class="py-12 text-center text-gray-500">No inventory items</td></tr>
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
