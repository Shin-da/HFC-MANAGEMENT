<?php
// Start output buffering immediately
ob_start();

// Set strict error handling
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/debug.log');

// Set headers
header('Content-Type: application/json');
header('Cache-Control: no-cache');

// Log the incoming request
error_log("Received request: " . print_r($_POST, true));

require_once '../includes/config.php';

try {
    if (empty($_POST['request_id'])) {
        throw new Exception("Request ID is missing");
    }

    // Log the request ID
    error_log("Processing request ID: " . $_POST['request_id']);

    $request_id = intval($_POST['request_id']);
    $temp_password = bin2hex(random_bytes(8));

    // Start transaction
    $conn->begin_transaction();

    // Get request details
    $stmt = $conn->prepare("SELECT * FROM account_request WHERE user_id = ?");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $request = $stmt->get_result()->fetch_assoc();

    if (!$request) {
        throw new Exception("Request not found");
    }

    // Create approved account
    $hashed_password = password_hash($temp_password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO approved_account (usermail, username, role, password, first_name, last_name, status, created_at, updated_at, is_online) VALUES (?, ?, ?, ?, ?, ?, 'active', NOW(), NOW(), FALSE)");
    $stmt->bind_param("ssssss", 
        $request['usermail'],
        $request['username'],
        $request['role'],
        $hashed_password,
        $request['first_name'],
        $request['last_name']
    );
    $stmt->execute();

    // Delete request
    $stmt = $conn->prepare("DELETE FROM account_request WHERE user_id = ?");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();

    $conn->commit();

    // Clear any output before sending JSON
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Account approved successfully',
        'password' => $temp_password,
        'username' => $request['username'],
        'email' => $request['usermail']
    ]);

} catch (Exception $e) {
    error_log("Error in approve-request.php: " . $e->getMessage());
    
    // Clear any output before sending error JSON
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

exit;
