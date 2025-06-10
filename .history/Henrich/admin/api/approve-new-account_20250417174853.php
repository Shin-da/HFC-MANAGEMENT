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
error_log("Received NEW ACCOUNT request: " . print_r($_POST, true));

require_once '../../includes/config.php'; // Correct path: Go up two levels

try {
    if (empty($_POST['request_id'])) { // Expecting the PK from account_requests
        throw new Exception("Request ID is missing");
    }

    $request_id = intval($_POST['request_id']);
    error_log("Processing NEW ACCOUNT request ID: " . $request_id);

    $temp_password = bin2hex(random_bytes(8));

    // Start transaction using PDO
    $GLOBALS['pdo']->beginTransaction();

    // Get request details from account_requests (plural)
    // Assuming PK is 'request_id', adjust if different (e.g., 'id')
    $stmt = $GLOBALS['pdo']->prepare("SELECT * FROM account_requests WHERE request_id = ? AND status = 'pending'"); 
    $stmt->execute([$request_id]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$request) {
        $GLOBALS['pdo']->rollBack(); 
        throw new Exception("Pending request not found for ID: " . $request_id);
    }

    // Check if user already exists in users table (by email or username if applicable)
    // Assuming username is not collected by the form used for account_requests based on INSERTs seen
    $stmt_check = $GLOBALS['pdo']->prepare("SELECT COUNT(*) FROM users WHERE useremail = ?");
    $stmt_check->execute([$request['email']]);
    if ($stmt_check->fetchColumn() > 0) {
         $GLOBALS['pdo']->rollBack(); 
        throw new Exception("User with this email already exists.");
    }

    // Hash the temporary password
    $hashed_password = password_hash($temp_password, PASSWORD_DEFAULT);

    // Prepare statement to insert into users table
    // Map columns from account_requests to users
    // Generate a simple username if not provided, e.g., from email prefix
    $username = $request['username'] ?? explode('@', $request['email'])[0] . rand(10,99); 
    $useremail = $request['email'];
    $first_name = $request['firstname'];
    $last_name = $request['lastname'];

    // Determine role based on position or department
    $requested_position = $request['position'] ?? '';
    $requested_department = $request['department'] ?? '';
    $role = 'supervisor'; // Default role

    if (stripos($requested_position, 'CEO') !== false) {
        $role = 'ceo';
    } elseif (stripos($requested_department, 'Admin') !== false) {
        $role = 'admin';
    }
    // Add more rules here if needed based on specific positions/departments
    
    $status = 'active'; 

    $stmt_users = $GLOBALS['pdo']->prepare("INSERT INTO users (username, useremail, password, first_name, last_name, role, status, department, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
    
    if (!$stmt_users->execute([$username, $useremail, $hashed_password, $first_name, $last_name, $role, $status, $request['department']])) {
        $error_info = $stmt_users->errorInfo();
        $GLOBALS['pdo']->rollBack();
        throw new Exception("Failed to create user in users table: " . $error_info[2]);
    }
    $new_user_id = $GLOBALS['pdo']->lastInsertId(); // Get the ID of the newly created user

    // Update the status of the request in account_requests to 'approved'
    // Option 1: Update status
    $stmt_update_req = $GLOBALS['pdo']->prepare("UPDATE account_requests SET status = 'approved', processed_date = NOW(), processed_by = ? WHERE request_id = ?");
    $admin_user_id = $_SESSION['user_id'] ?? null; // Assuming admin user ID is in session
    if (!$stmt_update_req->execute([$admin_user_id, $request_id])) {
         $error_info = $stmt_update_req->errorInfo();
         $GLOBALS['pdo']->rollBack();
         throw new Exception("Failed to update account request status: " . $error_info[2]);
    }
    
    /* Option 2: Delete request
    $stmt_delete = $GLOBALS['pdo']->prepare("DELETE FROM account_requests WHERE request_id = ?");
    if (!$stmt_delete->execute([$request_id])) {
        $error_info = $stmt_delete->errorInfo();
        $GLOBALS['pdo']->rollBack();
        throw new Exception("Failed to delete account request: " . $error_info[2]);
    }
    */

    // Commit transaction if all steps succeeded
    $GLOBALS['pdo']->commit();

    // Clear any output before sending JSON
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Send success response including temporary password
    echo json_encode([
        'status' => 'success',
        'message' => 'New account created successfully for ' . $first_name . ' ' . $last_name . '.',
        'new_user_id' => $new_user_id,
        'temp_password' => $temp_password, // Important to show this to the admin!
        'username' => $username,
        'email' => $useremail
    ]);

} catch (Exception $e) {
    // Ensure transaction is rolled back in case of error
    if ($GLOBALS['pdo']->inTransaction()) {
        $GLOBALS['pdo']->rollBack();
    }
    
    error_log("Error in approve-new-account.php: " . $e->getMessage());
    
    // Clear any output before sending error JSON
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Send error response
    http_response_code(400); // Set appropriate error code
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

exit; 