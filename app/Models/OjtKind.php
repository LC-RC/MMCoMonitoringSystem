<?php
/**
 * OJT Kind Model
 * Internship types for registration (e.g. IT OJT, Accounting OJT).
 */

namespace App\Models;

use App\Core\Model;
use PDO;

class OjtKind extends Model
{
    protected string $table = 'ojt_kinds';

    public function allNames(): array
    {
        $stmt = $this->db->query("SELECT name FROM {$this->table} ORDER BY name ASC");
        return array_values(array_filter(array_map(fn($r) => $r['name'] ?? null, $stmt->fetchAll(PDO::FETCH_ASSOC))));
    }

    public function createIfNotExists(string $name): void
    {
        $name = trim($name);
        if ($name === '') {
            return;
        }

        $stmt = $this->db->prepare("SELECT id FROM {$this->table} WHERE name = ? LIMIT 1");
        $stmt->execute([$name]);
        $exists = $stmt->fetchColumn();
        if ($exists) {
            return;
        }

        $this->create(['name' => $name]);
    }
}
