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
error_log("Received REJECT ACCOUNT request: " . print_r($_POST, true));

require_once '../../includes/config.php'; // Correct path: Go up two levels
require_once '../../includes/session.php'; // Need session for admin user ID
require_once '../../includes/functions.php'; // Required for email functions

try {
    // Ensure admin is logged in (basic check)
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'ceo'])) {
         throw new Exception("Unauthorized access");
    }

    if (empty($_POST['request_id'])) { // Expecting the PK from account_requests
        throw new Exception("Request ID is missing");
    }

    $request_id = intval($_POST['request_id']);
    $admin_user_id = $_SESSION['user_id'];
    $rejection_reason = isset($_POST['reason']) ? trim($_POST['reason']) : 'No reason provided';
    
    error_log("Processing REJECT ACCOUNT request ID: " . $request_id . " by Admin ID: " . $admin_user_id . " with reason: " . $rejection_reason);

    // Start transaction using PDO
    $GLOBALS['pdo']->beginTransaction();
    
    // First, fetch the request details to use in email notification
    $stmt_fetch_req = $GLOBALS['pdo']->prepare("
        SELECT * FROM account_requests 
        WHERE request_id = ? AND status = 'pending'
    ");
    $stmt_fetch_req->execute([$request_id]);
    $request = $stmt_fetch_req->fetch(PDO::FETCH_ASSOC);
    
    if (!$request) {
        throw new Exception("Request not found or already processed");
    }

    // Prepare statement to update account_requests status to 'rejected'
    $stmt_update_req = $GLOBALS['pdo']->prepare(
        "UPDATE account_requests 
         SET status = 'rejected', 
             processed_date = NOW(), 
             processed_by = ?,
             rejection_reason = ?
         WHERE request_id = ? AND status = 'pending'" 
         // Added status='pending' check to avoid accidental re-processing
    );
    
    if (!$stmt_update_req->execute([$admin_user_id, $rejection_reason, $request_id])) {
        $error_info = $stmt_update_req->errorInfo();
        $GLOBALS['pdo']->rollBack();
        throw new Exception("Failed to update account request status to rejected: " . $error_info[2]);
    }

    // Check if any row was actually updated
    if ($stmt_update_req->rowCount() === 0) {
        $GLOBALS['pdo']->rollBack();
        // Check if the request existed but wasn't pending
        $stmt_check = $GLOBALS['pdo']->prepare("SELECT status FROM account_requests WHERE request_id = ?");
        $stmt_check->execute([$request_id]);
        $current_status = $stmt_check->fetchColumn();
        if ($current_status) {
            throw new Exception("Request ID: " . $request_id . " was not pending (current status: " . $current_status . "). No action taken.");
        } else {
            throw new Exception("Request ID: " . $request_id . " not found.");
        }
    }
    
    // Log activity
    logActivity(
        $_SESSION['user_id'], 
        'rejected_account', 
        "Rejected account request for {$request['firstname']} {$request['lastname']} ({$request['email']})"
    );

    // Try to send email notification
    $emailSent = false;
    try {
        $subject = "Your HFC Management System Account Request Status";
        $message = "
            <html>
            <head>
                <title>Account Request Status Update</title>
            </head>
            <body>
                <p>Hello {$request['firstname']} {$request['lastname']},</p>
                <p>We regret to inform you that your request for an account in the HFC Management System has been declined.</p>
                <p><strong>Reason for rejection:</strong> {$rejection_reason}</p>
                <p>If you believe this was an error or if you have questions, please contact the system administrator.</p>
                <p>Regards,</p>
                <p>HFC Management System Admin</p>
            </body>
            </html>
        ";
        
        $emailSent = send_notification($request['email'], $subject, $message);
        error_log("Email notification sent: " . ($emailSent ? "Yes" : "No"));
    } catch (Exception $e) {
        error_log("Error sending rejection email: " . $e->getMessage());
        // Continue processing even if email fails
    }

    // Commit transaction if all steps succeeded
    $GLOBALS['pdo']->commit();

    // Clear any output before sending JSON
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Send success response
    echo json_encode([
        'status' => 'success',
        'message' => 'Account request #' . $request_id . ' rejected successfully.',
        'email_sent' => $emailSent
    ]);

} catch (Exception $e) {
    // Ensure transaction is rolled back in case of error
    if (isset($GLOBALS['pdo']) && $GLOBALS['pdo']->inTransaction()) {
        $GLOBALS['pdo']->rollBack();
    }
    
    error_log("Error in reject-new-account.php: " . $e->getMessage());
    
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
?> 