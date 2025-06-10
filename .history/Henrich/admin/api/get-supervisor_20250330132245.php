<?php
require_once '../../includes/config.php';
require_once '../access_control.php';

header('Content-Type: application/json');

try {
    if (!isset($_GET['id'])) {
        throw new Exception('User ID is required');
    }

    $userId = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    if (!$userId) {
        throw new Exception('Invalid user ID');
    }

    $stmt = $GLOBALS['pdo']->prepare("
        SELECT user_id, username, useremail, first_name, last_name, department, status, profile_picture
        FROM users 
        WHERE user_id = :user_id AND role = 'supervisor'
    ");

    $stmt->execute([':user_id' => $userId]);
    $supervisor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$supervisor) {
        throw new Exception('Supervisor not found');
    }

    echo json_encode([
        'success' => true,
        'supervisor' => $supervisor
    ]);

} catch (Exception $e) {
    error_log("Error in get-supervisor.php: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 