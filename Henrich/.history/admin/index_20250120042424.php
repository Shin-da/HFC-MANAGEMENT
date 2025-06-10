<?php
require_once 'access_control.php';
require_once '../includes/config.php';

// Get counts for dashboard
$supervisor_count = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'supervisor'")->fetch_assoc()['count'];
$employee_count = $conn->query("SELECT COUNT(*) as count FROM employees")->fetch_assoc()['count'];
$pending_requests = $conn->query("SELECT COUNT(*) as count FROM account_requests WHERE status = 'pending'")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - HFC Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/admin_header.php'; ?>
    
    <div class="container">
        <h2>Admin Dashboard</h2>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        
        <div class="dashboard-stats">
            <div class="stat-card">
                <h3>Total Supervisors</h3>
                <p class="stat-number"><?= $supervisor_count ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Employees</h3>
                <p class="stat-number"><?= $employee_count ?></p>
</html>
