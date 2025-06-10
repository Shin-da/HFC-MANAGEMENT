<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../../includes/config.php';
require_once '../../includes/session.php';

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception("User not authenticated");
    }

    // Update current user's online status first
    $updateStmt = $pdo->prepare("
        UPDATE users 
        SET is_online = TRUE, 
            last_online = CURRENT_TIMESTAMP 
        WHERE user_id = ?
    ");
    $updateStmt->execute([$_SESSION['user_id']]);

    // Fetch all users except current user
    $stmt = $pdo->prepare("
        SELECT 
            u.user_id,
            u.username,
            u.role,
            u.first_name,
            u.last_name,
            u.department,
            u.is_online,
            u.last_online,
            CASE 
                WHEN u.last_online >= NOW() - INTERVAL 2 MINUTE THEN true 
                ELSE false 
            END as is_active
        FROM users u
        WHERE u.user_id != ? 
        AND u.role IN ('supervisor', 'ceo', 'admin')
        AND u.status = 'active'
        ORDER BY 
            u.is_online DESC,
            u.last_online DESC,
            u.username ASC
    ");

    if (!$stmt) {
        throw new Exception("Failed to prepare statement");
    }
    
    $stmt->execute([$_SESSION['user_id']]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    error_log("Users found: " . count($users));

    echo json_encode([
        'success' => true,
        'users' => $users,
        'currentUser' => [
            'id' => $_SESSION['user_id'],
            'role' => $_SESSION['role']
        ]
    ]);
} catch (Exception $e) {
    error_log("Error in online.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch online users'
    ]);
}
