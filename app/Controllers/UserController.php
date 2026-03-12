<?php
/**
 * User Management Controller (Admin)
 * MM&Co Accounting Review Center Management System
 */

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Admin: User list
     */
    public function adminIndex(): void
    {
        Auth::requireAdmin();

        $userModel = new User();
        $users = $userModel->getEmployees();
        $ojts = $userModel->getOJTs();

        $this->view('users.admin', [
            'users' => $users,
            'ojts' => $ojts,
        ]);
    }

    /**
     * Store new user
     */
    public function store(): void
    {
        Auth::requireAdmin();
        $this->requireCsrf();

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'employee';
        $department = $_POST['department'] ?? '';
        $is_ojt = isset($_POST['is_ojt']) ? 1 : 0;
        $required_hours = $is_ojt ? (int) ($_POST['required_hours'] ?? 0) : null;
        $ojt_start_date = $is_ojt ? ($_POST['ojt_start_date'] ?? null) : null;

        if (empty($name) || empty($email) || empty($password)) {
            $_SESSION['error'] = 'Name, email, and password are required.';
            $this->redirect('/admin/users');
        }

        if (!in_array($department, $this->config['departments'])) {
            $_SESSION['error'] = 'Invalid department.';
            $this->redirect('/admin/users');
        }

        $userModel = new User();
        if ($userModel->findByEmail($email)) {
            $_SESSION['error'] = 'Email already exists.';
            $this->redirect('/admin/users');
        }

        $userModel->create([
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'role' => 'employee', // Only one admin
            'department' => $department,
            'is_ojt' => $is_ojt,
            'required_hours' => $required_hours,
            'ojt_start_date' => $ojt_start_date ?: null,
        ]);

        $_SESSION['success'] = 'User created successfully.';
        $this->redirect('/admin/users');
    }
}
