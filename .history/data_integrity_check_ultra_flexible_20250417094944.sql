-- Data Integrity Check Script for Henrich Food Corps Database (ULTRA FLEXIBLE)
-- Run this script before implementing schema changes to identify potential issues

-- ========== First check which tables exist in the database ==========
SELECT 'Tables found in database:' AS message;

-- Create a temporary table to store which tables exist
CREATE TEMPORARY TABLE IF NOT EXISTS existing_tables (
    table_name VARCHAR(100) PRIMARY KEY
);

-- Insert all relevant tables that exist in the database
INSERT IGNORE INTO existing_tables (table_name)
SELECT table_name 
FROM information_schema.tables 
WHERE table_schema = DATABASE() 
AND table_name IN (
    'users', 'account_request', 'account_requests', 'approved_account', 
    'approvedaccount_history', 'customeraccount', 'customerdetails',
    'customerorder', 'orderlog', 'products', 'inventory', 
    'branches', 'branch_inventory', 'stockmovement'
);

-- Display which tables were found
SELECT table_name FROM existing_tables;

-- ========== Check for NULL values in required fields (only if tables exist) ==========
SELECT 'Checking for NULL values in required fields...' AS message;

-- Check customeraccount table if it exists
SET @check_customeraccount = (
    SELECT COUNT(*) FROM existing_tables WHERE table_name = 'customeraccount'
);

SET @query = 'SELECT ''customeraccount table: NULL values in customerid'' AS issue, COUNT(*) AS count FROM customeraccount WHERE customerid IS NULL OR customerid = 0';
SET @skip_message = 'SELECT ''Skipping check: customeraccount table does not exist'' AS issue, 0 AS count';

SET @sql = IF(@check_customeraccount > 0, @query, @skip_message);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check customerorder table if it exists
SET @check_customerorder = (
    SELECT COUNT(*) FROM existing_tables WHERE table_name = 'customerorder'
);

SET @query = 'SELECT ''customerorder table: NULL values in orderid'' AS issue, COUNT(*) AS count FROM customerorder WHERE orderid IS NULL OR orderid = 0';
SET @skip_message = 'SELECT ''Skipping check: customerorder table does not exist'' AS issue, 0 AS count';

SET @sql = IF(@check_customerorder > 0, @query, @skip_message);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check users table if it exists
SET @check_users = (
    SELECT COUNT(*) FROM existing_tables WHERE table_name = 'users'
);

SET @query = 'SELECT ''users table: NULL values in user_id'' AS issue, COUNT(*) AS count FROM users WHERE user_id IS NULL OR user_id = 0';
SET @skip_message = 'SELECT ''Skipping check: users table does not exist'' AS issue, 0 AS count';

SET @sql = IF(@check_users > 0, @query, @skip_message);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check products table if it exists
SET @check_products = (
    SELECT COUNT(*) FROM existing_tables WHERE table_name = 'products'
);

