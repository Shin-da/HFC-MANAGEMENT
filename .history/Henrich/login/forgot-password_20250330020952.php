<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    try {
        $stmt = $pdo->prepare("SELECT user_id, username FROM users WHERE useremail = ? AND status = 'active'");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Generate reset token
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Store reset token
            $stmt = $pdo->prepare("
                UPDATE users 
                SET password_reset_token = ?,
                    password_reset_expires = ?
                WHERE user_id = ?
            ");
            $stmt->execute([$token, $expires, $user['user_id']]);
            
            // Send reset email
            $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/Henrich/login/reset-password.php?token=" . $token;
            $message = "
                <html>
                <body>
                    <h2>Password Reset Request</h2>
                    <p>Hello {$user['username']},</p>
                    <p>You have requested to reset your password. Click the link below to proceed:</p>
                    <p><a href='{$reset_link}'>{$reset_link}</a></p>
                    <p>This link will expire in 1 hour.</p>
                    <p>If you didn't request this, please ignore this email.</p>
                    <p>Best regards,<br>HFC Management System</p>
                </body>
                </html>
            ";
            
            if (send_notification($email, "Password Reset Request", $message)) {
                $_SESSION['success'] = "Password reset instructions have been sent to your email.";
            } else {
                $_SESSION['error'] = "Failed to send reset email. Please try again.";
            }
        } else {
            $_SESSION['error'] = "No active account found with this email address.";
        }
    } catch (Exception $e) {
        error_log("Password reset error: " . $e->getMessage());
        $_SESSION['error'] = "An error occurred. Please try again later.";
    }
    
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - HFC Management</title>
    <?php require '../reusable/header.php'; ?>
    <link rel="stylesheet" type="text/css" href="../assets/css/variables.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/login.css">
</head>
<body>
    <div class="session">
        <div class="session-content">
            <h2>Forgot Password</h2>
            <p>Enter your email address and we'll send you instructions to reset your password.</p>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <button type="submit" class="btn-primary">Send Reset Instructions</button>
            </form>
            
            <div class="form-footer">
                <a href="../index.php">Back to Login</a>
            </div>
        </div>
    </div>
</body>
</html> 