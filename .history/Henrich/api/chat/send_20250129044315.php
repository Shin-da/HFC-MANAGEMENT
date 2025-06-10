<?php
// Debugging setup
ini_set('display_errors', 1);
error_reporting(E_ALL);
error_log("Chat send.php accessed at " . date('Y-m-d H:i:s'));

// Start clean output
ob_end_clean();
ob_start();

require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/session.php';

// Set proper headers
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

try {
    // Log request data
    error_log("POST data: " . file_get_contents('php://input'));
    error_log("Session user_id: " . ($_SESSION['user_id'] ?? 'not set'));

    // Validate session
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Not authenticated');
    }

    // Parse and validate input
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON input: ' . json_last_error_msg());
    }

    if (empty($data['receiver_id']) || !isset($data['message'])) {
        throw new Exception('Missing required fields');
    }

    // Database operation
    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    
    if (!$stmt->execute([$_SESSION['user_id'], $data['receiver_id'], $data['message']])) {
        throw new Exception('Database error: ' . implode(' ', $stmt->errorInfo()));
    }

    // Success response
    $response = [
        'success' => true,
        'message_id' => $pdo->lastInsertId(),
        'timestamp' => date('Y-m-d H:i:s')
    ];

    error_log("Sending success response: " . json_encode($response));
    echo json_encode($response);

} catch (Exception $e) {
    error_log("Chat error: " . $e->getMessage());
    
    // Error response
    $response = [
        'success' => false,
        'error' => $e->getMessage()
    ];

    http_response_code(400);
    echo json_encode($response);
}

// Ensure output is sent
ob_end_flush();
exit;
