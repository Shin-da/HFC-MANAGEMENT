<?php
session_start();
include '../../config/connection.php';

// Check if the user is logged in and is a supervisor
if (!isset($_SESSION['user']) || $_SESSION['usertype'] !== 'supervisor') {
    // Redirect to login page if not logged in or not a supervisor
    header("Location: ../../in.php");
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