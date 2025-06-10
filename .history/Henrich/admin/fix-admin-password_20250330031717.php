<?php
require_once __DIR__ . '/../includes/config.php';

try {
    // The email and password we want to set
    $admin_email = 'admin@henrich.com';
    $new_password = 'admin-san123';
    
    // Hash the password correctly
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    // First, check if the admin exists
    $check_stmt = $pdo->prepare("SELECT * FROM users WHERE useremail = ?");
    $check_stmt->execute([$admin_email]);
    $user = $check_stmt->fetch();
    
    if ($user) {
        // Update existing admin's password
        $update_stmt = $pdo->prepare("UPDATE users SET password = ? WHERE useremail = ?");
        $update_stmt->execute([$hashed_password, $admin_email]);
        echo "Success: Admin password has been updated with the correct hash.";
    } else {
        // Create new admin account if it doesn't exist
        $insert_stmt = $pdo->prepare("
            INSERT INTO users (firstname, lastname, useremail, password, role, status) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $insert_stmt->execute([
            'Henrich', 
            'Admin', 
            $admin_email,
            $hashed_password,
            'admin',
            'active'
        ]);
        echo "Success: New admin account has been created with the correct hash.";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
} 