<?php
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/session.php';

header('Content-Type: application/json');

try {
    // Verify user is logged in
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Unauthorized');
    }

    // Get request data
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['receiver_id']) || !isset($data['message'])) {
        throw new Exception('Missing required fields');
    }

    $sender_id = $_SESSION['user_id'];
    $receiver_id = $data['receiver_id'];
    $message = trim($data['message']);

    if (empty($message)) {
        throw new Exception('Message cannot be empty');
    }

    // Insert message into database
    $stmt = $pdo->prepare("
        INSERT INTO messages (sender_id, receiver_id, message)
        VALUES (:sender_id, :receiver_id, :message)
    ");

    $stmt->execute([
        'sender_id' => $sender_id,
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
