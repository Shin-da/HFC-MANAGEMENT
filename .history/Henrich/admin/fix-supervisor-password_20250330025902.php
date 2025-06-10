<?php
require_once '../includes/config.php';

try {
    // The email and password we want to fix
    $supervisor_email = 'henrichsupervisor@henrich.com';
    $new_password = 'supervisor-san123';
    
    // Hash the password correctly
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    // First, check if the supervisor exists
    $check_stmt = $pdo->prepare("SELECT * FROM users WHERE useremail = ?");
    $check_stmt->execute([$supervisor_email]);
    $user = $check_stmt->fetch();
    
    if ($user) {
        // Update existing supervisor's password
        $update_stmt = $pdo->prepare("UPDATE users SET password = ? WHERE useremail = ?");
        $update_stmt->execute([$hashed_password, $supervisor_email]);
        echo "Success: Supervisor password has been updated with the correct hash.";
    } else {
        // Create new supervisor account if it doesn't exist
        $insert_stmt = $pdo->prepare("
            INSERT INTO users (firstname, lastname, useremail, password, role, status) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $insert_stmt->execute([
            'Henrich', 
            'Supervisor', 
            $supervisor_email,
            $hashed_password,
            'supervisor',
            'active'
        ]);
        echo "Success: New supervisor account has been created with the correct hash.";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
} 