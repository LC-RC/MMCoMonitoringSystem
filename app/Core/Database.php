<?php
/**
 * Database Connection Class
 * PDO-based singleton for secure connections
 * MM&Co Accounting Review Center Management System
 */

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;
    private static array $config;

    /**
     * Get PDO connection instance
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            self::$config = require dirname(__DIR__, 2) . '/config/database.php';
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                self::$config['host'],
                self::$config['dbname'],
                self::$config['charset']
            );
            try {
                self::$instance = new PDO(
                    $dsn,
                    self::$config['username'],
                    self::$config['password'],
                    self::$config['options']
                );
            } catch (PDOException $e) {
                if (self::$config['options'][PDO::ATTR_ERRMODE] === PDO::ERRMODE_EXCEPTION) {
                    throw new PDOException('Database connection failed: ' . $e->getMessage());
                }
            }
        }
        return self::$instance;
    }

    /**
     * Prevent cloning
     */
    private function __clone() {}

    /**
     * Prevent unserialization
     */
    public function __wakeup()
    {
        throw new \Exception('Cannot unserialize singleton');
    }
}
