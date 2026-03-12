-- Migration for Register Wizard fields + dynamic dropdown options
-- Run this AFTER schema.sql (and after existing data is loaded) on database: mmco_accounting_system

USE mmco_accounting_system;

-- 1) Dynamic option tables
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

-- Seed defaults (safe to re-run)
INSERT IGNORE INTO departments (name) VALUES ('HR'), ('IT'), ('Accounting');
INSERT IGNORE INTO ojt_kinds (name) VALUES ('IT OJT'), ('Accounting OJT'), ('HR OJT');

-- 2) Extend users table
-- IMPORTANT: current schema uses department ENUM('IT','Accounting','HR').
-- To support dynamic departments, change it to VARCHAR.
ALTER TABLE users
    MODIFY department VARCHAR(100) NOT NULL;

-- Add new columns needed by Register Wizard
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

-- Optional indexes
CREATE INDEX idx_id_number ON users (id_number);
CREATE INDEX idx_personal_email ON users (personal_email);
