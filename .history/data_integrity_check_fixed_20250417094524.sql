-- Data Integrity Check Script for Henrich Food Corps Database (FIXED)
-- Run this script before implementing schema changes to identify potential issues

-- ========== Check for NULL values in required fields ==========
SELECT 'customeraccount table: NULL values in customerid' AS issue, COUNT(*) AS count
FROM customeraccount WHERE customerid IS NULL OR customerid = 0
UNION ALL
SELECT 'customerorder table: NULL values in orderid' AS issue, COUNT(*) AS count
FROM customerorder WHERE orderid IS NULL OR orderid = 0
UNION ALL
SELECT 'users table: NULL values in user_id' AS issue, COUNT(*) AS count
FROM users WHERE user_id IS NULL OR user_id = 0
UNION ALL
SELECT 'products table: NULL values in productcode' AS issue, COUNT(*) AS count
FROM products WHERE productcode IS NULL OR productcode = '';

-- ========== Check for duplicate primary keys ==========
SELECT 'users table: Duplicate user_id values' AS issue, user_id, COUNT(*) AS count
FROM users GROUP BY user_id HAVING COUNT(*) > 1
UNION ALL
SELECT 'customeraccount table: Duplicate customerid values' AS issue, customerid, COUNT(*) AS count
FROM customeraccount GROUP BY customerid HAVING COUNT(*) > 1
UNION ALL
SELECT 'customerorder table: Duplicate orderid values' AS issue, orderid, COUNT(*) AS count
FROM customerorder GROUP BY orderid HAVING COUNT(*) > 1;

-- ========== Check for orphaned records ==========
SELECT 'orderlog table: orphaned records (no matching customerorder)' AS issue, COUNT(*) AS count
FROM orderlog ol
WHERE NOT EXISTS (SELECT 1 FROM customerorder co WHERE co.orderid = ol.orderid);

SELECT 'inventory table: orphaned records (no matching product)' AS issue, COUNT(*) AS count
FROM inventory i
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.productcode = i.productcode);

SELECT 'branch_inventory table: orphaned records (no matching branch)' AS issue, COUNT(*) AS count
FROM branch_inventory bi
WHERE NOT EXISTS (SELECT 1 FROM branches b WHERE b.branch_id = bi.branch_id);

SELECT 'branch_inventory table: orphaned records (no matching product)' AS issue, COUNT(*) AS count
FROM branch_inventory bi
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.productcode = bi.productcode);

-- ========== Check the email column name in users table ==========
-- First, let's check if the users table has a column named usermail, email, or useremail
SELECT COLUMN_NAME 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'dbhenrichfoodcorps' 
AND TABLE_NAME = 'users' 
AND COLUMN_NAME IN ('usermail', 'email', 'useremail');

-- ========== Check for inconsistent data between user tables (WITH COLUMN CHECK) ==========
-- This section dynamically checks for user email inconsistencies based on which email column exists
SET @emailColumnQuery = '
SELECT CASE 
    WHEN EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = "users" AND COLUMN_NAME = "usermail") THEN "usermail"
    WHEN EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = "users" AND COLUMN_NAME = "email") THEN "email"
    WHEN EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = "users" AND COLUMN_NAME = "useremail") THEN "useremail"
    ELSE "unknown"
END AS email_column_name';

-- Let's check for inconsistent data based on which email column exists
SELECT 'Inconsistent user accounts across tables' AS issue, 
       aa.username, aa.usermail, 
       u.username AS users_username, 
       -- Only try to access email columns that exist
       CASE 
         WHEN (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'usermail') > 0 THEN u.usermail
         WHEN (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'email') > 0 THEN u.email
         WHEN (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'useremail') > 0 THEN u.useremail
         ELSE 'email_column_not_found'
       END AS users_email
FROM approved_account aa
LEFT JOIN users u ON aa.username = u.username 
WHERE u.user_id IS NULL;

-- ========== Check for duplicate usernames or emails across tables ==========
-- This query avoids using specific email column names in the users table
(SELECT username, 'account_request' AS source_table FROM account_request)
UNION ALL
(SELECT username, 'approved_account' AS source_table FROM approved_account)
UNION ALL
(SELECT username, 'users' AS source_table FROM users)
GROUP BY username
HAVING COUNT(*) > 1;

-- ========== Check for data that would violate foreign key constraints ==========
SELECT 'customerorder table: customerid values not in customeraccount' AS issue, COUNT(*) AS count
FROM customerorder co
WHERE NOT EXISTS (SELECT 1 FROM customeraccount ca WHERE ca.customerid = co.customerid);

SELECT 'customerorder table: branch_id values not in branches' AS issue, COUNT(*) AS count
FROM customerorder co
WHERE co.branch_id IS NOT NULL 
  AND NOT EXISTS (SELECT 1 FROM branches b WHERE b.branch_id = co.branch_id);

SELECT 'orderlog table: productcode values not in products' AS issue, COUNT(*) AS count
FROM orderlog ol
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.productcode = ol.productcode);

-- ========== Check for truncated data ==========
SELECT 'approved_account table: truncated passwords' AS issue, COUNT(*) AS count
FROM approved_account
WHERE LENGTH(password) < 50;

-- ========== Recommend data cleanup steps based on issues found ==========
SELECT 'To fix: Remove orphaned orderlog records' AS recommendation
WHERE EXISTS (
    SELECT 1 FROM orderlog ol
    WHERE NOT EXISTS (SELECT 1 FROM customerorder co WHERE co.orderid = ol.orderid)
)
UNION ALL
SELECT 'To fix: Update duplicate user_id values in users table' AS recommendation
WHERE EXISTS (
    SELECT 1 FROM users GROUP BY user_id HAVING COUNT(*) > 1
); 