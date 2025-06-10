-- SQL Script to Fix the admin_logs Table

-- IMPORTANT: Backup your database before running this script!

USE dbhenrichfoodcorps;

-- Step 1: Find the name of the incorrect foreign key constraint
-- Run this query first in phpMyAdmin and note the CONSTRAINT_NAME
SELECT CONSTRAINT_NAME 
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'dbhenrichfoodcorps' 
  AND TABLE_NAME = 'admin_logs' 
  AND REFERENCED_TABLE_NAME = 'admin'; -- Or check if it points to NULL or another wrong table

-- Step 2: Drop the incorrect foreign key constraint
-- Replace 'constraint_name_here' with the actual name found above
ALTER TABLE admin_logs DROP FOREIGN KEY constraint_name_here;

-- Step 3: Rename the column from user_id to admin_id
-- Assuming the column is currently named user_id based on earlier checks
ALTER TABLE admin_logs CHANGE COLUMN user_id admin_id INT NULL;

-- Step 4: Add the correct foreign key constraint referencing the users table
ALTER TABLE admin_logs 
ADD CONSTRAINT fk_admin_logs_users 
FOREIGN KEY (admin_id) REFERENCES users(user_id)
ON DELETE SET NULL -- Or ON DELETE CASCADE, depending on desired behavior
ON UPDATE CASCADE;

-- Step 5: Add an index to the new foreign key column for performance
ALTER TABLE admin_logs ADD INDEX idx_admin_id (admin_id);

SELECT 'admin_logs table structure potentially fixed. Please verify in phpMyAdmin.' AS message; 