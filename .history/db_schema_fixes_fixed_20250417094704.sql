-- Database Schema Improvement Script for Henrich Food Corps (FIXED)
-- This script addresses the identified issues with table relationships and structure

-- ========== IDENTIFY EMAIL COLUMN NAME ==========
-- First determine the column name used for email in the users table
SET @email_column = (
    SELECT 
    CASE 
        WHEN EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'usermail') THEN 'usermail'
        WHEN EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'email') THEN 'email'
        WHEN EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'useremail') THEN 'useremail'
        ELSE NULL
    END
);

-- Display the found email column name
SELECT CONCAT('Using email column: ', @email_column) AS message;

-- ========== BACKUP STATEMENTS ==========
-- Before making changes, create backup tables
SET @backup_timestamp = CONCAT('backup_', DATE_FORMAT(NOW(), '%Y%m%d%H%i%s'));

-- Backup user-related tables
SET @users_backup = CONCAT('users_', @backup_timestamp);
SET @account_request_backup = CONCAT('account_request_', @backup_timestamp);
SET @account_requests_backup = CONCAT('account_requests_', @backup_timestamp);
SET @approved_account_backup = CONCAT('approved_account_', @backup_timestamp);
SET @approvedaccount_history_backup = CONCAT('approvedaccount_history_', @backup_timestamp);

SET @sql = CONCAT('CREATE TABLE ', @users_backup, ' SELECT * FROM users');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = CONCAT('CREATE TABLE ', @account_request_backup, ' SELECT * FROM account_request');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = CONCAT('CREATE TABLE ', @account_requests_backup, ' SELECT * FROM account_requests');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = CONCAT('CREATE TABLE ', @approved_account_backup, ' SELECT * FROM approved_account');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = CONCAT('CREATE TABLE ', @approvedaccount_history_backup, ' SELECT * FROM approvedaccount_history');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Backup order-related tables
SET @customerorder_backup = CONCAT('customerorder_', @backup_timestamp);
SET @orderlog_backup = CONCAT('orderlog_', @backup_timestamp);

SET @sql = CONCAT('CREATE TABLE ', @customerorder_backup, ' SELECT * FROM customerorder');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = CONCAT('CREATE TABLE ', @orderlog_backup, ' SELECT * FROM orderlog');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ========== FIX USER TABLES ==========
-- 1. Fix the users table structure
-- Create dynamic SQL based on which email column is found
SET @alter_users_sql = CONCAT(
    'ALTER TABLE `users` ',
    'MODIFY COLUMN `user_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, ',
    'MODIFY COLUMN `username` VARCHAR(50) NOT NULL, ',
    'MODIFY COLUMN `password` VARCHAR(255) NOT NULL, ',
    'ADD UNIQUE INDEX `idx_username` (`username`), '
);

-- Add appropriate unique index for the email column
IF @email_column IS NOT NULL THEN
    SET @alter_users_sql = CONCAT(@alter_users_sql, 'ADD UNIQUE INDEX `idx_email` (`', @email_column, '`), ');
END IF;

-- Complete the ALTER TABLE statement
SET @alter_users_sql = CONCAT(@alter_users_sql,
    'MODIFY COLUMN `status` ENUM(''active'', ''inactive'', ''pending'') DEFAULT ''active'', ',
    'MODIFY COLUMN `role` ENUM(''admin'', ''supervisor'', ''ceo'', ''employee'', ''customer'') NOT NULL'
);

-- Execute the dynamic SQL
PREPARE stmt FROM @alter_users_sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 2. Create a consolidated user_history table
CREATE TABLE IF NOT EXISTS `user_history` (
  `history_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `action` VARCHAR(50) NOT NULL,
  `previous_status` VARCHAR(50) NULL,
  `new_status` VARCHAR(50) NULL,
  `modified_by` INT NULL,
  `timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`modified_by`) REFERENCES `users`(`user_id`) ON DELETE SET NULL
);

-- 3. Migrate data from account_request to users if they don't already exist
-- Dynamic SQL to handle different email column names
SET @migrate_ar_sql = CONCAT(
    'INSERT INTO users (username, ', @email_column, ', role, password, first_name, last_name, status, created_at, updated_at) ',
    'SELECT ar.username, ar.usermail, ar.role, ar.password, ar.first_name, ar.last_name, ''pending'', ar.created_at, ar.updated_at ',
    'FROM account_request ar ',
    'WHERE NOT EXISTS ( ',
    '    SELECT 1 FROM users u WHERE u.username = ar.username OR u.', @email_column, ' = ar.usermail ',
    ') ',
    'AND ar.username IS NOT NULL AND ar.usermail IS NOT NULL'
);

-- Execute the dynamic SQL if the email column was found
IF @email_column IS NOT NULL THEN
    PREPARE stmt FROM @migrate_ar_sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
ELSE
    SELECT 'Warning: Skipping account_request migration because email column could not be identified' AS message;
END IF;

-- 4. Migrate data from approved_account to users if they don't already exist
-- Dynamic SQL to handle different email column names
SET @migrate_aa_sql = CONCAT(
    'INSERT INTO users (username, ', @email_column, ', first_name, last_name, status, created_at, updated_at) ',
    'SELECT aa.username, aa.usermail, aa.first_name, aa.last_name, ''active'', aa.created_at, aa.updated_at ',
    'FROM approved_account aa ',
    'WHERE NOT EXISTS ( ',
    '    SELECT 1 FROM users u WHERE u.username = aa.username OR u.', @email_column, ' = aa.usermail ',
    ') ',
    'AND aa.username IS NOT NULL AND aa.usermail IS NOT NULL'
);

-- Execute the dynamic SQL if the email column was found
IF @email_column IS NOT NULL THEN
    PREPARE stmt FROM @migrate_aa_sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
