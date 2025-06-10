<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/config.php';

try {
    // Test database connection
    $result = $pdo->query("SELECT 'Database connection successful' as message")->fetch();
    echo $result['message'];
    
    // Test session
    session_start();
    echo "\nSession ID: " . session_id();
    
    // Test user table
    $users = $pdo->query("SELECT COUNT(*) as count FROM users")->fetch();
    echo "\nTotal users: " . $users['count'];
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
