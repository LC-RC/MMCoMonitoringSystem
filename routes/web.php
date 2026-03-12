<?php
/**
 * Web Routes
 * MM&Co Accounting Review Center Management System
 */

use App\Core\Router;

$router = new Router();

// Auth (no middleware - guests)
$router->get('/', [\App\Controllers\AuthController::class, 'showLogin']);
$router->get('/login', [\App\Controllers\AuthController::class, 'showLogin']);
$router->post('/login', [\App\Controllers\AuthController::class, 'login']);
$router->get('/register', [\App\Controllers\AuthController::class, 'showRegister']);
$router->get('/register/next-employee-id', [\App\Controllers\AuthController::class, 'nextEmployeeId']);
$router->post('/register', [\App\Controllers\AuthController::class, 'register']);

// Logout (auth required)
$router->post('/logout', [\App\Controllers\AuthController::class, 'logout'], ['AuthMiddleware']);

// Employee Dashboard (auth)
$router->get('/dashboard', [\App\Controllers\DashboardController::class, 'index'], ['AuthMiddleware']);

// Clock In/Out (auth)
$router->post('/attendance/clock-in', [\App\Controllers\AttendanceController::class, 'clockIn'], ['AuthMiddleware']);
$router->post('/attendance/clock-out', [\App\Controllers\AttendanceController::class, 'clockOut'], ['AuthMiddleware']);

// Admin routes
$router->get('/admin/dashboard', [\App\Controllers\AdminController::class, 'dashboard'], ['AdminMiddleware']);
$router->get('/admin/attendance', [\App\Controllers\AttendanceController::class, 'adminIndex'], ['AdminMiddleware']);
$router->get('/admin/payroll', [\App\Controllers\PayrollController::class, 'adminIndex'], ['AdminMiddleware']);
$router->post('/admin/payroll/generate', [\App\Controllers\PayrollController::class, 'generate'], ['AdminMiddleware']);
$router->get('/admin/payroll/payslip/{id}', [\App\Controllers\PayrollController::class, 'payslip'], ['AuthMiddleware']);
$router->get('/admin/inventory', [\App\Controllers\InventoryController::class, 'adminIndex'], ['AdminMiddleware']);
$router->post('/admin/inventory', [\App\Controllers\InventoryController::class, 'store'], ['AdminMiddleware']);
$router->post('/admin/inventory/{id}', [\App\Controllers\InventoryController::class, 'update'], ['AdminMiddleware']);
$router->post('/admin/inventory/{id}/delete', [\App\Controllers\InventoryController::class, 'delete'], ['AdminMiddleware']);
$router->get('/admin/projects', [\App\Controllers\ProjectController::class, 'adminIndex'], ['AdminMiddleware']);
$router->get('/admin/projects/{id}', [\App\Controllers\ProjectController::class, 'show'], ['AdminMiddleware']);
$router->post('/admin/projects', [\App\Controllers\ProjectController::class, 'store'], ['AdminMiddleware']);
$router->post('/admin/projects/{id}/task', [\App\Controllers\ProjectController::class, 'storeTask'], ['AdminMiddleware']);
$router->post('/admin/projects/task/{id}/phase', [\App\Controllers\ProjectController::class, 'updateTaskPhase'], ['AdminMiddleware']);
$router->get('/admin/users', [\App\Controllers\UserController::class, 'adminIndex'], ['AdminMiddleware']);
$router->post('/admin/users', [\App\Controllers\UserController::class, 'store'], ['AdminMiddleware']);

// Payslip (auth - employees see own)
$router->get('/payroll/payslip/{id}', [\App\Controllers\PayrollController::class, 'payslip'], ['AuthMiddleware']);

$router->dispatch();
