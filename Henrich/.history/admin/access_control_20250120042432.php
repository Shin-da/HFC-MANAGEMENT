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
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();

if (!$admin) {
    session_destroy();
    header("Location: ../index.php");  // Changed from login.php to index.php
    exit();
}
?>
