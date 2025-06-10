<?php
session_start();

// Debug logging
error_log("Session check in session.php: " . print_r($_SESSION, true));

// Set session timeout to 30 minutes
if (isset($_SESSION['timeout'])) {
    if (time() - $_SESSION['timeout'] > 1800) {
        session_unset();
        session_destroy();
    $_SESSION['timeout'] = time();
} else {
    $_SESSION['timeout'] = time();
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {  // Changed from uid to user_id
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

