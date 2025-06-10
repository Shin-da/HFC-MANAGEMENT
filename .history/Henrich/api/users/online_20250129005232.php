<?php
require_once '../../includes/config.php';
require_once '../../includes/session.php';

header('Content-Type: application/json');

try {
    // Update current user's online status
    $stmt = $pdo->prepare("
        UPDATE users 
        SET is_online = TRUE, last_online = CURRENT_TIMESTAMP 
        WHERE user_id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);

    // Get online users
    $stmt = $pdo->prepare("
        SELECT user_id, username, role, last_online 
        FROM users 
        WHERE is_online = TRUE 
        AND user_id != ? 
        AND role IN ('supervisor', 'ceo', 'admin')
        AND last_online >= NOW() - INTERVAL 5 MINUTE
    ");
    $stmt->execute([$_SESSION['user_id']]);
    
    echo json_encode([
        'success' => true,
        'users' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch online users'
    ]);
}
