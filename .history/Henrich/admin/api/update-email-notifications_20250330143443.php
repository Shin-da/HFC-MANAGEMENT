<?php
require_once '../../includes/config.php';
require_once '../access_control.php';

header('Content-Type: application/json');

try {
    if (!isset($_POST['notifications'])) {
        throw new Exception('Notification settings are required');
    }

    $notifications = $_POST['notifications'];
    
    // Validate notification settings
    $validSettings = [
        'new_requests' => isset($notifications['new_requests']) ? 1 : 0,
        'request_updates' => isset($notifications['request_updates']) ? 1 : 0,
        'system_alerts' => isset($notifications['system_alerts']) ? 1 : 0,
        'security_alerts' => isset($notifications['security_alerts']) ? 1 : 0
    ];

    // Update notification settings
    $stmt = $GLOBALS['pdo']->prepare("
        UPDATE users 
        SET email_notifications = :notifications
        WHERE user_id = :user_id
    ");

    $stmt->execute([
        ':notifications' => json_encode($validSettings),
        ':user_id' => $_SESSION['user_id']
    ]);

    // Log the action
    logAdminAction("Updated email notification settings");

    echo json_encode([
        'success' => true,
        'message' => 'Email notification settings updated successfully'
    ]);

} catch (Exception $e) {
    error_log("Error in update-email-notifications.php: " . $e->getMessage());
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