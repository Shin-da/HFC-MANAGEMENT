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
    // Update last activity time
    $_SESSION['last_activity'] = time();
    
    // Update user's online status
    $stmt = $pdo->prepare("
        UPDATE users 
        SET is_online = TRUE,
            last_online = CURRENT_TIMESTAMP
        WHERE user_id = ?
    ");
    
    $stmt->execute([$_SESSION['user_id']]);
    
    // Get current online count
    $countStmt = $pdo->prepare("
        SELECT COUNT(*) as online_count
        FROM users
        WHERE is_online = TRUE 
        AND last_online >= NOW() - INTERVAL 2 MINUTE
        AND user_id != ?
    ");
    
    $countStmt->execute([$_SESSION['user_id']]);
    $count = $countStmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'onlineCount' => $count['online_count']
    ]);
} catch (Exception $e) {
    error_log("Heartbeat error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to update online status'
    ]);
}
