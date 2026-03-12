<?php
/**
 * Application Configuration
 * MM&Co Accounting Review Center Management System
 */

return [
    'name' => 'MM&Co Accounting Review Center',
    'url' => 'http://localhost',
    'timezone' => 'Asia/Manila',
    'debug' => true,
    'session_lifetime' => 7200, // 2 hours
    'min_clock_hours' => 5, // Minimum hours before clock out allowed
    'overtime_multiplier' => 1.5, // 1.5x for overtime
    'regular_hourly_rate' => 500, // Default PHP per hour
    'departments' => ['IT', 'Accounting', 'HR'],
];
