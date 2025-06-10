<?php
session_start();
require_once '../includes/config.php';
    header("Location: ../login.php");
    exit();
}

// Verify admin role
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../unauthorized.php");
    exit();
}
?>
