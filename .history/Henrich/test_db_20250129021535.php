<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Database Connection Test</h2>";

try {
    require_once 'includes/config.php';
    
    // Test PDO connection
    echo "<p>Testing PDO connection...</p>";
    $result = $pdo->query("SELECT DATABASE() as db")->fetch();
    echo "Connected to database: " . $result['db'] . "<br>";
    
    // Test users table
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo "Users table exists<br>";
        
        // Check users table structure
        echo "<pre>";
        print_r($pdo->query("DESCRIBE users")->fetchAll());
        echo "</pre>";
    } else {
        echo "Users table does not exist!<br>";
    }
} catch (Exception $e) {