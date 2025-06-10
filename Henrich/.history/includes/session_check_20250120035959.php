<?php
function check_session_timeout() {
    // Use existing timeout logic
    if (isset($_SESSION['timeout'])) {
        if (time() - $_SESSION['timeout'] > 1800) { // 30 minutes
            if (isset($_COOKIE['remember_token'])) {
                require_once dirname(__DIR__) . '/database/dbconnect.php';
                $token = $_COOKIE['remember_token'];
                $stmt = $conn->prepare("DELETE FROM remember_tokens WHERE token = ?");
                $stmt->bind_param("s", $token);
                $stmt->execute();
                setcookie('remember_token', '', time() - 3600, '/');
            }
            session_unset();
            session_destroy();
            header("Location: ../index.php?error=Session Expired");
            exit();
        }
        $_SESSION['timeout'] = time();
    } else {
        $_SESSION['timeout'] = time();
    }
}

function check_remember_token() {
    global $conn;
    