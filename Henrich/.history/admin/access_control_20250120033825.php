<?php
session_start();
require_once '../includes/config.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../unauthorized.php");
    exit();
}

// Get admin info using correct column name
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE user_id = ? AND role = 'admin' AND status = 'active'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();

if (!$admin) {
    session_destroy();
    header("Location: ../login.php");
    exit();
}
?>
