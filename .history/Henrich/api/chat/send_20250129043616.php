<?php
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/session.php';

header('Content-Type: application/json');

try {
    // Enable error logging
    error_log("Chat send.php accessed");
    
    // Verify user is logged in
    if (!isset($_SESSION['user_id'])) {
        error_log("Unauthorized access attempt - no session user_id");
        throw new Exception('Unauthorized');
    }

    // Log raw input
    $raw_input = file_get_contents('php://input');
    error_log("Received raw input: " . $raw_input);

    // Get request data
    $data = json_decode($raw_input, true);
    
    if (!isset($data['receiver_id']) || !isset($data['message'])) {
        error_log("Missing fields in request: " . print_r($data, true));
        throw new Exception('Missing required fields');
    }

    $sender_id = (int)$_SESSION['user_id'];
    $receiver_id = (int)$data['receiver_id'];
    $message = trim($data['message']);

    if (empty($message)) {
        throw new Exception('Message cannot be empty');
    }
        'receiver_id' => $receiver_id,
        'message' => $message
    ]);

    echo json_encode([
        'success' => true,
        'message_id' => $pdo->lastInsertId()
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
