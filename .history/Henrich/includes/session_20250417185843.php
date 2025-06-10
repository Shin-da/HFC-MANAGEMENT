<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debug logging
error_log("Session check in session.php: " . print_r($_SESSION, true));

// Add this debugging code temporarily
error_log("Session data in session.php:");
error_log(print_r($_SESSION, true));

// Set session timeout to 30 minutes
if (isset($_SESSION['timeout'])) {
    if (time() - $_SESSION['timeout'] > 1800) {
        // Check for remember me token using the new function
        // Ensure functions.php is included before this point
        if (!function_exists('attemptRememberMeLogin')) {
             require_once dirname(__DIR__) . '/includes/functions.php';
        }
        
        if (!attemptRememberMeLogin()) {
            // If remember me login failed or no token existed
            session_unset();
            session_destroy();
            header("Location: ../index.php?error=Session Expired or Invalid Token");
            exit();
        }
        // If attemptRememberMeLogin() was successful, the session is restored,
        // so we just continue and reset the timeout below.
    }
    $_SESSION['timeout'] = time();
} else {
    $_SESSION['timeout'] = time();
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: ../index.php?error=You've been logged out");
    exit();
}

// Fetch username from database table user based on session role
require_once dirname(__DIR__) . '/includes/config.php';
$stmt = $GLOBALS['pdo']->prepare("SELECT username FROM users WHERE user_id = ? AND role = ?");
$stmt->execute([$_SESSION['user_id'], $_SESSION['role']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_unset();
    session_destroy();
    header("Location: ../index.php?error=Invalid session");
    exit();
}

$_SESSION['username'] = $user['username'];

// Login success flag handling
$login_success = false;
if (isset($_SESSION['login_success']) && $_SESSION['login_success'] === true) {
    $login_success = true;
    unset($_SESSION['login_success']);
}