SET @query = 'SELECT ''products table: NULL values in productcode'' AS issue, COUNT(*) AS count FROM products WHERE productcode IS NULL OR productcode = ''''';
SET @skip_message = 'SELECT ''Skipping check: products table does not exist'' AS issue, 0 AS count';

SET @sql = IF(@check_products > 0, @query, @skip_message);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ========== Check for duplicate primary keys (only if tables exist) ==========
SELECT 'Checking for duplicate primary keys...' AS message;

-- Check users table if it exists
SET @check_users = (
    SELECT COUNT(*) FROM existing_tables WHERE table_name = 'users'
);

SET @query = 'SELECT ''users table: Duplicate user_id values'' AS issue, user_id, COUNT(*) AS count FROM users GROUP BY user_id HAVING COUNT(*) > 1';
SET @skip_message = 'SELECT ''Skipping check: users table does not exist'' AS issue, NULL AS user_id, 0 AS count';

SET @sql = IF(@check_users > 0, @query, @skip_message);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check customeraccount table if it exists
SET @check_customeraccount = (
    SELECT COUNT(*) FROM existing_tables WHERE table_name = 'customeraccount'
);

SET @query = 'SELECT ''customeraccount table: Duplicate customerid values'' AS issue, customerid, COUNT(*) AS count FROM customeraccount GROUP BY customerid HAVING COUNT(*) > 1';
SET @skip_message = 'SELECT ''Skipping check: customeraccount table does not exist'' AS issue, NULL AS customerid, 0 AS count';

SET @sql = IF(@check_customeraccount > 0, @query, @skip_message);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check customerorder table if it exists
SET @check_customerorder = (
    SELECT COUNT(*) FROM existing_tables WHERE table_name = 'customerorder'
);

SET @query = 'SELECT ''customerorder table: Duplicate orderid values'' AS issue, orderid, COUNT(*) AS count FROM customerorder GROUP BY orderid HAVING COUNT(*) > 1';
SET @skip_message = 'SELECT ''Skipping check: customerorder table does not exist'' AS issue, NULL AS orderid, 0 AS count';

SET @sql = IF(@check_customerorder > 0, @query, @skip_message);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ========== Check for orphaned records (only if related tables exist) ==========
SELECT 'Checking for orphaned records...' AS message;

-- Check orderlog and customerorder if both exist
SET @check_orderlog_customerorder = (
    SELECT COUNT(*) FROM existing_tables 
    WHERE table_name IN ('orderlog', 'customerorder') 
    GROUP BY table_name HAVING COUNT(*) = 2
);

SET @check_orderlog_customerorder = IFNULL(@check_orderlog_customerorder, 0);

SET @query = 'SELECT ''orderlog table: orphaned records (no matching customerorder)'' AS issue, COUNT(*) AS count FROM orderlog ol WHERE NOT EXISTS (SELECT 1 FROM customerorder co WHERE co.orderid = ol.orderid)';
SET @skip_message = 'SELECT ''Skipping check: orderlog or customerorder table does not exist'' AS issue, 0 AS count';

SET @sql = IF(@check_orderlog_customerorder > 0, @query, @skip_message);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check inventory and products if both exist
SET @check_inventory_products = (
    SELECT COUNT(*) FROM existing_tables 
    WHERE table_name IN ('inventory', 'products') 
    GROUP BY table_name HAVING COUNT(*) = 2
);

SET @check_inventory_products = IFNULL(@check_inventory_products, 0);

SET @query = 'SELECT ''inventory table: orphaned records (no matching product)'' AS issue, COUNT(*) AS count FROM inventory i WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.productcode = i.productcode)';
SET @skip_message = 'SELECT ''Skipping check: inventory or products table does not exist'' AS issue, 0 AS count';

SET @sql = IF(@check_inventory_products > 0, @query, @skip_message);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check branch_inventory and branches if both exist
SET @check_branch_inventory_branches = (
    SELECT COUNT(*) FROM existing_tables 
    WHERE table_name IN ('branch_inventory', 'branches') 
    GROUP BY table_name HAVING COUNT(*) = 2
);

SET @check_branch_inventory_branches = IFNULL(@check_branch_inventory_branches, 0);

SET @query = 'SELECT ''branch_inventory table: orphaned records (no matching branch)'' AS issue, COUNT(*) AS count FROM branch_inventory bi WHERE NOT EXISTS (SELECT 1 FROM branches b WHERE b.branch_id = bi.branch_id)';
SET @skip_message = 'SELECT ''Skipping check: branch_inventory or branches table does not exist'' AS issue, 0 AS count';

SET @sql = IF(@check_branch_inventory_branches > 0, @query, @skip_message);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check branch_inventory and products if both exist
SET @check_branch_inventory_products = (
    SELECT COUNT(*) FROM existing_tables 
    WHERE table_name IN ('branch_inventory', 'products') 
    GROUP BY table_name HAVING COUNT(*) = 2
);

SET @check_branch_inventory_products = IFNULL(@check_branch_inventory_products, 0);

SET @query = 'SELECT ''branch_inventory table: orphaned records (no matching product)'' AS issue, COUNT(*) AS count FROM branch_inventory bi WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.productcode = bi.productcode)';
SET @skip_message = 'SELECT ''Skipping check: branch_inventory or products table does not exist'' AS issue, 0 AS count';

SET @sql = IF(@check_branch_inventory_products > 0, @query, @skip_message);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ========== Check the email column name in users table (if it exists) ==========
SELECT 'Checking email column in users table...' AS message;

SET @check_users = (
    SELECT COUNT(*) FROM existing_tables WHERE table_name = 'users'
);

SET @query = 'SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ''users'' AND COLUMN_NAME IN (''usermail'', ''email'', ''useremail'')';
SET @skip_message = 'SELECT ''Skipping check: users table does not exist'' AS COLUMN_NAME';

SET @sql = IF(@check_users > 0, @query, @skip_message);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ========== Check for inconsistent data between user tables (if they exist) ==========
SELECT 'Checking for inconsistent data between user tables...' AS message;

-- Check if approved_account and users tables exist
SET @check_approved_account_users = (
    SELECT COUNT(*) FROM existing_tables 
    WHERE table_name IN ('approved_account', 'users') 
    GROUP BY table_name HAVING COUNT(*) = 2
);

SET @check_approved_account_users = IFNULL(@check_approved_account_users, 0);

-- Only run this check if both tables exist
IF @check_approved_account_users > 0 THEN
    -- Get email column name from users table
    SET @email_column = (
        SELECT 
        CASE 
            WHEN EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'usermail') THEN 'usermail'
            WHEN EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'email') THEN 'email'
            WHEN EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'useremail') THEN 'useremail'
            ELSE NULL
        END
    );
    
    IF @email_column IS NOT NULL THEN
        SET @query = CONCAT(
            'SELECT ''Inconsistent user accounts across tables'' AS issue, ',
            'aa.username, aa.usermail, ',
            'u.username AS users_username, ',
            'u.', @email_column, ' AS users_email ',
            'FROM approved_account aa ',
            'LEFT JOIN users u ON aa.username = u.username ',
            'WHERE u.user_id IS NULL'
        );
        
        PREPARE stmt FROM @query;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    ELSE
        SELECT 'Could not identify email column in users table' AS message;
    END IF;
ELSE
    SELECT 'Skipping check: approved_account or users table does not exist' AS message;
END IF;

-- ========== Check for duplicate usernames across tables (if they exist) ==========
SELECT 'Checking for duplicate usernames across tables...' AS message;

-- Create a temporary view to hold all usernames from all tables
SET @create_all_users_view = '
CREATE OR REPLACE VIEW all_users_view AS 
';

-- Check if account_request table exists
SET @account_request_exists = (
    SELECT COUNT(*) FROM existing_tables WHERE table_name = 'account_request'
);

IF @account_request_exists > 0 THEN
    SET @create_all_users_view = CONCAT(@create_all_users_view, 
        'SELECT username, ''account_request'' AS source_table FROM account_request WHERE username IS NOT NULL '
    );
ELSE 
    SET @create_all_users_view = CONCAT(@create_all_users_view, 
        'SELECT NULL AS username, ''account_request'' AS source_table FROM dual WHERE FALSE '
    );
END IF;

-- Check if approved_account table exists
SET @approved_account_exists = (
    SELECT COUNT(*) FROM existing_tables WHERE table_name = 'approved_account'
);

IF @approved_account_exists > 0 THEN
    SET @create_all_users_view = CONCAT(@create_all_users_view, 
        'UNION ALL SELECT username, ''approved_account'' AS source_table FROM approved_account WHERE username IS NOT NULL '
    );
END IF;

-- Check if users table exists
SET @users_exists = (
    SELECT COUNT(*) FROM existing_tables WHERE table_name = 'users'
);

IF @users_exists > 0 THEN
    SET @create_all_users_view = CONCAT(@create_all_users_view, 
        'UNION ALL SELECT username, ''users'' AS source_table FROM users WHERE username IS NOT NULL '
    );
END IF;

-- Create the view
PREPARE stmt FROM @create_all_users_view;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Now check for duplicates
SELECT username, COUNT(*) AS count, GROUP_CONCAT(source_table) AS tables
FROM all_users_view
GROUP BY username
HAVING COUNT(*) > 1;

-- ========== Check for data that would violate foreign key constraints (if tables exist) ==========
SELECT 'Checking for data that would violate foreign key constraints...' AS message;

-- Check customerorder and customeraccount if both exist
SET @check_customerorder_customeraccount = (
    SELECT COUNT(*) FROM existing_tables 
    WHERE table_name IN ('customerorder', 'customeraccount') 
    GROUP BY table_name HAVING COUNT(*) = 2
);

SET @check_customerorder_customeraccount = IFNULL(@check_customerorder_customeraccount, 0);

SET @query = 'SELECT ''customerorder table: customerid values not in customeraccount'' AS issue, COUNT(*) AS count FROM customerorder co WHERE co.customerid IS NOT NULL AND NOT EXISTS (SELECT 1 FROM customeraccount ca WHERE ca.customerid = co.customerid)';
SET @skip_message = 'SELECT ''Skipping check: customerorder or customeraccount table does not exist'' AS issue, 0 AS count';

SET @sql = IF(@check_customerorder_customeraccount > 0, @query, @skip_message);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check customerorder and branches if both exist
SET @check_customerorder_branches = (
    SELECT COUNT(*) FROM existing_tables 
    WHERE table_name IN ('customerorder', 'branches') 
    GROUP BY table_name HAVING COUNT(*) = 2
);

SET @check_customerorder_branches = IFNULL(@check_customerorder_branches, 0);

SET @query = 'SELECT ''customerorder table: branch_id values not in branches'' AS issue, COUNT(*) AS count FROM customerorder co WHERE co.branch_id IS NOT NULL AND NOT EXISTS (SELECT 1 FROM branches b WHERE b.branch_id = co.branch_id)';
SET @skip_message = 'SELECT ''Skipping check: customerorder or branches table does not exist'' AS issue, 0 AS count';

SET @sql = IF(@check_customerorder_branches > 0, @query, @skip_message);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check orderlog and products if both exist
SET @check_orderlog_products = (
    SELECT COUNT(*) FROM existing_tables 
    WHERE table_name IN ('orderlog', 'products') 
    GROUP BY table_name HAVING COUNT(*) = 2
);

SET @check_orderlog_products = IFNULL(@check_orderlog_products, 0);

SET @query = 'SELECT ''orderlog table: productcode values not in products'' AS issue, COUNT(*) AS count FROM orderlog ol WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.productcode = ol.productcode)';
SET @skip_message = 'SELECT ''Skipping check: orderlog or products table does not exist'' AS issue, 0 AS count';

SET @sql = IF(@check_orderlog_products > 0, @query, @skip_message);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ========== Check for truncated data (if approved_account table exists) ==========
SELECT 'Checking for truncated data...' AS message;

SET @check_approved_account = (
    SELECT COUNT(*) FROM existing_tables WHERE table_name = 'approved_account'
);

SET @query = 'SELECT ''approved_account table: truncated passwords'' AS issue, COUNT(*) AS count FROM approved_account WHERE LENGTH(password) < 50';
SET @skip_message = 'SELECT ''Skipping check: approved_account table does not exist'' AS issue, 0 AS count';

SET @sql = IF(@check_approved_account > 0, @query, @skip_message);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ========== Recommend data cleanup steps based on issues found ==========
SELECT 'Checking for data cleanup recommendations...' AS message;

-- Orphaned orderlog records
SET @check_orderlog_customerorder = (
    SELECT COUNT(*) FROM existing_tables 
    WHERE table_name IN ('orderlog', 'customerorder') 
    GROUP BY table_name HAVING COUNT(*) = 2
);

SET @check_orderlog_customerorder = IFNULL(@check_orderlog_customerorder, 0);

IF @check_orderlog_customerorder > 0 THEN
    SET @has_orphaned_records = (
        SELECT EXISTS (
            SELECT 1 FROM orderlog ol
            WHERE NOT EXISTS (SELECT 1 FROM customerorder co WHERE co.orderid = ol.orderid)
            LIMIT 1
        )
    );
    
    IF @has_orphaned_records > 0 THEN
        SELECT 'To fix: Remove orphaned orderlog records' AS recommendation;
    END IF;
END IF;

-- Duplicate user_id values
SET @check_users = (
    SELECT COUNT(*) FROM existing_tables WHERE table_name = 'users'
);

IF @check_users > 0 THEN
    SET @has_duplicate_users = (
        SELECT EXISTS (
            SELECT 1 FROM users 
            GROUP BY user_id 
            HAVING COUNT(*) > 1
            LIMIT 1
        )
    );
    
    IF @has_duplicate_users > 0 THEN
        SELECT 'To fix: Update duplicate user_id values in users table' AS recommendation;
    END IF;
END IF;

-- Summary
SELECT 'Data integrity check completed. Review results and proceed with fixes as needed.' AS summary; 