<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Strict session checking
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || !isset($_SESSION['last_activity'])) {
    session_destroy();
    header("Location: ../index.php?error=session_expired");
    exit();
}

// Session timeout check (30 minutes)
if (time() - $_SESSION['last_activity'] > 1800) {
    session_destroy();
    header("Location: ../index.php?error=session_timeout");
    exit();
}
$_SESSION['last_activity'] = time();

// Admin role check
if ($_SESSION['role'] !== 'admin') {
    error_log("Unauthorized access attempt to admin area by user ID: " . $_SESSION['user_id']);
    header("Location: ../unauthorized.php");
    exit();
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
