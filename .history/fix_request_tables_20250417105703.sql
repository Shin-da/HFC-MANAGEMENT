-- SQL Script to Fix Structure of account_request and approved_account

-- IMPORTANT: Backup your database before running this script!

USE dbhenrichfoodcorps;

-- Fixes for account_request table

-- 1. Add an auto-incrementing primary key
ALTER TABLE `account_request` 
ADD COLUMN `id` INT AUTO_INCREMENT PRIMARY KEY FIRST;

-- 2. Modify password column to store hashes correctly
ALTER TABLE `account_request` 
MODIFY COLUMN `password` VARCHAR(255) NOT NULL;

SELECT 'account_request table structure updated.' AS message;

-- Fixes for approved_account table

-- 1. Add an auto-incrementing primary key
ALTER TABLE `approved_account` 
ADD COLUMN `id` INT AUTO_INCREMENT PRIMARY KEY FIRST;

-- 2. Modify password column to store hashes correctly
ALTER TABLE `approved_account` 
MODIFY COLUMN `password` VARCHAR(255) NOT NULL;

-- 3. Correct the enum values for the role column
-- Note: Ensure existing data doesn't violate the new enum. 
-- If you have 'active' or 'inactive' in the role column currently, 
-- you might need to update them manually first or adjust this command.
ALTER TABLE `approved_account` 
MODIFY COLUMN `role` ENUM('admin', 'supervisor', 'ceo') NOT NULL;

SELECT 'approved_account table structure updated.' AS message;

SELECT 'Structural fixes applied. Review tables in phpMyAdmin.' AS summary; 