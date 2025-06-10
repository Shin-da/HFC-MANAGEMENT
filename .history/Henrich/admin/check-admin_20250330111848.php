<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require '../includes/config.php';

try {
    // Check if admin account exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE useremail = ?");
    $stmt->execute(['admin@henrich.com']);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        echo "Admin account exists:\n";
        echo "User ID: " . $admin['user_id'] . "\n";
        echo "Email: " . $admin['useremail'] . "\n";
        echo "Role: " . $admin['role'] . "\n";
        echo "Status: " . $admin['status'] . "\n";
        echo "Password hash length: " . strlen($admin['password']) . "\n";
        
        // Test password verification
        $test_password = 'admin-san123';
        if (password_verify($test_password, $admin['password'])) {
            echo "Password verification successful!\n";
        } else {
            echo "Password verification failed!\n";
        }
        
        echo "\nFull account details:\n";
        print_r($admin);
    } else {
        echo "Admin account not found!\n";
        
        // Show all users in the database
        echo "\nAll users in database:\n";
        $stmt = $pdo->query("SELECT user_id, useremail, role, status FROM users");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            print_r($row);
        }
    }
    
    // Show table structure
    echo "\nUsers table structure:\n";
    $stmt = $pdo->query("DESCRIBE users");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 