ELSE
    SELECT 'Warning: Skipping approved_account migration because email column could not be identified' AS message;
END IF;

-- 5. Add account history entries for tracking
INSERT INTO user_history (user_id, action, new_status, timestamp)
SELECT user_id, 'Account Approved', 'active', created_at
FROM approvedaccount_history;

-- ========== FIX CUSTOMER TABLES ==========
-- 1. Ensure proper primary key in customeraccount table
ALTER TABLE `customeraccount` 
MODIFY COLUMN `customerid` INT NOT NULL AUTO_INCREMENT PRIMARY KEY;

-- 2. Link customerdetails to customeraccount
ALTER TABLE `customerdetails` 
ADD CONSTRAINT `fk_customerdetails_account`
FOREIGN KEY (`customerid`) REFERENCES `customeraccount`(`customerid`)
ON DELETE CASCADE;

-- ========== FIX ORDER TABLES ==========
-- 1. Ensure proper primary key in customerorder table
ALTER TABLE `customerorder` 
MODIFY COLUMN `orderid` INT NOT NULL AUTO_INCREMENT PRIMARY KEY;

-- 2. Add missing foreign key relationships
ALTER TABLE `customerorder` 
ADD CONSTRAINT `fk_customerorder_customer`
FOREIGN KEY (`customerid`) REFERENCES `customeraccount`(`customerid`)
ON DELETE SET NULL;

ALTER TABLE `customerorder` 
ADD CONSTRAINT `fk_customerorder_branch`
FOREIGN KEY (`branch_id`) REFERENCES `branches`(`branch_id`)
ON DELETE SET NULL;

ALTER TABLE `orderlog` 
ADD CONSTRAINT `fk_orderlog_order`
FOREIGN KEY (`orderid`) REFERENCES `customerorder`(`orderid`)
ON DELETE CASCADE;

ALTER TABLE `orderlog` 
ADD CONSTRAINT `fk_orderlog_product`
FOREIGN KEY (`productcode`) REFERENCES `products`(`productcode`)
ON DELETE RESTRICT;

-- ========== FIX INVENTORY TABLES ==========
-- 1. Ensure proper foreign key relationships
ALTER TABLE `inventory` 
ADD CONSTRAINT `fk_inventory_product`
FOREIGN KEY (`productcode`) REFERENCES `products`(`productcode`)
ON DELETE CASCADE;

ALTER TABLE `stockmovement` 
ADD CONSTRAINT `fk_stockmovement_product`
FOREIGN KEY (`productcode`) REFERENCES `products`(`productcode`)
ON DELETE RESTRICT;

ALTER TABLE `stockmovement` 
ADD CONSTRAINT `fk_stockmovement_user`
FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`)
ON DELETE SET NULL;

-- For branch inventory
ALTER TABLE `branch_inventory` 
ADD CONSTRAINT `fk_branch_inventory_branch`
FOREIGN KEY (`branch_id`) REFERENCES `branches`(`branch_id`)
ON DELETE CASCADE,
ADD CONSTRAINT `fk_branch_inventory_product`
FOREIGN KEY (`productcode`) REFERENCES `products`(`productcode`)
ON DELETE CASCADE;

-- ========== CLEANUP REDUNDANT TABLES ==========
-- These should only be done after verifying that the data has been properly migrated

-- Don't drop tables immediately - first rename them to avoid breaking existing code
-- ALTER TABLE `account_request` RENAME TO `deprecated_account_request`;
-- ALTER TABLE `account_requests` RENAME TO `deprecated_account_requests`;
-- ALTER TABLE `approved_account` RENAME TO `deprecated_approved_account`;
-- ALTER TABLE `approvedaccount_history` RENAME TO `deprecated_approvedaccount_history`;

-- ========== ADD INDEXES FOR PERFORMANCE ==========
CREATE INDEX `idx_customer_order_date` ON `customerorder` (`orderdate`);
CREATE INDEX `idx_order_log_date` ON `orderlog` (`orderdate`);
CREATE INDEX `idx_inventory_stock` ON `inventory` (`availablequantity`);
CREATE INDEX `idx_product_category` ON `products` (`category`);
CREATE INDEX `idx_user_role` ON `users` (`role`);
CREATE INDEX `idx_user_status` ON `users` (`status`);

-- ========== CREATE NEW VIEWS FOR REPORTING ==========
-- Sales by period view
CREATE OR REPLACE VIEW `vw_sales_by_period` AS
SELECT 
    DATE_FORMAT(co.orderdate, '%Y-%m') AS period,
    SUM(ol.quantity * ol.unit_price) AS total_sales,
    COUNT(DISTINCT co.orderid) AS order_count,
    SUM(ol.quantity) AS total_quantity
FROM 
    customerorder co
JOIN 
    orderlog ol ON co.orderid = ol.orderid
WHERE 
    co.status = 'Completed'
GROUP BY 
    DATE_FORMAT(co.orderdate, '%Y-%m')
ORDER BY 
    period DESC;

-- Product performance view
CREATE OR REPLACE VIEW `vw_product_performance` AS
SELECT 
    p.productcode,
    p.productname,
    p.category,
    SUM(ol.quantity) AS total_sold,
    SUM(ol.quantity * ol.unit_price) AS total_revenue,
    COUNT(DISTINCT ol.orderid) AS order_count,
    i.availablequantity AS current_stock
FROM 
    products p
LEFT JOIN 
    orderlog ol ON p.productcode = ol.productcode
LEFT JOIN 
    inventory i ON p.productcode = i.productcode
GROUP BY 
    p.productcode, p.productname, p.category, i.availablequantity
ORDER BY 
    total_sold DESC; 