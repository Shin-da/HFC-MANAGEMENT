<?php
require_once '../../includes/config.php';
require_once '../access_control.php';

header('Content-Type: application/json');

try {
    if (!isset($_FILES['profile_picture'])) {
        throw new Exception('No file uploaded');
    }

    $file = $_FILES['profile_picture'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    // Validate file type
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Invalid file type. Only JPG, PNG and GIF are allowed.');
    }

    // Validate file size
    if ($file['size'] > $maxSize) {
        throw new Exception('File too large. Maximum size is 5MB.');
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $uploadPath = '../../uploads/profile_pictures/' . $filename;

    // Create directory if it doesn't exist
    if (!file_exists('../../uploads/profile_pictures')) {
        mkdir('../../uploads/profile_pictures', 0777, true);
    }

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        throw new Exception('Failed to upload file');
    }

    // Update database
    $stmt = $GLOBALS['pdo']->prepare("
        UPDATE users 
        SET profile_picture = :profile_picture
        WHERE user_id = :user_id
    ");

    $stmt->execute([
        ':profile_picture' => $filename,
        ':user_id' => $_SESSION['user_id']
    ]);

    // Log the action
    logAdminAction("Updated profile picture");

    echo json_encode([
        'success' => true,
        'message' => 'Profile picture updated successfully',
        'filename' => $filename
    ]);

} catch (Exception $e) {
    error_log("Error in update-profile-picture.php: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

function logAdminAction($action) {
    $stmt = $GLOBALS['pdo']->prepare("
        INSERT INTO admin_logs (admin_id, action, ip_address)
        VALUES (:admin_id, :action, :ip)
    ");

    $stmt->execute([
        ':admin_id' => $_SESSION['user_id'],
        ':action' => $action,
        ':ip' => $_SERVER['REMOTE_ADDR']
    ]);
} 