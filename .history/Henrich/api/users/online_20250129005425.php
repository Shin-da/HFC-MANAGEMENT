<?php
require_once '../../includes/config.php';
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

    // Get online users based on role permissions
    $allowedRoles = ['supervisor', 'ceo', 'admin'];
    $currentRole = $_SESSION['role'];
    
    $sql = "
        SELECT 
            u.user_id,
            u.username,
            u.role,
            u.last_online,
            CASE 
                WHEN TIMESTAMPDIFF(MINUTE, u.last_online, CURRENT_TIMESTAMP) < 5 THEN 1
                ELSE 0
            END as is_active
        FROM users u
        WHERE u.user_id != ? 
        AND u.role IN ('" . implode("','", $allowedRoles) . "')
        AND u.last_online >= NOW() - INTERVAL 10 MINUTE
        ORDER BY u.last_online DESC, u.username ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['user_id']]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'users' => $users,
        'current_user' => [
            'user_id' => $_SESSION['user_id'],
            'role' => $_SESSION['role']
        ]
    ]);
} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch online users'
    ]);
}
