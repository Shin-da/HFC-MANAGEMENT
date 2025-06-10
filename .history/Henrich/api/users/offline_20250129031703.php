<?php
require_once '../../includes/config.php';
require_once '../../includes/session.php';

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception("Not authenticated");
    }

    $stmt = $pdo->prepare("
        UPDATE users 
        SET is_online = FALSE,
            last_online = CURRENT_TIMESTAMP
        WHERE user_id = ?
    ");
    
    $stmt->execute([$_SESSION['user_id']]);
    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    error_log("Error marking user offline: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
