<?php
require_once '../../includes/config.php';
require_once '../access_control.php';

header('Content-Type: application/json');

try {
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['request_ids']) || !isset($data['status'])) {
        throw new Exception('Request IDs and status are required');
    }

    $requestIds = array_map('intval', $data['request_ids']);
    $status = $data['status'];

    if (empty($requestIds)) {
        throw new Exception('No request IDs provided');
    }

    // Validate status
    $validStatuses = ['pending', 'approved', 'rejected'];
    if (!in_array($status, $validStatuses)) {
        throw new Exception('Invalid status');
    }

    $pdo = $GLOBALS['pdo'];
    $pdo->beginTransaction();

    // Get request details for logging and notifications
    $placeholders = str_repeat('?,', count($requestIds) - 1) . '?';
    $stmt = $pdo->prepare("
        SELECT r.*, u.username, u.first_name, u.last_name, u.useremail
        FROM requests r
        JOIN users u ON r.user_id = u.user_id
        WHERE r.request_id IN ($placeholders)
    ");
    $stmt->execute($requestIds);
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($requests)) {
        throw new Exception('No valid requests found');
    }

    // Update request statuses
    $stmt = $pdo->prepare("
        UPDATE requests 
        SET status = ?,
            updated_at = CURRENT_TIMESTAMP
        WHERE request_id IN ($placeholders)
    ");
    
    $params = array_merge([$status], $requestIds);
    $stmt->execute($params);

    // Log the action
    logAdminAction("Updated " . count($requestIds) . " requests status to $status");

    // Send notifications to users
    foreach ($requests as $request) {
        sendRequestStatusNotification($request, $status);
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => count($requestIds) . ' requests updated successfully'
    ]);

} catch (Exception $e) {
    if (isset($pdo)) {
        $pdo->rollBack();
    }
    error_log("Error in update-requests.php: " . $e->getMessage());
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

function sendRequestStatusNotification($request, $status) {
    $subject = "Request Status Update - HFC Management System";
    $message = "Hello {$request['first_name']},\n\n"
             . "Your request has been {$status}.\n"
             . "Request Type: {$request['request_type']}\n"
             . "Date: " . date('M d, Y', strtotime($request['created_at'])) . "\n\n"
             . "Best regards,\nHFC Admin Team";

    mail($request['useremail'], $subject, $message);
} 