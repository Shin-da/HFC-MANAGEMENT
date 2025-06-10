<?php
require_once __DIR__ . '/../includes/config.php';

try {
    // Enable error reporting
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // The email we want to check
    $supervisor_email = 'henrichsupervisor@henrich.com';
    
    // Check if the supervisor exists
    $check_stmt = $pdo->prepare("SELECT * FROM users WHERE useremail = ?");
    $check_stmt->execute([$supervisor_email]);
    $user = $check_stmt->fetch();
    
    if ($user) {
        echo "Account found:\n";
        echo "User ID: " . $user['user_id'] . "\n";
        echo "Email: " . $user['useremail'] . "\n";
        echo "Role: " . $user['role'] . "\n";
        echo "Status: " . $user['status'] . "\n";
        echo "Password hash length: " . strlen($user['password']) . "\n";
        
        // Test password verification
        $test_password = 'supervisor-san123';
        if (password_verify($test_password, $user['password'])) {
            echo "\nPassword verification successful!\n";
        } else {
            echo "\nPassword verification failed!\n";
            echo "This means the stored hash doesn't match the password.\n";
        }
    } else {
        echo "No account found with email: $supervisor_email\n";
        
        // Show all users in the database
        echo "\nListing all users in the database:\n";
        $all_users = $pdo->query("SELECT useremail, role FROM users")->fetchAll();
        foreach ($all_users as $user) {
            echo "Email: " . $user['useremail'] . ", Role: " . $user['role'] . "\n";
        }
    }
    
} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
    
    // Show table structure
    try {
        echo "\nChecking table structure:\n";
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        echo "Tables in database:\n";
        print_r($tables);
        
        if (in_array('users', $tables)) {
            echo "\nStructure of users table:\n";
            $columns = $pdo->query("SHOW COLUMNS FROM users")->fetchAll();
            print_r($columns);
        }
    } catch (PDOException $e2) {
        echo "Error checking structure: " . $e2->getMessage();
    }
} 