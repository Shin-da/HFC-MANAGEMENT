<?php
// Disable error display in production
ini_set('display_errors', 0);
error_reporting(0);

// Start output buffering
ob_start();

require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/session.php';

// Set JSON headers
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');

// Clean any previous output
ob_clean();

try {
    // Verify database connection
    if (!$pdo) {
        throw new Exception('Database connection failed');
    }

    // Get JSON input
    $jsonInput = file_get_contents('php://input');
    error_log("Received input: " . $jsonInput); // Debug log

    $data = json_decode($jsonInput, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON: ' . json_last_error_msg());
    }

    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User not authenticated');
    }

    if (empty($data['receiver_id']) || !isset($data['message'])) {
        throw new Exception('Missing required fields');
    }

    $stmt = $pdo->prepare("
        INSERT INTO messages (sender_id, receiver_id, message)
        VALUES (:sender_id, :receiver_id, :message)
    ");

    $result = $stmt->execute([
        'sender_id' => $_SESSION['user_id'],
        'receiver_id' => $data['receiver_id'],
        'message' => $data['message']
    ]);

    if (!$result) {
        throw new Exception('Failed to save message');
    }

    $response = [
        'success' => true,
        'message_id' => $pdo->lastInsertId(),
        'timestamp' => date('Y-m-d H:i:s')
    ];

    echo json_encode($response);
    exit;

} catch (Exception $e) {
    error_log("Chat error: " . $e->getMessage());
    
    $error_response = [
        'success' => false,
        'error' => $e->getMessage()
    ];
    
    http_response_code(400);
    echo json_encode($error_response);
    exit;
}
