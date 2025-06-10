<?php
// This is a temporary script to check the database schema
// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once 'Henrich/includes/config.php';

try {
    // Check if rejection_reason column exists in account_requests table
    $stmt = $GLOBALS['pdo']->prepare("
        SELECT COLUMN_NAME
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'account_requests' 
        AND COLUMN_NAME = 'rejection_reason'
    ");
    $stmt->execute();
    $column_exists = $stmt->rowCount() > 0;
    
    if (!$column_exists) {
        echo "The rejection_reason column does not exist. Adding it now...\n";
        
        // Add column if it doesn't exist
        $alter_stmt = $GLOBALS['pdo']->prepare("
            ALTER TABLE account_requests 
            ADD COLUMN rejection_reason TEXT DEFAULT NULL AFTER processed_by
        ");
        $alter_stmt->execute();
        echo "Column added successfully!\n";
    } else {
        echo "The rejection_reason column already exists.\n";
    }
    
    // Show table structure
    $desc_stmt = $GLOBALS['pdo']->prepare("DESCRIBE account_requests");
    $desc_stmt->execute();
    $columns = $desc_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nTable structure:\n";
    echo "---------------------\n";
    foreach ($columns as $column) {
        echo $column['Field'] . " - " . $column['Type'] . " - " . ($column['Null'] === 'YES' ? 'NULL' : 'NOT NULL') . "\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 