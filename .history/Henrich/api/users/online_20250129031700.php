<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../../includes/config.php';
require_once '../../includes/session.php';

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception("User not authenticated");
    }

    // First, clean up stale online statuses
    $cleanupStmt = $pdo->prepare("
        UPDATE users 
        SET is_online = FALSE 
        WHERE last_online < NOW() - INTERVAL 5 MINUTE 
        AND is_online = TRUE
    ");
    $cleanupStmt->execute();

    // Update current user's online status first
    $updateStmt = $pdo->prepare("
        UPDATE users 
        SET is_online = TRUE, 
            last_online = CURRENT_TIMESTAMP 
        WHERE user_id = ?
    ");
    $updateStmt->execute([$_SESSION['user_id']]);

    // Fetch all users except current user
    $stmt = $pdo->prepare("
        SELECT 
            u.user_id,
            u.username,
            u.role,
            u.first_name,
            u.last_name,
            u.department,
            u.is_online,
            u.last_online,
            (u.last_online >= NOW() - INTERVAL 5 MINUTE) as is_active
        FROM users u
        WHERE u.user_id != ? 
        AND u.role IN ('supervisor', 'ceo', 'admin')
        AND u.status = 'active'
        ORDER BY 
            u.is_online DESC,
            u.last_online DESC,
            u.username ASC
    ");

    if (!$stmt) {
        throw new Exception("Failed to prepare statement");
    }
    
    $stmt->execute([$_SESSION['user_id']]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    error_log("Online users query executed. Found " . count($users) . " users");
    error_log("Current user ID: " . $_SESSION['user_id']);
    error_log("Users data: " . json_encode($users));

    echo json_encode([
        'success' => true,
        'users' => $users,
        'debug' => [
            'current_user' => $_SESSION['user_id'],
            'total_users' => count($users),
            'query_time' => date('Y-m-d H:i:s')
        ]
    ]);
} catch (Exception $e) {
    error_log("Error in online.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    http_response_code(500);