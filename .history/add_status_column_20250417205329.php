<?php
// This script adds or fixes the status column in the account_requests table
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'Henrich/includes/config.php';

try {
    // Check if table exists
    $tableCheck = $GLOBALS['pdo']->query("SHOW TABLES LIKE 'account_requests'");
    if ($tableCheck->rowCount() == 0) {
        die("The account_requests table does not exist.");
    }
    
    // First check if status column exists
    $columnCheck = $GLOBALS['pdo']->query("SHOW COLUMNS FROM account_requests LIKE 'status'");
    $hasStatusColumn = $columnCheck->rowCount() > 0;
    
    // Begin transaction
    $GLOBALS['pdo']->beginTransaction();
    
    if (!$hasStatusColumn) {
        echo "Status column does not exist. Adding it now...<br>";
        // Add status column if it doesn't exist
        $GLOBALS['pdo']->exec("ALTER TABLE account_requests ADD COLUMN status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending' AFTER reason");
        echo "Status column added successfully!<br>";
    } else {
        echo "Status column exists. Checking data type...<br>";
        
        // Get column info
        $columnInfo = $columnCheck->fetch(PDO::FETCH_ASSOC);
        echo "Current status column type: " . $columnInfo['Type'] . "<br>";
        
        // If not ENUM, modify it
        if (strpos($columnInfo['Type'], "enum") === false) {
            echo "Converting status column to ENUM type...<br>";
            $GLOBALS['pdo']->exec("ALTER TABLE account_requests MODIFY COLUMN status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending'");
            echo "Status column modified successfully!<br>";
        }
    }
    
    // Set default values for existing rows
    $GLOBALS['pdo']->exec("UPDATE account_requests SET status = 'pending' WHERE status IS NULL OR status = ''");
    echo "Default status values set for existing rows.<br>";
    
    // Check if rejection_reason column exists
    $reasonCheck = $GLOBALS['pdo']->query("SHOW COLUMNS FROM account_requests LIKE 'rejection_reason'");
    if ($reasonCheck->rowCount() == 0) {
        echo "Adding rejection_reason column...<br>";
        $GLOBALS['pdo']->exec("ALTER TABLE account_requests ADD COLUMN rejection_reason TEXT NULL AFTER processed_by");
        echo "Rejection reason column added.<br>";
    }
    
    // Add processed_by column if it doesn't exist
    $processedByCheck = $GLOBALS['pdo']->query("SHOW COLUMNS FROM account_requests LIKE 'processed_by'");
    if ($processedByCheck->rowCount() == 0) {
        echo "Adding processed_by column...<br>";
        $GLOBALS['pdo']->exec("ALTER TABLE account_requests ADD COLUMN processed_by INT NULL AFTER request_date");
        echo "Processed by column added.<br>";
    }
    
    // Add processed_date column if it doesn't exist
    $processedDateCheck = $GLOBALS['pdo']->query("SHOW COLUMNS FROM account_requests LIKE 'processed_date'");
    if ($processedDateCheck->rowCount() == 0) {
        echo "Adding processed_date column...<br>";
        $GLOBALS['pdo']->exec("ALTER TABLE account_requests ADD COLUMN processed_date TIMESTAMP NULL AFTER processed_by");
        echo "Processed date column added.<br>";
    }
    
    // Commit all changes
    $GLOBALS['pdo']->commit();
    
    echo "<hr>";
    echo "<h2>Current Table Structure</h2>";
    
    // Display current table structure
    $columns = $GLOBALS['pdo']->query("DESCRIBE account_requests")->fetchAll(PDO::FETCH_ASSOC);
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    foreach ($columns as $column) {
        echo "<tr>";
        foreach ($column as $key => $value) {
            echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
        }
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Show some sample data
    echo "<h2>Sample Data</h2>";
    $samples = $GLOBALS['pdo']->query("SELECT request_id, firstname, lastname, status, processed_date FROM account_requests LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($samples) > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr>";
        foreach (array_keys($samples[0]) as $header) {
            echo "<th>" . htmlspecialchars($header) . "</th>";
        }
        echo "</tr>";
        
        foreach ($samples as $row) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
            }
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "No data found in the table.";
    }
    
} catch (PDOException $e) {
    if ($GLOBALS['pdo']->inTransaction()) {
        $GLOBALS['pdo']->rollBack();
    }
    echo "Error: " . $e->getMessage();
}
?> 