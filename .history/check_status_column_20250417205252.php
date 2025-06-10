<?php
// This is a temporary script to check the account_requests status values
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'Henrich/includes/config.php';

try {
    // Check if status column exists
    $stmt = $GLOBALS['pdo']->prepare("
        SELECT COLUMN_NAME, DATA_TYPE
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'account_requests' 
        AND COLUMN_NAME = 'status'
    ");
    $stmt->execute();
    $column_info = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<h2>Status Column Information</h2>";
    if ($column_info) {
        echo "Status column exists with data type: " . $column_info['DATA_TYPE'] . "<br>";
    } else {
        echo "Status column does not exist in the account_requests table!<br>";
    }
    
    // Check status values
    $stmt = $GLOBALS['pdo']->prepare("
        SELECT request_id, firstname, lastname, status
        FROM account_requests
        LIMIT 10
    ");
    $stmt->execute();
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Account Requests Status Values</h2>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Name</th><th>Status</th><th>Status (binary)</th></tr>";
    
    foreach ($requests as $row) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['request_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) . "</td>";
        echo "<td>" . htmlspecialchars($row['status'] ?? 'NULL') . "</td>";
        echo "<td>" . bin2hex($row['status'] ?? '') . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Check for default value
    $stmt = $GLOBALS['pdo']->prepare("
        SELECT COLUMN_DEFAULT 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'account_requests' 
        AND COLUMN_NAME = 'status'
    ");
    $stmt->execute();
    $default_value = $stmt->fetchColumn();
    
    echo "<h2>Status Column Default Value</h2>";
    echo "Default value: " . ($default_value ?? 'NULL') . "<br>";
    
    // Check table definition
    $stmt = $GLOBALS['pdo']->prepare("SHOW CREATE TABLE account_requests");
    $stmt->execute();
    $table_def = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<h2>Table Definition</h2>";
    echo "<pre>" . htmlspecialchars($table_def['Create Table'] ?? '') . "</pre>";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 