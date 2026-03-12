<?php
/**
 * Database Configuration (example)
 * Copy this file to database.php and fill in your MySQL credentials.
 * Do not commit database.php – it is in .gitignore.
 */

return [
    'host' => 'localhost',
    'dbname' => 'mmco_accounting_system',
    'username' => 'root',
    'password' => 'your_password_here',
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],
];
