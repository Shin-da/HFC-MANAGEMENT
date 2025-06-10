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
try {
    $stmt = $GLOBALS['pdo']->prepare("SELECT * FROM users WHERE user_id = ? AND role = 'admin'");
    $stmt->execute([$_SESSION['user_id']]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin) {
        error_log("Invalid admin account - User ID: {$_SESSION['user_id']}");
        session_destroy();
        header("Location: ../index.php?error=Invalid admin account");
        exit();
    }
} catch (PDOException $e) {
    error_log("Database error in access control: " . $e->getMessage());
    header("Location: ../index.php?error=System error occurred");
    exit();
}

// Update last activity
$_SESSION['last_activity'] = time();

// Set security headers
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
?>