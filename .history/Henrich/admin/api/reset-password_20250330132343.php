<?php
require_once '../../includes/config.php';
require_once '../access_control.php';

header('Content-Type: application/json');

try {
    if (!isset($_POST['user_id'])) {
        throw new Exception('User ID is required');
    }

    $userId = filter_var($_POST['user_id'], FILTER_VALIDATE_INT);
    if (!$userId) {
        throw new Exception('Invalid user ID');
    }

    // Generate new random password
    $newPassword = bin2hex(random_bytes(8));
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Get user email
    $stmt = $GLOBALS['pdo']->prepare("
        SELECT useremail, username 
        FROM users 
        WHERE user_id = :user_id AND role = 'supervisor'
    ");
    $stmt->execute([':user_id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception('Supervisor not found');
    }

    // Update password
    $stmt = $GLOBALS['pdo']->prepare("
        UPDATE users 
        SET password = :password 
        WHERE user_id = :user_id AND role = 'supervisor'
    ");
    
    $stmt->execute([
        ':password' => $hashedPassword,
        ':user_id' => $userId
    ]);

    // Log the action
    logAdminAction("Reset password for supervisor ID: $userId");

    // Send email with new password
    $subject = "Password Reset - HFC Management System";
    $message = "Hello,\n\n"
             . "Your password has been reset by an administrator.\n"
             . "Your new password is: $newPassword\n\n"
             . "Please change your password after logging in.\n\n"
             . "Best regards,\nHFC Admin Team";

    mail($user['useremail'], $subject, $message);

    echo json_encode([
        'success' => true,
        'message' => 'Password reset successfully. New password has been sent to ' . $user['useremail']
    ]);

} catch (Exception $e) {
    error_log("Error in reset-password.php: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

function logAdminAction($action) {
    $stmt = $GLOBALS['pdo']->prepare("
        INSERT INTO admin_logs (admin_id, action, ip_address)
        VALUES (:admin_id, :action, :ip)
    ");

    $stmt->execute([
        ':admin_id' => $_SESSION['user_id'],
        ':action' => $action,
        ':ip' => $_SERVER['REMOTE_ADDR']
    ]);
} 