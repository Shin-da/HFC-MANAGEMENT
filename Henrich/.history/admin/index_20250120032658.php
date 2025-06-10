<?php
require_once 'access_control.php';
require_once '../includes/config.php';

// Get counts for dashboard
$supervisor_count = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'supervisor'")->fetch_assoc()['count'];
$pending_requests = $conn->query("SELECT COUNT(*) as count FROM account_requests WHERE status = 'pending'")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    // ...existing code...
</head>
<body>
    <div class="container">
        <h2>Admin Dashboard</h2>
        <div class="dashboard-stats">
            <div class="stat-card">
                <h3>Total Supervisors</h3>
                <p><?= $supervisor_count ?></p>
            </div>
            <div class="stat-card">
                <h3>Pending Requests</h3>
                <p><?= $pending_requests ?></p>
            </div>
        </div>
        <div class="admin-controls">
            <!-- Admin-specific operations -->
            <a href="manage-supervisors.php">Manage Supervisors</a>
            <a href="system-settings.php">System Settings</a>