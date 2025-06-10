<?php
require_once '../../includes/config.php';
require_once '../../includes/session.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->prepare("
        SELECT 
            u.user_id,
            u.username,
            u.is_online,
            m.message as last_message,
            m.created_at as last_message_time,
            COUNT(CASE WHEN m2.is_read = FALSE AND m2.receiver_id = ? THEN 1 END) as unread
        FROM users u
        LEFT JOIN (
            SELECT 
                CASE 
                    WHEN sender_id = ? THEN receiver_id 
                    ELSE sender_id 
                END as other_user,
                message,
                created_at,
                ROW_NUMBER() OVER (PARTITION BY 
                    CASE 
                        WHEN sender_id = ? THEN receiver_id 
                        ELSE sender_id 
                    END 
                    ORDER BY created_at DESC
                ) as rn
            FROM chat_messages 
            WHERE sender_id = ? OR receiver_id = ?
        ) m ON u.user_id = m.other_user AND m.rn = 1
        LEFT JOIN chat_messages m2 ON m2.sender_id = u.user_id AND m2.receiver_id = ?
        WHERE u.user_id != ? AND u.role IN ('supervisor', 'ceo', 'admin')
        GROUP BY u.user_id
        HAVING last_message IS NOT NULL
        ORDER BY last_message_time DESC
        LIMIT 20
    ");

    $userId = $_SESSION['user_id'];
    $stmt->execute([$userId, $userId, $userId, $userId, $userId, $userId, $userId]);

    echo json_encode([
        'success' => true,
        'chats' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);

} catch (Exception $e) {
    error_log("Error fetching recent chats: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Failed to load recent chats'
    ]);
}
