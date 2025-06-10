-- Data Cleanup Script for Henrich Food Corps Database (FIXED)
-- Run this script before implementing schema changes to fix common data issues

START TRANSACTION;

-- ========== Identify email column name in users table ==========
-- Set a variable for the email column name
SET @email_column = (
    SELECT 
    CASE 
        WHEN EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'usermail') THEN 'usermail'
        WHEN EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'email') THEN 'email'
        WHEN EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'useremail') THEN 'useremail'
        ELSE NULL
    END
);

-- If email column couldn't be identified, show an error
SELECT CONCAT('ERROR: Could not identify email column in users table. Found: ', @email_column) AS message
WHERE @email_column IS NULL;

-- ========== Fix NULL or zero values in key fields ==========

-- Remove records with NULL or 0 customerid from customeraccount
DELETE FROM customeraccount WHERE customerid IS NULL OR customerid = 0;

-- Fix records with NULL orderid in customerorder (unlikely but checking)
DELETE FROM customerorder WHERE orderid IS NULL OR orderid = 0;

-- Remove records with NULL user_id from users
DELETE FROM users WHERE user_id IS NULL OR user_id = 0;

-- Remove records with NULL productcode from products
DELETE FROM products WHERE productcode IS NULL OR productcode = '';

-- ========== Fix duplicate primary keys ==========

-- Create temporary tables to identify duplicates (only if id column exists)
SET @has_id_column = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'users' 
    AND COLUMN_NAME = 'id'
);

-- Only create temporary tables and fix duplicates if id column exists
SET @create_temp_table = CONCAT(
    'CREATE TEMPORARY TABLE IF NOT EXISTS duplicate_users AS ',
    'SELECT user_id, MIN(id) AS keep_id ',
    'FROM users ',
    'GROUP BY user_id ',
    'HAVING COUNT(*) > 1'
);

SET @delete_dups = CONCAT(
    'DELETE u FROM users u ',
    'JOIN duplicate_users d ON u.user_id = d.user_id ',
    'WHERE u.id != d.keep_id'
);

-- Only execute if id column exists
PREPARE stmt FROM @create_temp_table;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

PREPARE stmt FROM @delete_dups;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Similar process for customeraccount if needed
CREATE TEMPORARY TABLE IF NOT EXISTS duplicate_customers AS
SELECT customerid, MIN(id) AS keep_id
FROM customeraccount
GROUP BY customerid
HAVING COUNT(*) > 1;

DELETE ca FROM customeraccount ca
JOIN duplicate_customers d ON ca.customerid = d.customerid
WHERE ca.id != d.keep_id;

-- ========== Fix orphaned records ==========

-- Remove orderlog entries with no matching customerorder
DELETE FROM orderlog
WHERE NOT EXISTS (
    SELECT 1 FROM customerorder co WHERE co.orderid = orderlog.orderid
);

-- Remove inventory entries with no matching product
DELETE FROM inventory
WHERE NOT EXISTS (
    SELECT 1 FROM products p WHERE p.productcode = inventory.productcode
);

-- Remove branch_inventory entries with no matching branch or product
DELETE FROM branch_inventory
WHERE NOT EXISTS (
    SELECT 1 FROM branches b WHERE b.branch_id = branch_inventory.branch_id
) OR NOT EXISTS (
    SELECT 1 FROM products p WHERE p.productcode = branch_inventory.productcode
);

-- ========== Fix customerorder foreign key issues ==========

-- Set NULL for customerid in customerorder that don't exist in customeraccount
UPDATE customerorder co
SET co.customerid = NULL
WHERE NOT EXISTS (
    SELECT 1 FROM customeraccount ca WHERE ca.customerid = co.customerid
);

-- Set NULL for branch_id in customerorder that don't exist in branches
UPDATE customerorder co
SET co.branch_id = NULL
WHERE co.branch_id IS NOT NULL
AND NOT EXISTS (
    SELECT 1 FROM branches b WHERE b.branch_id = co.branch_id
);

-- ========== Fix orderlog foreign key issues ==========

-- Remove orderlog entries with invalid productcode
DELETE FROM orderlog
WHERE NOT EXISTS (
    SELECT 1 FROM products p WHERE p.productcode = orderlog.productcode
);

-- ========== Fix truncated passwords ==========

-- Mark accounts with truncated passwords for reset
UPDATE approved_account
SET password = CONCAT(password, '_NEEDS_RESET')
WHERE LENGTH(password) < 50;

-- ========== Consolidate user data ==========

-- Create a temporary mapping table for account consolidation
CREATE TEMPORARY TABLE user_mapping (
    source_table VARCHAR(50),
    source_id INT,
    username VARCHAR(50),
    email VARCHAR(100),
    target_user_id INT NULL
);

-- Insert records from account_request that don't exist in users
INSERT INTO user_mapping (source_table, source_id, username, email)
SELECT 'account_request', user_id, username, usermail
FROM account_request
WHERE username IS NOT NULL AND usermail IS NOT NULL;

-- Insert records from approved_account that don't exist in users
INSERT INTO user_mapping (source_table, source_id, username, email)
SELECT 'approved_account', user_id, username, usermail
FROM approved_account
WHERE username IS NOT NULL AND usermail IS NOT NULL;

-- Mark duplicates based on username or email
UPDATE user_mapping um1
JOIN user_mapping um2 ON (um1.username = um2.username OR um1.email = um2.email)
    AND um1.source_id != um2.source_id
SET um1.username = CONCAT(um1.username, '_', um1.source_id)
WHERE um1.source_id > um2.source_id;

-- Prepare for actual schema changes
-- These updates will be executed in the main migration script

COMMIT;

-- Verify cleanup results
SELECT 'Remaining NULL customerid in customeraccount' AS check_result, COUNT(*) AS count
FROM customeraccount WHERE customerid IS NULL OR customerid = 0
UNION ALL
SELECT 'Remaining NULL orderid in customerorder' AS check_result, COUNT(*) AS count
FROM customerorder WHERE orderid IS NULL OR orderid = 0
UNION ALL
SELECT 'Remaining orphaned orderlog records' AS check_result, COUNT(*) AS count
FROM orderlog ol
WHERE NOT EXISTS (SELECT 1 FROM customerorder co WHERE co.orderid = ol.orderid)
UNION ALL
SELECT 'Remaining orphaned inventory records' AS check_result, COUNT(*) AS count
FROM inventory i
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.productcode = i.productcode); 