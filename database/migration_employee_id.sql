-- Migration: Add employee_id for automatic ID generation (format TDYY##)
-- Run on database: mmco_accounting_system

USE mmco_accounting_system;

-- Add employee_id column (unique, nullable for existing rows)
-- If column already exists, skip or run: ALTER TABLE users ADD COLUMN employee_id VARCHAR(20) NULL UNIQUE AFTER id_number;
ALTER TABLE users
    ADD COLUMN employee_id VARCHAR(20) NULL UNIQUE;

-- Optional: backfill existing id_number into employee_id where pattern matches
-- UPDATE users SET employee_id = id_number WHERE id_number REGEXP '^[EO][IAH]?[0-9]{4}[0-9]{2}$' AND (employee_id IS NULL OR employee_id = '');
