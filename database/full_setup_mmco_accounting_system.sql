-- MM&Co Accounting Review Center Management System
-- Full database setup script for MySQL Workbench
-- This combines schema, seed data, and all migrations.
--
-- HOW TO USE IN MYSQL WORKBENCH
-- 1. Open MySQL Workbench and connect to your server.
-- 2. File -> Open SQL Script... -> select this file:
--      database/full_setup_mmco_accounting_system.sql
-- 3. Click the lightning bolt (Execute) to run the entire script.
-- 4. When finished, you should see a database named: mmco_accounting_system
--
-- You can safely re-run this script; it uses DROP/IF NOT EXISTS/ON DUPLICATE
-- in key places so it won't usually fail if objects already exist.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ======================================================================
-- 1) CORE SCHEMA
-- ======================================================================

-- BEGIN: schema.sql
CREATE DATABASE IF NOT EXISTS mmco_accounting_system
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
USE mmco_accounting_system;

DROP TABLE IF EXISTS attendance;
DROP TABLE IF EXISTS payroll_records;
DROP TABLE IF EXISTS project_assignments;
DROP TABLE IF EXISTS task_assignments;
DROP TABLE IF EXISTS tasks;
DROP TABLE IF EXISTS projects;
DROP TABLE IF EXISTS inventory_items;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'employee') NOT NULL DEFAULT 'employee',
    department ENUM('IT', 'Accounting', 'HR') NOT NULL,
    is_ojt TINYINT(1) NOT NULL DEFAULT 0,
    required_hours INT UNSIGNED NULL,
    ojt_start_date DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_department (department),
    INDEX idx_is_ojt (is_ojt)
) ENGINE=InnoDB;

CREATE TABLE attendance (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    clock_in_time DATETIME NOT NULL,
    clock_out_time DATETIME NULL,
    total_hours DECIMAL(5,2) NULL,
    overtime_hours DECIMAL(5,2) DEFAULT 0,
    date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_date (user_id, date),
    INDEX idx_user_id (user_id),
    INDEX idx_date (date),
    INDEX idx_clock_in (clock_in_time)
) ENGINE=InnoDB;

CREATE TABLE projects (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    status ENUM('active', 'completed', 'on_hold') DEFAULT 'active',
    estimated_hours INT UNSIGNED NULL,
    actual_hours DECIMAL(8,2) DEFAULT 0,
    start_date DATE NULL,
    deadline DATE NULL,
    completed_at DATETIME NULL,
    created_by INT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_deadline (deadline)
) ENGINE=InnoDB;

CREATE TABLE tasks (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id INT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    phase ENUM('To Do', 'In Progress', 'Review', 'Completed') NOT NULL DEFAULT 'To Do',
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    estimated_hours DECIMAL(5,2) NULL,
    actual_hours DECIMAL(5,2) DEFAULT 0,
    sort_order INT UNSIGNED DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    INDEX idx_project_phase (project_id, phase)
) ENGINE=InnoDB;

CREATE TABLE project_assignments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    role ENUM('lead', 'member', 'ojt') DEFAULT 'member',
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_project_user (project_id, user_id)
) ENGINE=InnoDB;

CREATE TABLE task_assignments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    task_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_task_user (task_id, user_id)
) ENGINE=InnoDB;

CREATE TABLE payroll_records (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    period_start DATE NOT NULL,
    period_end DATE NOT NULL,
    regular_hours DECIMAL(6,2) NOT NULL DEFAULT 0,
    overtime_hours DECIMAL(6,2) NOT NULL DEFAULT 0,
    gross_pay DECIMAL(12,2) NOT NULL DEFAULT 0,
    deductions DECIMAL(12,2) DEFAULT 0,
    net_pay DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_period (user_id, period_start),
    INDEX idx_period (period_start, period_end)
) ENGINE=InnoDB;

CREATE TABLE inventory_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    quantity INT UNSIGNED NOT NULL DEFAULT 0,
    low_stock_threshold INT UNSIGNED NOT NULL DEFAULT 10,
    unit VARCHAR(50) DEFAULT 'pcs',
    project_id INT UNSIGNED NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL,
    INDEX idx_category (category),
    INDEX idx_quantity (quantity),
    INDEX idx_project (project_id)
) ENGINE=InnoDB;
-- END: schema.sql

-- ======================================================================
-- 2) SEED MOCK DATA
-- ======================================================================

-- BEGIN: seed.sql
USE mmco_accounting_system;

-- Password for admin: password123 (bcrypt)
SET @pw = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

-- Admin only (admin.mmco@gmail.com / password123)
INSERT INTO users (name, email, password, role, department, is_ojt, required_hours, ojt_start_date) VALUES
('Admin Owner', 'admin.mmco@gmail.com', @pw, 'admin', 'IT', 0, NULL, NULL);

-- Sample Projects
INSERT INTO projects (name, description, status, estimated_hours, deadline, created_by) VALUES
('Website Redesign', 'Main company website overhaul', 'active', 120, DATE_ADD(CURDATE(), INTERVAL 30 DAY), 1),
('ERP Integration', 'Integrate new accounting software', 'active', 200, DATE_ADD(CURDATE(), INTERVAL 60 DAY), 1);

