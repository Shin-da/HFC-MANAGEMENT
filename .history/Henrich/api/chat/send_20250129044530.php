<?php
// Ensure no output before headers
ob_start();

// Absolute path to includes directory
define('ROOT_PATH', realpath($_SERVER['DOCUMENT_ROOT'] . '/HFC MANAGEMENT/Henrich'));

// Check if includes directory exists
if (!file_exists(ROOT_PATH . '/includes')) {
    error_log("Includes directory not found at: " . ROOT_PATH . '/includes');
    http_response_code(500);
    die(json_encode(['success' => false, 'error' => 'Server configuration error']));
}

try {
    require_once ROOT_PATH . '/includes/config.php';
    require_once ROOT_PATH . '/includes/database.php';
    require_once ROOT_PATH . '/includes/session.php';

    // Set JSON header
    header('Content-Type: application/json; charset=utf-8');

    // Clear any previous output
    ob_clean();

    // Verify session and database
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Session expired. Please login again.');
    }

    if (!isset($pdo)) {
        throw new Exception('Database connection failed');
    }

    // Get and validate input
    $input = file_get_contents('php://input');
    if (!$input) {
        throw new Exception('No input received');
    }

    $data = json_decode($input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON format');
    }

    // Validate message data
    if (empty($data['receiver_id']) || !isset($data['message'])) {
        throw new Exception('Missing receiver_id or message');
    }

    // Insert message
    $stmt = $pdo->prepare("
        INSERT INTO messages (sender_id, receiver_id, message, created_at) 
        VALUES (:sender_id, :receiver_id, :message, NOW())
    ");

    $success = $stmt->execute([
        'sender_id' => $_SESSION['user_id'],
        'receiver_id' => $data['receiver_id'],
        'message' => $data['message']
    ]);

    if (!$success) {
        throw new Exception('Failed to save message');
    }

    // Send success response
    echo json_encode([
        'success' => true,
        'message_id' => $pdo->lastInsertId(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);

} catch (Exception $e) {
    error_log('Chat Error: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

ob_end_flush();
exit;
