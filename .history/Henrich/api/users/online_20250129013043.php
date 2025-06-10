<?php
require_once '../../includes/config.php';
require_once '../../includes/session.php';

header('Content-Type: application/json');

try {
    // Debug log for session
    error_log("Current user session: " . print_r($_SESSION, true));

    // Update current user's online status
    $stmt = $pdo->prepare("
        UPDATE users 
        SET is_online = TRUE, 
            last_online = CURRENT_TIMESTAMP 
        WHERE user_id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);

    // Get all online users except current user
    $stmt = $pdo->prepare("
        SELECT 
            user_id,
            username,
            role,
            last_online,
            is_online
        FROM users 
        WHERE is_online = TRUE 
        AND user_id != ? 
        AND role IN ('supervisor', 'ceo', 'admin')
        AND last_online >= NOW() - INTERVAL 5 MINUTE
    ");

    $stmt->execute([$_SESSION['user_id']]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Debug log for query results
    error_log("Found online users: " . print_r($users, true));

    echo json_encode([
        'success' => true,
        'users' => $users,
        'debug' => [
            'total_users' => count($users),
            'current_user' => [
                'id' => $_SESSION['user_id'],
                'role' => $_SESSION['role']
            ],
            'query_time' => date('Y-m-d H:i:s')
        ]
    ]);
} catch (Exception $e) {
    error_log("Error in online.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch online users',
        'debug_message' => $e->getMessage()
    ]);
}
