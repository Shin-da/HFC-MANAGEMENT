<?php
require_once '../includes/config.php';

try {
    if (isset($_SESSION['uid'])) {
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
        $stmt->execute([$_SESSION['uid']]);

        // Log the logout using activity_logs table directly
        $stmt = $pdo->prepare("
            INSERT INTO activity_logs (user_id, activity_type, details) 
            VALUES (?, 'logout', 'User logged out')
        ");
        $stmt->execute([$_SESSION['uid']]);
    }

    // Clear session
    session_unset();
    session_destroy();
    
    // Clear session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    header("Location: ../index.php?success=Logged out successfully");
    exit();
} catch (Exception $e) {
    error_log("Logout error: " . $e->getMessage());
    header("Location: ../index.php?error=Error during logout");
    exit();
}

