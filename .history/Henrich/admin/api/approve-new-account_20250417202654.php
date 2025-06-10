<?php
/**
 * API Endpoint: Approve Account Request
 * 
 * Processes an account request approval, creates a new user, and sends notification
 */

// Start session for authentication
session_start();

// Include required files
require_once '../../includes/config.php';
require_once '../../includes/functions.php';

// Check if user is authenticated and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode([
        'success' => false,
        'error' => 'Unauthorized access'
    ]);
    exit;
}

// Set content type
header('Content-Type: application/json');

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid request method'
    ]);
    exit;
}

// Validate request ID
if (!isset($_POST['request_id']) || !is_numeric($_POST['request_id'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid request ID'
    ]);
    exit;
}

$requestId = (int) $_POST['request_id'];

try {
    // Begin transaction
    $GLOBALS['pdo']->beginTransaction();
    
    // First, fetch the request details
    $stmt = $GLOBALS['pdo']->prepare("
        SELECT * 
        FROM account_requests 
        WHERE request_id = ? AND status = 'pending'
    ");
    $stmt->execute([$requestId]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$request) {
        throw new Exception('Request not found or already processed');
    }
    
    // Check if email already exists in users table
    $stmt = $GLOBALS['pdo']->prepare("SELECT COUNT(*) FROM users WHERE useremail = ?");
    $stmt->execute([$request['email']]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception('A user with this email already exists');
    }
    
    // Generate a random password
    $password = generate_random_password();
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Determine the role based on position/department
    $role = 'supervisor'; // Default to supervisor
    if (stripos($request['position'], 'admin') !== false) {
        $role = 'admin';
    }
    
    // Create new user account
    $stmt = $GLOBALS['pdo']->prepare("
        INSERT INTO users (
            username, 
            useremail, 
            password, 
            first_name, 
            last_name, 
            role, 
            department,
            status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, 'active')
    ");
    
    // Generate a username (email prefix)
    $username = strtolower(substr($request['firstname'], 0, 1) . $request['lastname']);
    $username = preg_replace('/[^a-z0-9]/', '', $username); // Remove special characters
    
    // Execute statement
    $stmt->execute([
        $username,
        $request['email'],
        $hashedPassword,
        $request['firstname'],
        $request['lastname'],
        $role,
        $request['department']
    ]);
    
    $userId = $GLOBALS['pdo']->lastInsertId();
    
    // Update request status
    $stmt = $GLOBALS['pdo']->prepare("
        UPDATE account_requests 
        SET status = 'approved', processed_date = NOW(), processed_by = ?
        WHERE request_id = ?
    ");
    $stmt->execute([$_SESSION['user_id'], $requestId]);
    
    // Log activity
    logActivity(
        $_SESSION['user_id'], 
        'approved_account', 
        "Approved account request for {$request['firstname']} {$request['lastname']} ({$request['email']})"
    );
    
    // Try to send email notification
    $emailSent = false;
    try {
        $subject = "Your HFC Management System Account Request Approved";
        $message = "
            <html>
            <head>
                <title>Account Request Approved</title>
            </head>
            <body>
                <p>Hello {$request['firstname']} {$request['lastname']},</p>
                <p>Your request for an account in the HFC Management System has been approved.</p>
                <p>Here are your login credentials:</p>
                <ul>
                    <li><strong>Username:</strong> {$username}</li>
                    <li><strong>Password:</strong> {$password}</li>
                </ul>
                <p>Please log in at <a href='http://localhost/HFC%20MANAGEMENT/Henrich/'>HFC Management System</a> and change your password as soon as possible.</p>
                <p>Regards,</p>
                <p>HFC Management System Admin</p>
            </body>
            </html>
        ";
        
        $emailSent = send_notification($request['email'], $subject, $message);
    } catch (Exception $e) {
        error_log("Error sending approval email: " . $e->getMessage());
    }
    
    // Commit transaction
    $GLOBALS['pdo']->commit();
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Account request approved successfully',
        'email_sent' => $emailSent,
        'user_id' => $userId
    ]);
    
} catch (PDOException $e) {
    // Rollback transaction
    if ($GLOBALS['pdo']->inTransaction()) {
        $GLOBALS['pdo']->rollBack();
    }
    
    // Log the error
    error_log("Database error in approve-new-account.php: " . $e->getMessage());
    
    // Return error response
    echo json_encode([
        'success' => false,
        'error' => 'Database error occurred: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    // Rollback transaction
    if ($GLOBALS['pdo']->inTransaction()) {
        $GLOBALS['pdo']->rollBack();
    }
    
    // Log the error
    error_log("Error in approve-new-account.php: " . $e->getMessage());
    
    // Return error response
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}