<?php
require_once '../../includes/config.php';
require_once '../access_control.php';

header('Content-Type: application/json');

try {
    if (!isset($_POST['current_password']) || !isset($_POST['new_password']) || !isset($_POST['confirm_password'])) {
        throw new Exception('All password fields are required');
    }

    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Validate password match
    if ($newPassword !== $confirmPassword) {
        throw new Exception('New passwords do not match');
    }

    // Validate password strength
    if (strlen($newPassword) < 8) {
        throw new Exception('Password must be at least 8 characters long');
    }
    if (!preg_match('/[A-Z]/', $newPassword)) {
        throw new Exception('Password must contain at least one uppercase letter');
    }
    if (!preg_match('/[a-z]/', $newPassword)) {
        throw new Exception('Password must contain at least one lowercase letter');
    }
    if (!preg_match('/[0-9]/', $newPassword)) {
        throw new Exception('Password must contain at least one number');
    }

    // Verify current password
    $stmt = $GLOBALS['pdo']->prepare("
        SELECT password 
        FROM users 
        WHERE user_id = :user_id
    ");
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!password_verify($currentPassword, $user['password'])) {
        throw new Exception('Current password is incorrect');
    }

    // Update password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $GLOBALS['pdo']->prepare("
        UPDATE users 
        SET password = :password
        WHERE user_id = :user_id
    ");

    $stmt->execute([
        ':password' => $hashedPassword,
        ':user_id' => $_SESSION['user_id']
    ]);

    // Log the action
    logAdminAction("Changed password");

    echo json_encode([
        'success' => true,
        'message' => 'Password changed successfully'
    ]);

} catch (Exception $e) {
    error_log("Error in change-password.php: " . $e->getMessage());
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