<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/../includes/config.php';

try {
    // The email we want to check
    $admin_email = 'admin@henrich.com';
    
    // Check if the admin exists
    $check_stmt = $pdo->prepare("SELECT * FROM users WHERE useremail = ?");
    $check_stmt->execute([$admin_email]);
    $user = $check_stmt->fetch();
    
    if ($user) {
        echo "Account found:\n";
        echo "User ID: " . $user['user_id'] . "\n";
        echo "Email: " . $user['useremail'] . "\n";
        echo "Role: " . $user['role'] . "\n";
        echo "Status: " . $user['status'] . "\n";
        echo "Password hash length: " . strlen($user['password']) . "\n";
        
        // Test password verification
        $test_password = 'admin-san123';
        if (password_verify($test_password, $user['password'])) {
            echo "\nPassword verification successful!\n";
        } else {
            echo "\nPassword verification failed!\n";
            echo "This means the stored hash doesn't match the password.\n";
        }

        // Show all columns and their values
        echo "\nAll account details:\n";
        foreach ($user as $key => $value) {
            if ($key !== 'password') {  // Don't show the password hash
                echo $key . ": " . $value . "\n";
            }
        }
    } else {
        echo "No account found with email: $admin_email\n";
        
        // Show all users in the database
        echo "\nListing all users in the database:\n";
        $all_users = $pdo->query("SELECT useremail, role FROM users")->fetchAll();
        foreach ($all_users as $user) {
            echo "Email: " . $user['useremail'] . ", Role: " . $user['role'] . "\n";
        }
    }
    
    // Show table structure
    echo "\nTable structure:\n";
    $columns = $pdo->query("SHOW COLUMNS FROM users")->fetchAll();
    foreach ($columns as $column) {
        echo $column['Field'] . " - " . $column['Type'] . "\n";
    }
    
} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
} 