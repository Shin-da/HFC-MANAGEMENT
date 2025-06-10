<?php
require_once '../../includes/config.php';
require_once '../../includes/session.php';

header('Content-Type: application/json');

if (!isset($_GET['user_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'User ID required']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            cm.*,
            u_sender.username as sender_name,
            u_sender.role as sender_role
        FROM chat_messages cm
        JOIN users u_sender ON cm.sender_id = u_sender.user_id
        WHERE (cm.sender_id = ? AND cm.receiver_id = ?)
           OR (cm.sender_id = ? AND cm.receiver_id = ?)
        ORDER BY cm.created_at DESC
        LIMIT 50
    ");
    
    $stmt->execute([
        $_SESSION['user_id'], $_GET['user_id'],