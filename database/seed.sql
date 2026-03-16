-- MM&Co Accounting Review Center - Mock Data
-- Run after schema.sql

USE mmco_accounting_system;

-- Password for admin: password123 (bcrypt)
SET @pw = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

-- ----------------------------------------
-- Admin only (admin.mmco@gmail.com / password123)
-- ----------------------------------------
INSERT INTO users (name, email, password, role, department, is_ojt, required_hours, ojt_start_date) VALUES
('Admin Owner', 'admin.mmco@gmail.com', @pw, 'admin', 'IT', 0, NULL, NULL);

-- ----------------------------------------
-- Sample Projects
-- ----------------------------------------
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

-- ----------------------------------------
-- Sample Inventory
-- ----------------------------------------
INSERT INTO inventory_items (name, category, quantity, low_stock_threshold, unit, notes) VALUES
('Laptop', 'Equipment', 15, 5, 'pcs', 'Development laptops'),
('Monitor', 'Equipment', 20, 8, 'pcs', '24-inch monitors'),
('Paper (Ream)', 'Office Supplies', 3, 10, 'ream', 'A4 bond paper'),
('Pens', 'Office Supplies', 50, 20, 'pcs', 'Ballpoint pens'),
('Printer Ink', 'Office Supplies', 2, 5, 'cartridge', 'HP 67 black');
