<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Database Connection Test</h2>";

try {
    $dsn = "mysql:host=localhost;dbname=dbhenrichfoodcorps";
    $username = "root";
    $password = "";

    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    echo "<p style='color:green'>Connected to database successfully!</p>";
    
    // Test users table
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color:green'>Users table exists</p>";
        
        // Show table structure
        echo "<h3>Table Structure:</h3>";
        echo "<pre>";
        print_r($pdo->query("DESCRIBE users")->fetchAll());
        echo "</pre>";
        
        // Show sample data
        echo "<h3>Sample Data:</h3>";
        echo "<pre>";
        print_r($pdo->query("SELECT * FROM users LIMIT 3")->fetchAll());
        echo "</pre>";
    } else {
        echo "<p style='color:red'>Users table does not exist!</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color:red'>Connection failed: " . $e->getMessage() . "</p>";
}
