<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require '../includes/config.php';

try {
    // Admin account details
    $admin_email = 'admin@henrich.com';
    $admin_password = 'admin-san123';
    $admin_role = 'admin';
    $admin_status = 'active';
    
    // Hash the password
    $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
    
    // Check if admin exists
    $check_stmt = $pdo->prepare("SELECT user_id FROM users WHERE useremail = ?");
    $check_stmt->execute([$admin_email]);
    $admin = $check_stmt->fetch();
    
    if ($admin) {
        // Update existing admin
        $stmt = $pdo->prepare("UPDATE users SET password = ?, role = ?, status = ? WHERE useremail = ?");
        $stmt->execute([$hashed_password, $admin_role, $admin_status, $admin_email]);
        echo "Admin account updated successfully!\n";
    } else {
        // Create new admin
        $stmt = $pdo->prepare("INSERT INTO users (useremail, password, role, status, username) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$admin_email, $hashed_password, $admin_role, $admin_status, 'admin']);
        echo "Admin account created successfully!\n";
    }
    
    // Verify the account
    $verify_stmt = $pdo->prepare("SELECT * FROM users WHERE useremail = ?");
    $verify_stmt->execute([$admin_email]);
    $verified_admin = $verify_stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($verified_admin) {
        echo "\nVerification successful:\n";
        echo "Email: " . $verified_admin['useremail'] . "\n";
        echo "Role: " . $verified_admin['role'] . "\n";
        echo "Status: " . $verified_admin['status'] . "\n";
        echo "Password verification: " . (password_verify($admin_password, $verified_admin['password']) ? "Success" : "Failed") . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 