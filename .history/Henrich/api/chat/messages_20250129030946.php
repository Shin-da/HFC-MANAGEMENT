<?php
require_once '../../includes/config.php';
require_once '../../includes/session.php';

header('Content-Type: application/json');

try {
    $userId = $_GET['user_id'] ?? null;
    if (!$userId) {
        throw new Exception("User ID required");
    }

    $stmt = $pdo->prepare("
        SELECT 
            m.*,
            s.username as sender_name,
            s.role as sender_role
        FROM chat_messages m
        JOIN users s ON m.sender_id = s.user_id
        WHERE (m.sender_id = ? AND m.receiver_id = ?)
           OR (m.sender_id = ? AND m.receiver_id = ?)
        ORDER BY m.created_at ASC
        LIMIT 50
    ");

    $stmt->execute([
        $_SESSION['user_id'], $userId,
        $userId, $_SESSION['user_id']
    ]);

    // Mark messages as read
    $updateStmt = $pdo->prepare("
        UPDATE chat_messages 
        SET is_read = TRUE 
        WHERE receiver_id = ? AND sender_id = ? AND is_read = FALSE
    ");
    $updateStmt->execute([$_SESSION['user_id'], $userId]);

    echo json_encode([
        'success' => true,
        'messages' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
