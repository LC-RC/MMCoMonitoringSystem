<?php
/**
 * Base Model
 * PDO-based with prepared statements
 * MM&Co Accounting Review Center Management System
 */

namespace App\Core;

use PDO;

abstract class Model
{
    protected PDO $db;
    protected string $table;
    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Find record by primary key
     */
    public function find(int $id): ?object
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result ?: null;
    }

    /**
     * Get all records
     */
    public function all(string $orderBy = 'id', string $direction = 'ASC'): array
    {
        $allowed = ['id', 'name', 'email', 'created_at', 'date'];
        $orderBy = in_array($orderBy, $allowed) ? $orderBy : 'id';
        $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
        $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY {$orderBy} {$direction}");
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Insert record
     */
    public function create(array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($data));
        return (int) $this->db->lastInsertId();
    }

    /**
     * Update record
     */
    public function update(int $id, array $data): bool
    {
        $sets = implode(' = ?, ', array_keys($data)) . ' = ?';
        $sql = "UPDATE {$this->table} SET {$sets} WHERE {$this->primaryKey} = ?";
        $stmt = $this->db->prepare($sql);
        $values = array_values($data);
        $values[] = $id;
        return $stmt->execute($values);
    }

    /**
     * Delete record
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Execute raw query
     */
    protected function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}
