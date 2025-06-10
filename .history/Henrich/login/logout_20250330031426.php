<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
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

        // Try to log the logout if the table exists
        try {
            $stmt = $pdo->prepare("
                INSERT INTO activity_logs (user_id, activity_type, details) 
                VALUES (?, 'logout', 'User logged out')
            ");
            $stmt->execute([$user_id]);
        } catch (PDOException $e) {
            // Ignore activity logging errors
            error_log("Activity logging failed: " . $e->getMessage());
        }
    }

    // Destroy session
    $_SESSION = array();
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    session_destroy();
    
    // Clear all cookies
    foreach ($_COOKIE as $name => $value) {
        setcookie($name, '', time() - 3600, '/');
    }
    
    // Use a simple redirect instead of JavaScript
    header("Location: ../index.php?msg=logout_success");
    exit();

} catch (Exception $e) {
    // Log the error
    error_log("Logout error: " . $e->getMessage());
    
    // Display error and wait 5 seconds before redirecting
    echo "<h2>Error during logout:</h2>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
    echo "<p>Redirecting in 5 seconds...</p>";
    echo "<script>
        setTimeout(function() {
            window.location.href = '../index.php?error=" . urlencode($e->getMessage()) . "';
        }, 5000);
    </script>";
    exit();
}

