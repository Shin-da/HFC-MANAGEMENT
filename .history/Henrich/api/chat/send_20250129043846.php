<?php
// Prevent any output before our JSON response
ob_start();

require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/session.php';

// Ensure we're sending JSON response
header('Content-Type: application/json');

try {
    // Clear any previous output
    ob_clean();

    // Check for database connection
    if (!isset($pdo)) {
        throw new Exception('Database connection not established');
    }

    // Verify user is logged in
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Unauthorized - Please log in');
    }

    // Get and validate input
    $input = file_get_contents('php://input');
    if (!$input) {
        throw new Exception('No input received');
    }

    $data = json_decode($input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON input');
    }

    // Validate required fields
    if (empty($data['receiver_id']) || !isset($data['message'])) {
        throw new Exception('Missing receiver_id or message');
    }

    $sender_id = (int)$_SESSION['user_id'];
    $receiver_id = (int)$data['receiver_id'];
    $message = trim($data['message']);

    if (empty($message)) {
        throw new Exception('Message cannot be empty');
    }

    // Begin transaction
    $pdo->beginTransaction();

    try {
        $stmt = $pdo->prepare("
            INSERT INTO messages (sender_id, receiver_id, message, created_at)
            VALUES (:sender_id, :receiver_id, :message, NOW())
        ");

        $success = $stmt->execute([
            'sender_id' => $sender_id,
            'receiver_id' => $receiver_id,
            'message' => $message
        ]);

        if (!$success) {
            throw new Exception('Failed to save message');
        }

        $message_id = $pdo->lastInsertId();
        $pdo->commit();

        // Success response
        echo json_encode([
            'success' => true,
            'message_id' => $message_id,
            'timestamp' => date('Y-m-d H:i:s')
        ]);

    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }

} catch (Exception $e) {
    // Clear any output that might have been generated
    ob_clean();
    
    error_log('Chat Error: ' . $e->getMessage());
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

// Flush and end output buffer
ob_end_flush();
