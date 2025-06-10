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

    $stmt = $pdo->prepare("
        SELECT 
            user_id,
            username,
            role,
            first_name,
            last_name,
            department,
            is_online,
            last_online,
            CASE 
                WHEN last_online >= NOW() - INTERVAL 5 MINUTE THEN 1
                ELSE 0
            END as is_active
        FROM users 
        WHERE user_id != ? 
        AND role IN ('supervisor', 'ceo', 'admin')
        AND status = 'active'
        AND (is_online = TRUE OR last_online >= NOW() - INTERVAL 30 MINUTE)
        ORDER BY is_online DESC, last_online DESC
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
        'debug' => [
            'session_id' => session_id(),
            'user_id' => $_SESSION['user_id'] ?? null,
            'timestamp' => date('Y-m-d H:i:s')
        ]
    ]);
} catch (Exception $e) {
    error_log("Error in online.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Server error occurred',
        'debug_message' => $e->getMessage()
    ]);
}
