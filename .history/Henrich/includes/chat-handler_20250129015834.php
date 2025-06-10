<?php
require_once 'config.php';
require_once 'session.php';

class ChatHandler {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function sendMessage($senderId, $receiverId, $message, $attachmentUrl = null) {
        $stmt = $this->pdo->prepare("
            INSERT INTO chat_messages (sender_id, receiver_id, message, attachment_url)
            VALUES (:sender_id, :receiver_id, :message, :attachment_url)
        ");
        
        $stmt->execute([
            ':sender_id' => $senderId,
            ':receiver_id' => $receiverId,
            ':message' => $message,
            ':attachment_url' => $attachmentUrl
        ]);

        $messageId = $this->pdo->lastInsertId();
        $this->createNotification($receiverId, $messageId);
        
        return $messageId;
    }

    public function getMessages($userId, $otherId, $limit = 50) {
        $stmt = $this->pdo->prepare("
            SELECT m.*, 
                   s.username as sender_name,
                   r.username as receiver_name
            FROM chat_messages m
            JOIN users s ON m.sender_id = s.user_id
            JOIN users r ON m.receiver_id = r.user_id
            WHERE (m.sender_id = :user1 AND m.receiver_id = :user2)
               OR (m.sender_id = :user2 AND m.receiver_id = :user1)
            ORDER BY m.created_at DESC
            LIMIT :limit
        ");
        
        $stmt->bindValue(':user1', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':user2', $otherId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAvailableUsers($currentUserId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    u.user_id,
                    u.username,
                    u.role,
                    u.is_online,
                    u.last_online,
                    (SELECT COUNT(*) 
                     FROM chat_messages 
                     WHERE receiver_id = ? 
                     AND sender_id = u.user_id 
                     AND is_read = FALSE) as unread_count
                FROM users u
                WHERE u.user_id != ?
                AND u.role IN ('supervisor', 'ceo', 'admin')
                AND u.is_active = TRUE
                ORDER BY u.is_online DESC, u.last_online DESC, u.username ASC
            ");
            
            $stmt->execute([$currentUserId, $currentUserId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error getting available users: " . $e->getMessage());
            return [];
        }
    }

    private function createNotification($userId, $messageId) {
        $stmt = $this->pdo->prepare("
            INSERT INTO chat_notifications (user_id, message_id)
            VALUES (:user_id, :message_id)
        ");
        
        $stmt->execute([
            ':user_id' => $userId,
            ':message_id' => $messageId
        ]);
    }
}
