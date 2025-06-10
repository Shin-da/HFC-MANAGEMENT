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
