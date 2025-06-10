<?php
require_once '../../includes/config.php';
require_once '../../includes/session.php';

header('Content-Type: application/json');

try {
    $userId = $_GET['user_id'] ?? null;
    if (!$userId) {
        throw new Exception("User ID required");
    }

    $stmt = $pdo->prepare("
        SELECT 
            user_id,
            username,
            role,
            is_online,
            last_online,
            first_name,
            last_name,
            department
        FROM users 
        WHERE user_id = ?
    ");
    
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception("User not found");
    }

    echo json_encode([
        'success' => true,
        'user' => $user
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
