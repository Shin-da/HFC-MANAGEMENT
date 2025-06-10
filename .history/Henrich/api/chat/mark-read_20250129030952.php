<?php
require_once '../../includes/config.php';
require_once '../../includes/session.php';

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $senderId = $data['sender_id'] ?? null;

    if (!$senderId) {
        throw new Exception("Sender ID required");
    }

    $stmt = $pdo->prepare("
        UPDATE chat_messages 
        SET is_read = TRUE 
        WHERE receiver_id = ? AND sender_id = ? AND is_read = FALSE
    ");
    
    $stmt->execute([$_SESSION['user_id'], $senderId]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