INSERT INTO tasks (project_id, title, phase, priority, sort_order)
SELECT id, 'Design mockups', 'Completed', 'high', 0 FROM projects WHERE name = 'Website Redesign';
INSERT INTO tasks (project_id, title, phase, priority, sort_order)
SELECT id, 'Frontend development', 'In Progress', 'high', 1 FROM projects WHERE name = 'Website Redesign';
INSERT INTO tasks (project_id, title, phase, priority, sort_order)
SELECT id, 'Backend API', 'To Do', 'high', 2 FROM projects WHERE name = 'Website Redesign';
INSERT INTO tasks (project_id, title, phase, priority, sort_order)
SELECT id, 'Requirements gathering', 'Completed', 'high', 0 FROM projects WHERE name = 'ERP Integration';
INSERT INTO tasks (project_id, title, phase, priority, sort_order)
SELECT id, 'Database setup', 'In Progress', 'high', 1 FROM projects WHERE name = 'ERP Integration';

-- Sample Inventory
INSERT INTO inventory_items (name, category, quantity, low_stock_threshold, unit, notes) VALUES
('Laptop', 'Equipment', 15, 5, 'pcs', 'Development laptops'),
('Monitor', 'Equipment', 20, 8, 'pcs', '24-inch monitors'),
('Paper (Ream)', 'Office Supplies', 3, 10, 'ream', 'A4 bond paper'),
('Pens', 'Office Supplies', 50, 20, 'pcs', 'Ballpoint pens'),
('Printer Ink', 'Office Supplies', 2, 5, 'cartridge', 'HP 67 black');
-- END: seed.sql

-- ======================================================================
-- 3) REGISTER WIZARD / DYNAMIC TABLE MIGRATION
-- ======================================================================

-- BEGIN: migration_register_wizard.sql
USE mmco_accounting_system;

CREATE TABLE IF NOT EXISTS departments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS ojt_kinds (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT IGNORE INTO departments (name) VALUES ('HR'), ('IT'), ('Accounting');
INSERT IGNORE INTO ojt_kinds (name) VALUES ('IT OJT'), ('Accounting OJT'), ('HR OJT');

ALTER TABLE users
    MODIFY department VARCHAR(100) NOT NULL;

ALTER TABLE users
    ADD COLUMN first_name VARCHAR(100) NULL AFTER id,
    ADD COLUMN middle_name VARCHAR(100) NULL AFTER first_name,
    ADD COLUMN last_name VARCHAR(100) NULL AFTER middle_name,
    ADD COLUMN personal_email VARCHAR(255) NULL AFTER email,
    ADD COLUMN contact_number VARCHAR(50) NULL AFTER personal_email,
    ADD COLUMN id_number VARCHAR(80) NULL AFTER contact_number,
    ADD COLUMN profile_photo_path VARCHAR(255) NULL AFTER id_number,
    ADD COLUMN school_name VARCHAR(255) NULL AFTER profile_photo_path,
    ADD COLUMN ojt_kind VARCHAR(100) NULL AFTER school_name,
    ADD COLUMN ojt_end_date DATE NULL AFTER ojt_start_date;

CREATE INDEX idx_id_number ON users (id_number);
CREATE INDEX idx_personal_email ON users (personal_email);
-- END: migration_register_wizard.sql

-- ======================================================================
-- 4) GENDER & CIVIL STATUS MIGRATION
-- ======================================================================

-- BEGIN: migration_gender_civil_status.sql
USE mmco_accounting_system;

ALTER TABLE users
    ADD COLUMN gender VARCHAR(20) NULL,
    ADD COLUMN civil_status VARCHAR(30) NULL;
-- END: migration_gender_civil_status.sql

-- ======================================================================
-- 5) EMPLOYEE ID MIGRATION
-- ======================================================================

-- BEGIN: migration_employee_id.sql
USE mmco_accounting_system;

ALTER TABLE users
    ADD COLUMN employee_id VARCHAR(20) NULL UNIQUE;
-- END: migration_employee_id.sql

-- ======================================================================
-- 6) ENSURE ADMIN USER (SAFE TO RE-RUN)
-- ======================================================================

-- BEGIN: insert_admin_now.sql
USE mmco_accounting_system;

SET @pw = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

DELETE FROM users WHERE email != 'admin.mmco@gmail.com';

INSERT INTO users (name, email, password, role, department, is_ojt, required_hours, ojt_start_date)
VALUES ('Admin Owner', 'admin.mmco@gmail.com', @pw, 'admin', 'IT', 0, NULL, NULL)
ON DUPLICATE KEY UPDATE password = @pw, name = 'Admin Owner', role = 'admin', department = 'IT', is_ojt = 0, required_hours = NULL, ojt_start_date = NULL;

UPDATE users SET email = 'admin.mmco@gmail.com' WHERE email = 'admin@mmco.com';

SELECT id, name, email, role FROM users;
-- END: insert_admin_now.sql

SET FOREIGN_KEY_CHECKS = 1;

