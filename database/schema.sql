-- MM&Co Accounting Review Center Management System
-- Database Schema - MySQL

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS mmco_accounting_system
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
USE mmco_accounting_system;

-- ----------------------------------------
-- Users Table
-- ----------------------------------------
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

-- ----------------------------------------
-- Attendance Table
-- ----------------------------------------
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

-- ----------------------------------------
-- Projects Table
-- ----------------------------------------
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

-- ----------------------------------------
-- Tasks Table (Project Work Phases)
-- ----------------------------------------
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

-- ----------------------------------------
-- Project Assignments (Employees + OJTs)
-- ----------------------------------------
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

-- ----------------------------------------
-- Task Assignments
-- ----------------------------------------
CREATE TABLE task_assignments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    task_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_task_user (task_id, user_id)
) ENGINE=InnoDB;

-- ----------------------------------------
-- Payroll Records
-- ----------------------------------------
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

-- ----------------------------------------
-- Inventory Items
-- ----------------------------------------
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

SET FOREIGN_KEY_CHECKS = 1;
