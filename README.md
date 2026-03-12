# MM&Co Accounting Review Center Management System

A modern, modular PHP MVC management system for an Accounting Review Center.

## Features

- **Authentication**: Secure session-based login with bcrypt
- **Role-based dashboards**: Admin and Employee
- **Clock In/Out**: 5-hour minimum rule before clock out
- **Dynamic theming**: Department-based UI (Admin: Blue+Yellow, IT: Maroon, Accounting: Yellow, HR: Blue)
- **OJT tracking**: Required hours, progress bar, completion alerts
- **Admin attendance**: Filter by department, OJT, date, search; Export CSV
- **Payroll**: Regular/OT hours, gross/net pay, payslip
- **Inventory**: Add/edit/delete, low stock alerts
- **Projects**: Kanban board, work phases (To Do, In Progress, Review, Completed)

## Requirements

- PHP 8.0+
- MySQL 5.7+ / MariaDB
- Apache with mod_rewrite (or equivalent)

## Installation

**Quick start (XAMPP):** Open **http://localhost/MMCoMonitoringSystem/** in your browser. The folder name must be `MMCoMonitoringSystem` (no spaces or `&`).

### 1. Database

```bash
mysql -u root -p < database/schema.sql
mysql -u root -p mmco_accounting_system < database/seed.sql
```

### 2. Configuration

Edit `config/database.php` with your MySQL credentials:

```php
'host' => 'localhost',
'dbname' => 'mmco_accounting_system',
'username' => 'root',
'password' => 'your_password',
```

### 3. Web Server

**XAMPP / Apache (htdocs):**  
Place the project in `htdocs` (e.g. `c:\xampp\htdocs\MMCoMonitoringSystem`). Use this URL (folder name must match exactly, no spaces):

- **http://localhost/MMCoMonitoringSystem/**

**Apache VirtualHost** (optional – point document root to `public/`):
```apache
DocumentRoot /path/to/MMCoMonitoringSystem/public
<Directory /path/to/MMCoMonitoringSystem/public>
    AllowOverride All
    Require all granted
</Directory>
```

**PHP built-in server** (development):
```bash
cd public && php -S localhost:8000
```
Visit `http://localhost:8000` (or your domain).

## Demo Login

Default login (admin only):

| Email | Password | Role |
|-------|----------|------|
| admin.mmco@gmail.com | password123 | Admin |

## Project Structure

```
/app
  /Controllers    (Auth, Dashboard, Admin, Attendance, Payroll, Inventory, Project, User)
  /Core           (Database, Router, Auth, Controller, Model, ThemeHelper)
  /Middleware     (AuthMiddleware, AdminMiddleware)
  /Models         (User, Attendance, PayrollRecord, InventoryItem, Project, Department, OjtKind)
  /Views          (auth/, dashboard/, attendance/, payroll/, inventory/, projects/, users/, errors/, layouts/)
  helpers.php
/config           (app.php, database.php)
/database         (schema.sql, seed.sql, migrations)
/public
  index.php, .htaccess
  /assets
    /css          (layout.css; auth/login.css, auth/register.css)
    /images
/routes           (web.php)
index.php         (forwards to public/index.php)
router.php       (for PHP built-in server)
```

## Security

- bcrypt password hashing
- Prepared statements (PDO)
- CSRF tokens on forms
- Session regeneration on login
- Role-based route protection
