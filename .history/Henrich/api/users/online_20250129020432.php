<?php
require_once '../../includes/config.php';
require_once '../../includes/session.php';

header('Content-Type: application/json');

try {
    // Debug session
    error_log("Session user_id: " . $_SESSION['user_id']);

    $stmt = $pdo->prepare("
        SELECT 
            user_id,
            username,
            role,
            is_online,
            last_online
        FROM users 
        WHERE user_id != ? 
        AND role IN ('supervisor', 'ceo', 'admin')
    ");
    
    $stmt->execute([$_SESSION['user_id']]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    error_log("Found " . count($users) . " users");
    
    echo json_encode([
        'success' => true,
        'users' => $users,
        'currentUser' => $_SESSION['user_id']
    ]);
} catch (Exception $e) {
    error_log("Error in online.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error'
    ]);
}
