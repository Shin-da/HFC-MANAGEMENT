<?php
// Don't start session here as it's already started in session.php
require_once '../includes/config.php';

// Check if user is logged in and is a supervisor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'supervisor') {
    header("Location: ../index.php?error=Unauthorized access");
    header("Location: ../../index.php");
    exit();
}

// Get user details from session
$user_id = $_SESSION['user'];
$username = $_SESSION['username'];

// You can add more specific access control logic here
// For example, checking specific permissions for supervisors

// Prevent direct URL access to sensitive pages
if (!isset($_SERVER['HTTP_REFERER'])) {
    // If accessed directly without being referred from another page
    header("Location: dashboard.php");
    exit();
}
?>