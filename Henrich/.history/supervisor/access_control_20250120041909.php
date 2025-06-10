<?php
// Don't start session here as it's already started in session.php
require_once '../includes/config.php';

// Check if user is logged in and is a supervisor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'supervisor') {
    header("Location: ../index.php?error=Unauthorized access");
    exit();
}

// Get user details from session
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
?>