<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$config = require dirname(__DIR__) . '/config/database.php';

$dsn = sprintf(
    'mysql:host=%s;dbname=%s;charset=%s',
    $config['host'],
    $config['dbname'],
    $config['charset']
);

try {
    $pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
    $stmt = $pdo->query('SELECT COUNT(*) AS cnt FROM users');
    $row = $stmt->fetch();
    echo 'OK - DB connected. users count = ' . ($row['cnt'] ?? 0);
} catch (Throwable $e) {
    http_response_code(500);
    echo 'ERROR - ' . $e->getMessage();
}