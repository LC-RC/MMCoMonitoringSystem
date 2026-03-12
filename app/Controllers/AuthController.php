<?php
/**
 * Auth Controller
 * Login, logout, session handling
 * MM&Co Accounting Review Center Management System
 */

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Department;
use App\Models\OjtKind;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin(): void
    {
        if (Auth::check()) {
            $this->redirect(Auth::isAdmin() ? '/admin/dashboard' : '/dashboard');
        }
        $errorEmail = $_SESSION['login_error_email'] ?? null;
        $errorPassword = $_SESSION['login_error_password'] ?? null;
        $errorGeneral = $_SESSION['error'] ?? null;
        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['login_error_email'], $_SESSION['login_error_password'], $_SESSION['error'], $_SESSION['success']);
        $this->view('auth.login', [
            'errorEmail' => $errorEmail,
            'errorPassword' => $errorPassword,
            'errorGeneral' => $errorGeneral,
            'success' => $success,
        ]);
    }

    /**
     * Process login – sets field-specific errors for email vs password
     */
    public function login(): void
    {
        $this->requireCsrf();

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Field-specific validation
        if (empty($email)) {
            $_SESSION['login_error_email'] = 'Email is required.';
            $this->redirect('/login');
        }

        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['login_error_email'] = 'Please enter a valid email address.';
            $this->redirect('/login');
        }

        if (empty($password)) {
            $_SESSION['login_error_password'] = 'Password is required.';
            $this->redirect('/login');
        }

        try {
            $result = Auth::attemptWithReason($email, $password);
            if (!$result['success']) {
                if ($result['reason'] === 'email_not_found') {
                    $_SESSION['login_error_email'] = 'Email address not found.';
                } else {
                    $_SESSION['login_error_password'] = 'Incorrect password.';
                }
                $this->redirect('/login');
            }
        } catch (\Throwable $e) {
            $_SESSION['login_error_email'] = 'Unable to sign in. Please check that the database is set up and try again.';
            $this->redirect('/login');
        }

        unset($_SESSION['login_error_email'], $_SESSION['login_error_password']);
        $redirect = $_SESSION['redirect_after_login'] ?? null;
        unset($_SESSION['redirect_after_login']);

        if ($redirect) {
            $this->redirect($redirect);
        }

        $this->redirect(Auth::isAdmin() ? '/admin/dashboard' : '/dashboard');
    }

    /**
     * Logout
     */
    public function logout(): void
    {
        Auth::logout();
    }

    /**
     * GET /register/next-employee-id?role=employee|ojt&department=IT|Accounting|HR|Other
     * Returns JSON: { "employee_id": "EI2601" }
     */
    public function nextEmployeeId(): void
    {
        header('Content-Type: application/json');
        $role = trim($_GET['role'] ?? '');
        $department = trim($_GET['department'] ?? '');
        if (!in_array($role, ['employee', 'ojt'], true)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid role.']);
            return;
        }
        $allowedDepts = ['IT', 'Accounting', 'HR', 'Other'];
        if ($department === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Department is required.']);
            return;
        }
        if (!in_array($department, $allowedDepts, true)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid department.']);
            return;
        }
        try {
            $userModel = new User();
            $employeeId = $userModel->getNextEmployeeId($role, $department);
            echo json_encode(['employee_id' => $employeeId]);
        } catch (\Throwable $e) {
            $roleCode = ($role === 'ojt') ? 'O' : 'E';
            $deptCode = User::departmentToCode($department);
            $yy = date('y');
            $fallbackId = $roleCode . $deptCode . $yy . '01';
            echo json_encode(['employee_id' => $fallbackId]);
        }
    }

    /**
     * Show register form (multi-step wizard)
     */
    public function showRegister(): void
    {
        if (Auth::check()) {
            $this->redirect(Auth::isAdmin() ? '/admin/dashboard' : '/dashboard');
        }

        $errors = $_SESSION['register_errors'] ?? [];
        $old = $_SESSION['register_old'] ?? [];
        $activeStep = $_SESSION['register_step'] ?? 1;
        $errorGeneral = $_SESSION['error'] ?? null;
        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['register_errors'], $_SESSION['register_old'], $_SESSION['register_step'], $_SESSION['error'], $_SESSION['success']);

        $departments = $this->config['departments'] ?? ['IT', 'Accounting', 'HR'];
        $ojtKinds = ['IT OJT', 'Accounting OJT', 'HR OJT'];

        try {
            $depModel = new Department();
            $dbDepartments = $depModel->allNames();
            if (!empty($dbDepartments)) {
                $departments = $dbDepartments;
            }
        } catch (\Throwable $e) {
            // fallback to config
        }

        try {
            $kindModel = new OjtKind();
            $dbKinds = $kindModel->allNames();
            if (!empty($dbKinds)) {
                $ojtKinds = $dbKinds;
            }
        } catch (\Throwable $e) {
            // fallback to defaults
        }

        $this->view('auth.register', [
            'errors' => $errors,
            'old' => $old,
            'activeStep' => $activeStep,
            'departments' => $departments,
            'ojtKinds' => $ojtKinds,
            'errorGeneral' => $errorGeneral,
            'success' => $success,
        ]);
    }

    /**
     * Handle registration
     */
    public function register(): void
    {
        $this->requireCsrf();

        $data = [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'middle_name' => trim($_POST['middle_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'personal_email' => trim($_POST['personal_email'] ?? ''),
            'birthday' => trim($_POST['birthday'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'gender' => trim($_POST['gender'] ?? ''),
            'civil_status' => trim($_POST['civil_status'] ?? ''),
            'mmco_email' => trim($_POST['mmco_email'] ?? ''),
            'password' => (string) ($_POST['password'] ?? ''),
            'confirm_password' => (string) ($_POST['confirm_password'] ?? ''),
            'user_type' => $_POST['user_type'] ?? '',
            'department' => $_POST['department'] ?? '',
            'department_other' => trim($_POST['department_other'] ?? ''),
            'ojt_kind' => $_POST['ojt_kind'] ?? '',
            'ojt_kind_other' => trim($_POST['ojt_kind_other'] ?? ''),
            'school_name' => trim($_POST['school_name'] ?? ''),
            'hours_needed' => trim($_POST['hours_needed'] ?? ''),
            'start_date' => trim($_POST['start_date'] ?? ''),
            'end_date' => trim($_POST['end_date'] ?? ''),
            'contact_number' => trim($_POST['contact_number'] ?? ''),
            'employee_id' => trim($_POST['employee_id'] ?? ''),
            'terms' => isset($_POST['terms']) ? '1' : '0',
        ];

        $errors = [];

        $requiredStep1 = ['first_name', 'last_name', 'personal_email', 'mmco_email', 'password', 'confirm_password'];
        foreach ($requiredStep1 as $k) {
            if ($data[$k] === '') {
                $errors[$k] = 'This field is required.';
            }
        }
        if ($data['first_name'] !== '' && strlen($data['first_name']) < 3) {
            $errors['first_name'] = 'First name must be at least 3 characters.';
        }
        if ($data['last_name'] !== '' && strlen($data['last_name']) < 3) {
            $errors['last_name'] = 'Last name must be at least 3 characters.';
        }
        if ($data['middle_name'] !== '' && strlen($data['middle_name']) < 3) {
            $errors['middle_name'] = 'Middle name must be at least 3 characters if provided.';
        }
        if ($data['address'] !== '' && strlen($data['address']) < 15) {
            $errors['address'] = 'Address must be at least 15 characters to ensure a complete address.';
        }
        if ($data['personal_email'] !== '' && !filter_var($data['personal_email'], FILTER_VALIDATE_EMAIL)) {
            $errors['personal_email'] = 'Please enter a valid email address.';
        }
        if ($data['personal_email'] !== '' && !isset($errors['personal_email'])) {
            $localPart = explode('@', $data['personal_email'], 2)[0] ?? '';
            if (strlen($localPart) < 8) {
                $errors['personal_email'] = 'Email is too short.';
            }
        }
        if ($data['mmco_email'] !== '' && !filter_var($data['mmco_email'], FILTER_VALIDATE_EMAIL)) {
            $errors['mmco_email'] = 'Please enter a valid email address.';
        }
        if ($data['birthday'] === '') {
            $errors['birthday'] = 'Birthday is required.';
        } elseif ($data['birthday'] !== '') {
            $birth = \DateTime::createFromFormat('Y-m-d', $data['birthday']);
            if (!$birth) {
                $errors['birthday'] = 'Please enter a valid date.';
            } else {
                $today = new \DateTime('today');
                $age = $today->diff($birth)->y;
                if ($age < 19 || $age > 100) {
                    $errors['birthday'] = 'Age must be between 19 and 100 years old.';
                }
            }
        }
        if ($data['address'] === '') {
            $errors['address'] = 'Address is required.';
        }
        if ($data['gender'] === '') {
            $errors['gender'] = 'Please select gender.';
        } elseif (!in_array($data['gender'], ['Female', 'Male'], true)) {
            $errors['gender'] = 'Invalid gender.';
        }
        if ($data['civil_status'] === '') {
            $errors['civil_status'] = 'Please select civil status.';
        } elseif (!in_array($data['civil_status'], ['Single', 'Married', 'Widowed'], true)) {
            $errors['civil_status'] = 'Invalid civil status.';
        }
        if ($data['password'] !== '' && strlen($data['password']) < 6) {
            $errors['password'] = 'Password must be at least 6 characters.';
        }
        if ($data['password'] !== $data['confirm_password']) {
            $errors['confirm_password'] = 'Passwords do not match.';
        }

        if (!in_array($data['user_type'], ['employee', 'ojt'], true)) {
            $errors['user_type'] = 'Please select a user type.';
        }

        // Resolve department
        $department = $data['department'];
        if ($department === 'Other') {
            if ($data['department_other'] === '') {
                $errors['department_other'] = 'Please enter a department.';
            }
            $department = $data['department_other'];
        }
        if ($department === '') {
            $errors['department'] = 'Please select a department.';
        }

        $isOjt = $data['user_type'] === 'ojt';
        $ojtKind = null;
        if ($isOjt) {
            // Derive OJT kind from department instead of asking user to pick an explicit "Intern Type"
            if ($department !== '') {
                switch ($department) {
                    case 'IT':
                        $ojtKind = 'IT OJT';
                        break;
                    case 'Accounting':
                        $ojtKind = 'Accounting OJT';
                        break;
                    case 'HR':
                        $ojtKind = 'HR OJT';
                        break;
                    default:
                        $ojtKind = $department . ' OJT';
                        break;
                }
            }
            if ($data['school_name'] === '') {
                $errors['school_name'] = 'School name is required.';
            }
            if ($data['hours_needed'] === '' || !ctype_digit($data['hours_needed']) || (int) $data['hours_needed'] <= 0) {
                $errors['hours_needed'] = 'Please enter valid hours needed.';
            }
        }

        if ($data['contact_number'] === '') {
            $errors['contact_number'] = 'Contact number is required.';
        }
        if ($data['terms'] !== '1') {
            $errors['terms'] = 'You must accept the terms.';
        }

        // Persist old inputs for repopulation (exclude passwords)
        $old = $data;
        unset($old['password'], $old['confirm_password']);

        if (!empty($errors)) {
            $_SESSION['register_errors'] = $errors;
            $_SESSION['register_old'] = $old;
            $_SESSION['register_step'] = (int) ($_POST['active_step'] ?? 1);
            $this->redirect('/register');
        }

        // Ensure MMCO email is unique (stored as primary email)
        $userModel = new User();
        if ($userModel->findByEmail($data['mmco_email'])) {
            $_SESSION['register_errors'] = ['mmco_email' => 'MMCO email already exists.'];
            $_SESSION['register_old'] = $old;
            $_SESSION['register_step'] = 1;
            $this->redirect('/register');
        }

        // Optional photo upload
        $photoPath = null;
        if (!empty($_FILES['profile_photo']) && is_array($_FILES['profile_photo']) && ($_FILES['profile_photo']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            if (($_FILES['profile_photo']['error'] ?? UPLOAD_ERR_OK) === UPLOAD_ERR_OK) {
                $tmp = $_FILES['profile_photo']['tmp_name'] ?? '';
                $name = $_FILES['profile_photo']['name'] ?? '';
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                if (!in_array($ext, $allowed, true)) {
                    $_SESSION['register_errors'] = ['profile_photo' => 'Invalid photo format.'];
                    $_SESSION['register_old'] = $old;
                    $_SESSION['register_step'] = 4;
                    $this->redirect('/register');
                }
                $uploadDir = dirname(__DIR__, 2) . '/public/uploads/profile_photos';
                if (!is_dir($uploadDir)) {
                    @mkdir($uploadDir, 0777, true);
                }
                $fileName = 'pp_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $dest = $uploadDir . '/' . $fileName;
                if (!@move_uploaded_file($tmp, $dest)) {
                    $_SESSION['register_errors'] = ['profile_photo' => 'Unable to upload photo.'];
                    $_SESSION['register_old'] = $old;
                    $_SESSION['register_step'] = 4;
                    $this->redirect('/register');
                }
                $photoPath = '/uploads/profile_photos/' . $fileName;
            }
        }

        // Save dynamic options if provided
        try {
            if ($data['department'] === 'Other' && $data['department_other'] !== '') {
                (new Department())->createIfNotExists($data['department_other']);
            }
        } catch (\Throwable $e) {
            // ignore if table not present
        }
        try {
            if ($isOjt && $ojtKind) {
                (new OjtKind())->createIfNotExists($ojtKind);
            }
        } catch (\Throwable $e) {
            // ignore if table not present
        }

        // Create user (note: additional columns require DB migration)
        $fullName = trim($data['first_name'] . ' ' . ($data['middle_name'] !== '' ? ($data['middle_name'] . ' ') : '') . $data['last_name']);
        $createData = [
            'name' => $fullName,
            'email' => $data['mmco_email'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            'role' => 'employee',
            'department' => $department,
            'is_ojt' => $isOjt ? 1 : 0,
            'required_hours' => $isOjt ? (int) $data['hours_needed'] : null,
            'ojt_start_date' => ($isOjt && $data['start_date'] !== '') ? $data['start_date'] : null,
        ];

        // Generate Employee ID server-side (avoids races; do not trust client value for insert)
        $employeeId = $userModel->getNextEmployeeId($data['user_type'], $department);

        // Optional / extended fields (will work after migration)
        $createDataExtended = [
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'] !== '' ? $data['middle_name'] : null,
            'last_name' => $data['last_name'],
            'personal_email' => $data['personal_email'],
            'contact_number' => $data['contact_number'],
            'birthday' => $data['birthday'] !== '' ? $data['birthday'] : null,
            'address' => $data['address'] !== '' ? $data['address'] : null,
            'gender' => $data['gender'] !== '' ? $data['gender'] : null,
            'civil_status' => $data['civil_status'] !== '' ? $data['civil_status'] : null,
            'id_number' => $employeeId,
            'employee_id' => $employeeId,
            'profile_photo_path' => $photoPath,
            'school_name' => $isOjt ? $data['school_name'] : null,
            'ojt_kind' => $isOjt ? $ojtKind : null,
            'ojt_end_date' => ($isOjt && $data['end_date'] !== '') ? $data['end_date'] : null,
        ];

        $createPayload = array_merge($createData, array_filter($createDataExtended, fn($v) => $v !== null));
        try {
            // Try inserting with extended columns first
            $userModel->create($createPayload);
        } catch (\Throwable $e) {
            $code = $e->getCode();
            $isDuplicate = (strpos($e->getMessage(), 'Duplicate') !== false || $code === '23000' || (int) $code === 23000);
            if ($isDuplicate) {
                $employeeId = $userModel->getNextEmployeeId($data['user_type'], $department);
                $createDataExtended['id_number'] = $employeeId;
                $createDataExtended['employee_id'] = $employeeId;
                $createPayload = array_merge($createData, array_filter($createDataExtended, fn($v) => $v !== null));
                try {
                    $userModel->create($createPayload);
                } catch (\Throwable $e2) {
                    $_SESSION['error'] = 'Registration failed due to a conflict. Please try again.';
                    $_SESSION['register_old'] = $old;
                    $_SESSION['register_step'] = 3;
                    $this->redirect('/register');
                }
            } else {
                // Fallback to minimal columns (for pre-migration DB)
                try {
                    $userModel->create($createData);
                } catch (\Throwable $e2) {
                    $_SESSION['error'] = 'Unable to create account. Please ensure the database has been migrated.';
                    $_SESSION['register_old'] = $old;
                    $_SESSION['register_step'] = 1;
                    $this->redirect('/register');
                }
            }
        }

        $_SESSION['success'] = 'Account Successfully Created';
        $_SESSION['register_step'] = 1;
        $_SESSION['register_old'] = [];
        $this->redirect('/register');
    }
}
