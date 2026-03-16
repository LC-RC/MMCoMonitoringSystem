-- Migration: Add gender and civil_status (run if you already have birthday/address from migration_birthday_address.sql)
-- Run on database: mmco_accounting_system

USE mmco_accounting_system;

ALTER TABLE users
    ADD COLUMN gender VARCHAR(20) NULL,
    ADD COLUMN civil_status VARCHAR(30) NULL;
