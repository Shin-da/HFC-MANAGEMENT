<?php
/**
 * API Endpoint: Get Account Request Details
 * 
 * Fetches details for a specific account request by ID
 */

// Start session for authentication
session_start();

// Include required files
require_once '../../includes/config.php';
require_once '../../includes/functions.php';

// Check if user is authenticated and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized access'
    ]);
    exit;
}

// Validate request ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request ID'
    ]);
    exit;
}

$requestId = (int) $_GET['id'];

try {
    // Check if the account_requests table exists
    $tableCheck = $GLOBALS['pdo']->query("SHOW TABLES LIKE 'account_requests'");
    if ($tableCheck->rowCount() == 0) {
        throw new Exception("The account_requests table does not exist");
    }
    
    // Fetch the request details
    $stmt = $GLOBALS['pdo']->prepare("
        SELECT * 
        FROM account_requests 
        WHERE request_id = ?
    ");
    $stmt->execute([$requestId]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$request) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Request not found'
        ]);
        exit;
    }
    
    // Return the request details
    echo json_encode([
        'status' => 'success',
        'request' => $request
    ]);
    
} catch (PDOException $e) {
    // Log the error
    error_log("Database error in get-request.php: " . $e->getMessage());
    
    // Return error response
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error occurred'
    ]);
} catch (Exception $e) {
    // Log the error
    error_log("Error in get-request.php: " . $e->getMessage());
    
    // Return error response
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
} 