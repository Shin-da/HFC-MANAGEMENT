<?php
session_start();
require_once '../includes/config.php';

if (!isset($_SESSION['admin_id']) || !isset($_SESSION['role'])) {
    header("Location: ../login.php");
    exit();
}
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../unauthorized.php");
    exit();
}
?>
