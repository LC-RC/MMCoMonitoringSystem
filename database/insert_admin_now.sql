-- Run this in MySQL Workbench or phpMyAdmin.
-- IMPORTANT: In MySQL Workbench, double-click "mmco_accounting_system" in the Schemas list
-- so it becomes the default (bold), then execute this entire script.

USE mmco_accounting_system;

-- Password for admin: password123 (bcrypt hash)
SET @pw = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

-- Remove all users except admin
DELETE FROM users WHERE email != 'admin.mmco@gmail.com';

-- Ensure admin exists: insert or update so admin.mmco@gmail.com can log in
INSERT INTO users (name, email, password, role, department, is_ojt, required_hours, ojt_start_date)
VALUES ('Admin Owner', 'admin.mmco@gmail.com', @pw, 'admin', 'IT', 0, NULL, NULL)
ON DUPLICATE KEY UPDATE password = @pw, name = 'Admin Owner', role = 'admin', department = 'IT', is_ojt = 0, required_hours = NULL, ojt_start_date = NULL;

-- Update old admin email if it exists
UPDATE users SET email = 'admin.mmco@gmail.com' WHERE email = 'admin@mmco.com';

-- Verify: only admin should exist
SELECT id, name, email, role FROM users;
