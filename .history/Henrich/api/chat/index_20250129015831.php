<?php
require_once '../../includes/config.php';
require_once '../../includes/session.php';
require_once '../../includes/chat-handler.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$chatHandler = new ChatHandler($pdo);

try {
    switch ($action) {
        case 'get-users':
            $users = $chatHandler->getAvailableUsers($_SESSION['user_id']);
            echo json_encode([
                'success' => true,
                'users' => $users
            ]);
            break;
            
        case 'get-messages':
            $otherId = $_GET['user_id'] ?? null;
            if (!$otherId) {
                echo json_encode(['success' => false, 'error' => 'User ID required']);
                break;
            }
            $messages = $chatHandler->getMessages($_SESSION['user_id'], $otherId);
            echo json_encode(['success' => true, 'messages' => $messages]);
            break;
            
        case 'send':
            $data = json_decode(file_get_contents('php://input'), true);
            $result = $chatHandler->sendMessage(
                $_SESSION['user_id'],
                $data['receiver_id'],
                $data['message']
            );
            echo json_encode(['success' => true, 'message_id' => $result]);
            break;
            
        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    error_log("Chat API error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Operation failed',
        'debug_message' => $e->getMessage()
    ]);
}
