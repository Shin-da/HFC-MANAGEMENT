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
        