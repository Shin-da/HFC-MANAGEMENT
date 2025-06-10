<?php
require_once '../includes/config.php';

try {
    // Start by getting the user ID before we destroy the session
    $user_id = $_SESSION['uid'] ?? null;

    if ($user_id) {
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
        $stmt->execute([$user_id]);

        // Log the logout
        $stmt = $pdo->prepare("
            INSERT INTO activity_logs (user_id, activity_type, details) 
            VALUES (?, 'logout', 'User logged out')
        ");
        $stmt->execute([$user_id]);
    }

    // Destroy session
    $_SESSION = array();
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    session_destroy();
    
    // Ensure all session data is cleared
    unset($_SESSION);
    
    // Force a new session to start for the success message
    session_start();
    $_SESSION['success'] = "Logged out successfully";
    
    // Use JavaScript to redirect and clear cache
    echo "<script>
        window.location.replace('../index.php');
    </script>";
    exit();

} catch (Exception $e) {
    error_log("Logout error: " . $e->getMessage());
    session_start();
    $_SESSION['error'] = "Error during logout";
    echo "<script>
        window.location.replace('../index.php');
    </script>";
    exit();
}

