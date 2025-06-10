<?php
require_once '../../includes/config.php';
require_once '../../includes/session.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

try {
    // Mark user as offline
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
    echo json_encode(['success' => false, 'error' => 'Internal server error']);
}
