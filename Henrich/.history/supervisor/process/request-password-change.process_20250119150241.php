<?php
require '../../session/session.php';
require '../../database/dbconnect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uid = $_POST['uid'];
    $old_password = $_POST['oldpassword'];
    
    // Validate inputs
    if (empty($uid) || empty($old_password)) {
        $_SESSION['error'] = "Please enter your current password";
        header("Location: ../myaccount.php");
        exit();
    }

    // Verify current password
    $sql = "SELECT password FROM user WHERE uid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if (password_verify($old_password, $row['password'])) {
            // Generate unique reset token
            $reset_token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+24 hours'));
            
            // Store reset token
            $token_sql = "INSERT INTO password_reset_tokens (uid, token, expiry) VALUES (?, ?, ?)";
            $token_stmt = $conn->prepare($token_sql);
            $token_stmt->bind_param("iss", $uid, $reset_token, $expiry);
            
            if ($token_stmt->execute()) {
                // Log the activity
                $activity = "Password change requested";
                $log_sql = "INSERT INTO activity_log (uid, activity, activity_type) VALUES (?, ?, 'Password Reset Request')";
                $log_stmt = $conn->prepare($log_sql);
                $log_stmt->bind_param("is", $uid, $activity);
                $log_stmt->execute();

                // Generate reset link
                $reset_link = "http://" . $_SERVER['HTTP_HOST'] . 
                            dirname($_SERVER['PHP_SELF']) . 
                            "/../../reset-password.php?token=" . $reset_token;
                
                $_SESSION['success'] = "Password reset link generated. Please check your email.";
                
                // TODO: Implement email sending functionality
                // For now, we'll just show the reset link
                $_SESSION['reset_link'] = $reset_link;
            } else {
                $_SESSION['error'] = "Error generating reset token";
            }
        } else {
            $_SESSION['error'] = "Current password is incorrect";
        }
    } else {
        $_SESSION['error'] = "User not found";
    }
    
    $stmt->close();
    $conn->close();
    
    header("Location: ../myaccount.php");
    exit();
} else {
    header("Location: ../myaccount.php");
    exit();
}
?>
