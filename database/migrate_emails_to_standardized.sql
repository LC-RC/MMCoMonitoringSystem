-- Migration: Update admin email to standardized format (admin.mmco@gmail.com)
-- Run this if you have the old admin email (admin@mmco.com).

USE mmco_accounting_system;

UPDATE users SET email = 'admin.mmco@gmail.com' WHERE email = 'admin@mmco.com';
