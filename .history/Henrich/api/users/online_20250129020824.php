<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../../includes/config.php';
require_once '../../includes/session.php';

header('Content-Type: application/json');

try {
    // Debug log current session and connection
    error_log("Session data: " . print_r($_SESSION, true));
    error_log("PDO connection status check");
    
    // Verify database connection
    if (!$pdo) {
        throw new Exception("Database connection not available");
    }

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
