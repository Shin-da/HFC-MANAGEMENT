<?php
require_once '../../includes/config.php';
require_once '../access_control.php';

header('Content-Type: application/json');

try {
    if (!isset($_POST['first_name']) || !isset($_POST['last_name'])) {
        throw new Exception('First name and last name are required');
    }

    $firstName = filter_var($_POST['first_name'], FILTER_SANITIZE_STRING);
    $lastName = filter_var($_POST['last_name'], FILTER_SANITIZE_STRING);

    if (empty($firstName) || empty($lastName)) {
        throw new Exception('First name and last name cannot be empty');
    }

    $stmt = $GLOBALS['pdo']->prepare("
        UPDATE users 
        SET first_name = :first_name,
            last_name = :last_name
        WHERE user_id = :user_id
    ");

    $stmt->execute([
        ':first_name' => $firstName,
        ':last_name' => $lastName,
        ':user_id' => $_SESSION['user_id']
    ]);

    // Log the action
    logAdminAction("Updated profile information");

    echo json_encode([
        'success' => true,
        'message' => 'Profile updated successfully'
    ]);

} catch (Exception $e) {
    error_log("Error in update-profile.php: " . $e->getMessage());
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