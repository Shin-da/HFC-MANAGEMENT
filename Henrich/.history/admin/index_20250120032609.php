<?php
require_once 'access_control.php';
require_once '../includes/config.php';

// Get counts for dashboard
<html>
<head>
    <title>Admin Dashboard</title>
    // ...existing code...
</head>
<body>
    <div class="container">
        <h2>Admin Dashboard</h2>
        <div class="admin-controls">
            <!-- Admin-specific operations -->
            <a href="manage-employees.php">Manage Employees</a>
            <a href="system-settings.php">System Settings</a>
            <a href="manage-supervisors.php">Manage Supervisors</a>
        </div>
    </div>
</body>
</html>
