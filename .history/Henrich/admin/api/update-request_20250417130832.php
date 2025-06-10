<?php
require_once '../../includes/config.php';
require_once '../access_control.php';

header('Content-Type: application/json');

try {
    error_log("update-request.php: Script started."); // Log start

    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    error_log("update-request.php: POST data received: " . print_r($data, true)); // Log received data
    
    if (!isset($data['request_id']) || !isset($data['status'])) {
        throw new Exception('Request ID and status are required');
    }

    $requestId = filter_var($data['request_id'], FILTER_VALIDATE_INT);
    $status = $data['status'];

    if (!$requestId) {
        throw new Exception('Invalid request ID');
    }

    // Validate status
    $validStatuses = ['pending', 'approved', 'rejected'];
    if (!in_array($status, $validStatuses)) {
        throw new Exception('Invalid status');
    }

    $pdo = $GLOBALS['pdo'];
    error_log("update-request.php: Starting transaction."); // Log before transaction
    $pdo->beginTransaction();
    error_log("update-request.php: Transaction started."); // Log after transaction

    // Get request details for logging
    error_log("update-request.php: Fetching request details for ID: $requestId"); // Log before fetch
    $stmt = $pdo->prepare("
        SELECT r.*, u.username, u.first_name, u.last_name, u.useremail -- Added useremail for notification
        FROM requests r
        JOIN users u ON r.user_id = u.user_id
        WHERE r.request_id = :request_id
    ");
    $stmt->execute([':request_id' => $requestId]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);
    error_log("update-request.php: Fetched request: " . ($request ? print_r($request, true) : 'Not Found')); // Log fetch result

    if (!$request) {
        throw new Exception('Request not found');
    }

    // Update request status
    error_log("update-request.php: Updating status to $status for ID: $requestId"); // Log before update
    $stmt = $pdo->prepare("
        UPDATE requests 
        SET status = :status,
            updated_at = CURRENT_TIMESTAMP
        WHERE request_id = :request_id
    ");
    
    $stmt->execute([
        ':status' => $status,
        ':request_id' => $requestId
    ]);
    error_log("update-request.php: Status updated."); // Log after update

    // Log the action
    // logAdminAction("Updated request ID: $requestId status to $status"); // Temporarily commented out
    error_log("update-request.php: Admin action logging skipped (commented out).");

    // Send notification to user
    // sendRequestStatusNotification($request, $status); // Keep commented out
    error_log("update-request.php: Notification sending skipped (commented out).");

    error_log("update-request.php: Committing transaction."); // Log before commit
    $pdo->commit();
    error_log("update-request.php: Transaction committed."); // Log after commit

    echo json_encode([
        'success' => true,
        'message' => 'Request status updated successfully'
    ]);

} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) { // Check if PDO is set and in transaction
        error_log("update-request.php: Rolling back transaction due to error."); // Log rollback
        $pdo->rollBack();
    }
    error_log("Error in update-request.php: " . $e->getMessage());
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