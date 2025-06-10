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
        