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
        // Check for remember me token
        if (isset($_COOKIE['remember_token'])) {
            require_once dirname(__DIR__) . '/includes/config.php';
            $token = $_COOKIE['remember_token'];
            
            // Verify token
            $stmt = $pdo->prepare("
                SELECT u.* 
                FROM users u 
                JOIN remember_tokens rt ON u.user_id = rt.user_id 
                WHERE rt.token = ? 
                AND rt.expires > NOW() 
                AND u.status = 'active'
            ");
            $stmt->execute([$token]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                // Restore session
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['useremail'] = $user['useremail'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['timeout'] = time();
                
                // Update online status
                $stmt = $pdo->prepare("
                    UPDATE users 
                    SET is_online = TRUE,
                        last_online = CURRENT_TIMESTAMP
                    WHERE user_id = ?
                ");
                $stmt->execute([$user['user_id']]);
            } else {
                // Invalid or expired token
                setcookie('remember_token', '', time() - 3600, '/');
                session_unset();
                session_destroy();
                header("Location: ../index.php?error=Session Expired");
                exit();
            }
        } else {
            session_unset();
            session_destroy();
            header("Location: ../index.php?error=Session Expired");
            exit();
        }
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
$sql = "SELECT username FROM users WHERE user_id = ? AND role = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $_SESSION['user_id'], $_SESSION['role']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows != 1) {
    session_unset();
    session_destroy();
    header("Location: ../index.php?error=Invalid session");
    exit();
}

$row = $result->fetch_assoc();
$_SESSION['username'] = $row['username'];

// Login success flag handling
$login_success = false;
if (isset($_SESSION['login_success']) && $_SESSION['login_success'] === true) {
    $login_success = true;
    unset($_SESSION['login_success']);
}

