<?php
require_once '../includes/config.php';

header('Content-Type: application/json');

try {
    // Test database connection
    $pdo->query("SELECT 1");
    
    echo json_encode([
        'success' => true,
        'status' => 'operational',
        'timestamp' => date('Y-m-d H:i:s'),
        'php_version' => PHP_VERSION,
        'session_status' => session_status()
    ]);
} catch (Exception $e) {
    error_log("Status check failed: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'System status check failed',
        'details' => $e->getMessage()
    ]);
}
