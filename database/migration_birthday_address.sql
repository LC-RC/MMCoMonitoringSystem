-- Migration: Add birthday, address, gender, civil_status for registration
-- Run on database: mmco_accounting_system

USE mmco_accounting_system;

ALTER TABLE users
    ADD COLUMN birthday DATE NULL AFTER contact_number,
    ADD COLUMN address VARCHAR(500) NULL AFTER birthday,
    ADD COLUMN gender VARCHAR(20) NULL AFTER address,
    ADD COLUMN civil_status VARCHAR(30) NULL AFTER gender;
