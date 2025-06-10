<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Basic session check
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    error_log("Session variables not set - User ID: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'not set'));
    session_destroy();
    header("Location: ../index.php?error=Please log in first");
    exit();
}

// Admin role check
if ($_SESSION['role'] !== 'admin') {
    error_log("Non-admin access attempt - User ID: {$_SESSION['user_id']}, Role: {$_SESSION['role']}");
    header("Location: ../unauthorized.php");
    exit();
}

// Verify admin exists in database
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ? AND role = 'admin'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
}

// Verify admin status
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ? AND role = 'admin' AND status = 'active'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();

if (!$admin) {
    session_destroy();
    error_log("Invalid admin access attempt - User ID: " . $user_id);
    header("Location: ../index.php?error=invalid_admin");
    exit();
}

// Set security headers
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
?>
