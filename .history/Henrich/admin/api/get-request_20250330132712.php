<?php
require_once '../../includes/config.php';
require_once '../access_control.php';

header('Content-Type: application/json');

try {
    if (!isset($_GET['id'])) {
        throw new Exception('Request ID is required');
    }

    $requestId = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    if (!$requestId) {
        throw new Exception('Invalid request ID');
    }

    $stmt = $GLOBALS['pdo']->prepare("
        SELECT r.*, u.username, u.first_name, u.last_name, u.useremail, u.profile_picture
        FROM requests r
        JOIN users u ON r.user_id = u.user_id
        WHERE r.request_id = :request_id
    ");

    $stmt->execute([':request_id' => $requestId]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$request) {
        throw new Exception('Request not found');
    }

    echo json_encode([
        'success' => true,
        'request' => $request
    ]);

} catch (Exception $e) {
    error_log("Error in get-request.php: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 