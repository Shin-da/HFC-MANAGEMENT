<?php
require_once '../../includes/config.php';
require_once '../access_control.php';

header('Content-Type: application/json');

try {
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['userId'])) {
        throw new Exception('User ID is required');
    }

    $userId = (int)$data['userId'];

    // Generate a random password
    $new_password = bin2hex(random_bytes(4)); // 8 characters
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Update user's password
    $stmt = $pdo->prepare("UPDATE users SET password = ?, last_password_change = CURRENT_TIMESTAMP WHERE user_id = ?");
    $stmt->execute([$hashed_password, $userId]);

    if ($stmt->rowCount() === 0) {
        throw new Exception('User not found');
    }

    // TODO: Send email to user with new password
    // For now, we'll just return the password in the response
    echo json_encode([
        'status' => 'success',
        'message' => 'Password reset successfully',
        'new_password' => $new_password // In production, remove this and send via email
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
} 