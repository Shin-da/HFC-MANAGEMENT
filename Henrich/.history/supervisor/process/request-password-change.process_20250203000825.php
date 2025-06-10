<?php
require_once '../../includes/session.php';
require_once '../../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
            DB_USER,
            DB_PASSWORD,
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        );

        $stmt = $pdo->prepare("SELECT password FROM users WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $_POST['user_id']]);
        $user = $stmt->fetch();

        if ($user && password_verify($_POST['oldpassword'], $user['password'])) {
            // Generate password reset token and expiry
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $stmt = $pdo->prepare("
                UPDATE users 
                SET reset_token = :token,
                    reset_expires = :expires
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
