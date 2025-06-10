<?php
require_once '../../includes/config.php';  // Include config first
require_once '../../includes/session.php';

header('Content-Type: application/json');

try {
    // Update current user's online status
    $stmt = $pdo->prepare("
        UPDATE users 
        SET is_online = TRUE, 
            last_online = CURRENT_TIMESTAMP 
        WHERE user_id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);

    // Simplified query to get all online users from allowed roles
    $sql = "
        SELECT 
            u.user_id,
            u.username,
            u.role,
            u.last_online,
            u.is_online,
            CASE 
                WHEN u.last_online >= NOW() - INTERVAL 5 MINUTE THEN true 
                ELSE false 
            END as is_active
        FROM users u
        WHERE u.user_id != ?
        AND u.role IN ('supervisor', 'ceo', 'admin')
        ORDER BY 
            u.is_online DESC,
            u.last_online DESC,
            u.username ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['user_id']]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Debug information
    error_log("Found " . count($users) . " online users");
    error_log("Current user: " . $_SESSION['user_id'] . ", Role: " . $_SESSION['role']);

    echo json_encode([
        'success' => true,
        'users' => $users,
        'debug' => [
            'total_users' => count($users),
            'current_user' => [
                'id' => $_SESSION['user_id'],
                'role' => $_SESSION['role']
            ]
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
