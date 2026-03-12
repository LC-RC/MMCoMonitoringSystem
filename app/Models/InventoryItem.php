<?php
/**
 * Inventory Item Model
 * MM&Co Accounting Review Center Management System
 */

namespace App\Models;

use App\Core\Model;
use PDO;

class InventoryItem extends Model
{
    protected string $table = 'inventory_items';

    /**
     * Get low stock items
     */
    public function getLowStock(): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM {$this->table} WHERE quantity <= low_stock_threshold ORDER BY quantity ASC"
        );
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get all with optional filters
     */
    public function getAllFiltered(array $filters = []): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];

        if (!empty($filters['category'])) {
            $sql .= " AND category = ?";
            $params[] = $filters['category'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (name LIKE ? OR category LIKE ?)";
            $term = '%' . $filters['search'] . '%';
            $params[] = $term;
            $params[] = $term;
        }
        if (isset($filters['low_stock']) && $filters['low_stock']) {
            $sql .= " AND quantity <= low_stock_threshold";
        }

        $sql .= " ORDER BY name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
