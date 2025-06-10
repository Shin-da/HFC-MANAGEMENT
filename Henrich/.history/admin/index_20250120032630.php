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
            <a href="system-settings.php">System Settings</a>
            <a href="manage-supervisors.php">Manage Supervisors</a>
        </div>
    </div>
</body>
</html>
