<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/session.php';

// Make connection available
global $conn;

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

try {
    // Use user_id consistently
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'error' => 'Not authenticated']);
        exit;
    }

    // Use prepared statement
    $query = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN type LIKE 'order_%' THEN 1 ELSE 0 END) as order_count,
                SUM(CASE WHEN type LIKE 'inventory_%' THEN 1 ELSE 0 END) as inventory_count
              FROM notifications 
              WHERE user_id = ? AND is_read = 0";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        throw new Exception('Failed to prepare query: ' . $conn->error);
    }

    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    
    // Log for debugging
    error_log("Notification count for user {$_SESSION['user_id']}: {$data['total']}");
    
    echo json_encode([
        'success' => true,
        'count' => [
            'total' => (int)$data['total'],
            'orders' => (int)$data['order_count'],
            'inventory' => (int)$data['inventory_count']
        ]
    ]);

} catch (Exception $e) {
    error_log("Error in get_notification_count.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

// Add this before closing to debug session and connection
error_log("Session data: " . print_r($_SESSION, true));
error_log("Connection status: " . ($conn->ping() ? 'connected' : 'disconnected'));
