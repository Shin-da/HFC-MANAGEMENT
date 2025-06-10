<?php
// Prevent any output before our JSON response
ob_start();


header('Content-Type: application/json');

try {
    // Enable error logging
    error_log("Chat send.php accessed");
    
    // Verify user is logged in
    if (!isset($_SESSION['user_id'])) {
        error_log("Unauthorized access attempt - no session user_id");
        throw new Exception('Unauthorized');
    }

    // Log raw input
    $raw_input = file_get_contents('php://input');
    error_log("Received raw input: " . $raw_input);

    // Get request data
    $data = json_decode($raw_input, true);
    
    if (!isset($data['receiver_id']) || !isset($data['message'])) {
        error_log("Missing fields in request: " . print_r($data, true));
        throw new Exception('Missing required fields');
    }

    $sender_id = (int)$_SESSION['user_id'];
    $receiver_id = (int)$data['receiver_id'];
    $message = trim($data['message']);

    if (empty($message)) {
        throw new Exception('Message cannot be empty');
    }

    error_log("Attempting to insert message: sender={$sender_id}, receiver={$receiver_id}, message={$message}");

    // Insert message into database with error checking
    $stmt = $pdo->prepare("
        INSERT INTO messages (sender_id, receiver_id, message, created_at)
        VALUES (:sender_id, :receiver_id, :message, NOW())
    ");

    $result = $stmt->execute([
        'sender_id' => $sender_id,
        'receiver_id' => $receiver_id,
        'message' => $message
    ]);

    if (!$result) {
        error_log("Database error: " . print_r($stmt->errorInfo(), true));
        throw new Exception('Failed to save message');
    }

    $message_id = $pdo->lastInsertId();
    error_log("Message inserted successfully with ID: " . $message_id);

    echo json_encode([
        'success' => true,
        'message_id' => $message_id,
        'timestamp' => date('Y-m-d H:i:s')
    ]);

} catch (PDOException $e) {
    error_log("PDO Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error occurred'
    ]);
} catch (Exception $e) {
    error_log("General Error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
