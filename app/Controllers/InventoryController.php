<?php
/**
 * Inventory Controller
 * MM&Co Accounting Review Center Management System
 */

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\InventoryItem;

class InventoryController extends Controller
{
    /**
     * Admin: Inventory list
     */
    public function adminIndex(): void
    {
        Auth::requireAdmin();

        $filters = [
            'category' => $_GET['category'] ?? '',
            'search' => trim($_GET['search'] ?? ''),
            'low_stock' => isset($_GET['low_stock']),
        ];

        $inventory = new InventoryItem();
        $items = $inventory->getAllFiltered($filters);

        $this->view('inventory.admin', [
            'items' => $items,
            'filters' => $filters,
        ]);
    }

    /**
     * Store new item
     */
    public function store(): void
    {
        Auth::requireAdmin();
        $this->requireCsrf();

        $name = trim($_POST['name'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $quantity = (int) ($_POST['quantity'] ?? 0);
        $low_stock_threshold = (int) ($_POST['low_stock_threshold'] ?? 10);
        $unit = trim($_POST['unit'] ?? 'pcs');
        $project_id = !empty($_POST['project_id']) ? (int) $_POST['project_id'] : null;
        $notes = trim($_POST['notes'] ?? '');

        if (empty($name) || empty($category)) {
            $_SESSION['error'] = 'Name and category are required.';
            $this->redirect('/admin/inventory');
        }

        $inventory = new InventoryItem();
        $inventory->create([
            'name' => $name,
            'category' => $category,
            'quantity' => max(0, $quantity),
            'low_stock_threshold' => max(0, $low_stock_threshold),
            'unit' => $unit ?: 'pcs',
            'project_id' => $project_id,
            'notes' => $notes,
        ]);

        $_SESSION['success'] = 'Item added successfully.';
        $this->redirect('/admin/inventory');
    }

    /**
     * Update item
     */
    public function update(int $id): void
    {
        Auth::requireAdmin();
        $this->requireCsrf();

        $inventory = new InventoryItem();
        $item = $inventory->find($id);
        if (!$item) {
            $_SESSION['error'] = 'Item not found.';
            $this->redirect('/admin/inventory');
        }

        $name = trim($_POST['name'] ?? $item->name);
        $category = trim($_POST['category'] ?? $item->category);
        $quantity = (int) ($_POST['quantity'] ?? $item->quantity);
        $low_stock_threshold = (int) ($_POST['low_stock_threshold'] ?? $item->low_stock_threshold);
        $unit = trim($_POST['unit'] ?? $item->unit);
        $project_id = !empty($_POST['project_id']) ? (int) $_POST['project_id'] : null;
        $notes = trim($_POST['notes'] ?? $item->notes ?? '');

        $inventory->update($id, [
            'name' => $name,
            'category' => $category,
            'quantity' => max(0, $quantity),
            'low_stock_threshold' => max(0, $low_stock_threshold),
            'unit' => $unit ?: 'pcs',
            'project_id' => $project_id,
            'notes' => $notes,
        ]);

        $_SESSION['success'] = 'Item updated successfully.';
        $this->redirect('/admin/inventory');
    }

    /**
     * Delete item
     */
    public function delete(int $id): void
    {
        Auth::requireAdmin();
        $this->requireCsrf();

        $inventory = new InventoryItem();
        if ($inventory->find($id)) {
            $inventory->delete($id);
            $_SESSION['success'] = 'Item deleted successfully.';
        }
        $this->redirect('/admin/inventory');
    }
}
