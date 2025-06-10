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

    // Check if user exists and is not the last admin
    $stmt = $pdo->prepare("SELECT role FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception('User not found');
    }

    // If user is admin, check if there are other admins
    if ($user['role'] === 'admin') {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = 'admin' AND user_id != ?");
        $stmt->execute([$userId]);
        $admin_count = $stmt->fetchColumn();

        if ($admin_count === 0) {
            throw new Exception('Cannot delete the last admin user');
        }
    }

    // Delete user
    $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);

    echo json_encode([
        'status' => 'success',
        'message' => 'User deleted successfully'
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
} 