<?php
require_once '../includes/config.php';
session_start();

try {
    if (isset($_SESSION['user_id'])) {
        // Clear remember me token if exists
        if (isset($_COOKIE['remember_token'])) {
            $token = $_COOKIE['remember_token'];
            $stmt = $pdo->prepare("DELETE FROM remember_tokens WHERE token = ?");
            $stmt->execute([$token]);
            setcookie('remember_token', '', time() - 3600, '/');
        }

        // Mark user as offline
        $stmt = $pdo->prepare("
            UPDATE users 
            SET is_online = FALSE, 
                last_online = CURRENT_TIMESTAMP 
            WHERE user_id = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);

        // Log the logout
        logActivity($_SESSION['user_id'], 'logout', 'User logged out');
    }

    // Clear session
    session_unset();
    session_destroy();
    
    header("Location: ../index.php?success=Logged out successfully");
    exit();
} catch (Exception $e) {
    error_log("Logout error: " . $e->getMessage());
    header("Location: ../index.php?error=Error during logout");
    exit();
}

