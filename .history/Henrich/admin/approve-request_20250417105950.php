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

    // Get request details using the new primary key 'id'
    $stmt = $conn->prepare("SELECT * FROM account_request WHERE id = ?");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $request = $stmt->get_result()->fetch_assoc();

    if (!$request) {
        // Rollback transaction if request not found
        $conn->rollback();
        throw new Exception("Request not found for ID: " . $request_id);
    }

    // Hash the temporary password
    $hashed_password = password_hash($temp_password, PASSWORD_DEFAULT);

    // Prepare statement to insert into users table
    $stmt_users = $conn->prepare("INSERT INTO users (username, useremail, password, first_name, last_name, role, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
    $status = 'active'; // Set the user status directly
    $stmt_users->bind_param("sssssss", 
        $request['username'],
        $request['usermail'],
        $hashed_password,
        $request['first_name'],
        $request['last_name'],
        $request['role'], // Use the role from the request
        $status
    );
    
    // Execute the insert into users
    if (!$stmt_users->execute()) {
        // Rollback transaction if insert fails
        $error_message = $stmt_users->error;
        $conn->rollback();
        throw new Exception("Failed to create user in users table: " . $error_message);
    }
    $stmt_users->close();

    // Delete request from account_request using the new primary key 'id'
    $stmt_delete = $conn->prepare("DELETE FROM account_request WHERE id = ?");
    $stmt_delete->bind_param("i", $request_id);
    
    if (!$stmt_delete->execute()) {
        // Rollback transaction if delete fails
        $error_message = $stmt_delete->error;
        $conn->rollback();
        throw new Exception("Failed to delete account request: " . $error_message);
    }
    $stmt_delete->close();

    // Commit transaction if all steps succeeded
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
