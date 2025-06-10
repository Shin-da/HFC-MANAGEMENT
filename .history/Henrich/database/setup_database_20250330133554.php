<?php
require_once '../includes/config.php';

try {
    // Create database if it doesn't exist
    $pdo = new PDO("mysql:host=localhost", $GLOBALS['db_user'], $GLOBALS['db_pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $dbname = $GLOBALS['db_name'];
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
    echo "Database '$dbname' created or already exists.\n";
    
    // Select the database
    $pdo->exec("USE `$dbname`");
    
    // Read and execute requests.sql
    $sql = file_get_contents('requests.sql');
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $pdo->exec($statement);
            echo "Executed: " . substr($statement, 0, 50) . "...\n";
        }
    }
    
    echo "\nDatabase setup completed successfully!\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 