<?php
namespace Chat;

class MessageHandler {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function saveMessage($senderId, $receiverId, $message) {
        $stmt = $this->pdo->prepare("
            INSERT INTO chat_messages (sender_id, receiver_id, message, created_at)
            VALUES (?, ?, ?, CURRENT_TIMESTAMP)
        ");
        return $stmt->execute([$senderId, $receiverId, $message]);
    }

    public function getUnreadCount($userId) {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM chat_messages 
            WHERE receiver_id = ? AND is_read = FALSE
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
    }

    public function markAsRead($messageId) {
        $stmt = $this->pdo->prepare("
            UPDATE chat_messages SET is_read = TRUE 
            WHERE message_id = ?
        ");
        return $stmt->execute([$messageId]);
    }
}
