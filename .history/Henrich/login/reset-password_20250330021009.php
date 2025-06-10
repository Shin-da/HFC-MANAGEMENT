<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

$token = $_GET['token'] ?? '';
$error = '';
$success = '';

if (empty($token)) {
    header("Location: ../index.php?error=Invalid reset link");
    exit();
}

try {
    // Verify token and check expiration
    $stmt = $pdo->prepare("
        SELECT user_id 
        FROM users 
        WHERE password_reset_token = ? 
        AND password_reset_expires > NOW() 
        AND status = 'active'
    ");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        header("Location: ../index.php?error=Invalid or expired reset link");
        exit();
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        // Validate password
        if (strlen($password) < 8) {
            $error = "Password must be at least 8 characters long";
        } elseif ($password !== $confirm_password) {
            $error = "Passwords do not match";
        } else {
            // Update password and clear reset token
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("
                UPDATE users 
                SET password = ?,
                    password_reset_token = NULL,
                    password_reset_expires = NULL,
                    last_password_change = CURRENT_TIMESTAMP
                WHERE user_id = ?
            ");
            
            if ($stmt->execute([$hashed_password, $user['user_id']])) {
                $success = "Password has been reset successfully. You can now login with your new password.";
                logActivity($user['user_id'], 'password_reset', 'Password reset successful');
            } else {
                $error = "Failed to reset password. Please try again.";
            }
        }
    }
} catch (Exception $e) {
    error_log("Password reset error: " . $e->getMessage());
    $error = "An error occurred. Please try again later.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - HFC Management</title>
    <?php require '../reusable/header.php'; ?>
    <link rel="stylesheet" type="text/css" href="../assets/css/variables.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/login.css">
</head>
<body>
    <div class="session">
        <div class="session-content">
            <h2>Reset Password</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                    <p><a href="../index.php">Click here to login</a></p>
                </div>
            <?php else: ?>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="password">New Password</label>
                        <input type="password" id="password" name="password" required minlength="8">
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required minlength="8">
                    </div>
                    
                    <button type="submit" class="btn-primary">Reset Password</button>
                </form>
            <?php endif; ?>
            
            <div class="form-footer">
                <a href="../index.php">Back to Login</a>
            </div>
        </div>
    </div>
</body>
</html> 