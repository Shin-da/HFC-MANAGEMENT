<?php
require_once 'access_control.php';
require_once '../includes/config.php';

// Get counts for dashboard with error handling
try {
    $supervisor_count = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'supervisor' AND status = 'active'")->fetch_assoc()['count'];
    $pending_requests = $conn->query("SELECT COUNT(*) as count FROM account_requests WHERE status = 'pending'")->fetch_assoc()['count'];
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    $supervisor_count = 0;
    $pending_requests = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - HFC Management</title>
    error_log("Database error: " . $e->getMessage());
    $supervisor_count = 0;
    $employee_count = 0;
    $pending_requests = 0;
}
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
            </div>
            <div class="stat-card">
                <h3>Pending Requests</h3>
                <p class="stat-number"><?= $pending_requests ?></p>
            </div>
        </div>

        <div class="quick-actions">
            <h3>Quick Actions</h3>
            <div class="action-buttons">
                <a href="manage-supervisors.php" class="btn btn-primary">Manage Supervisors</a>
                <a href="manage-employees.php" class="btn btn-primary">Manage Employees</a>
                <a href="manage-account-requests.php" class="btn btn-warning">Account Requests <?php if($pending_requests > 0): ?><span class="badge"><?= $pending_requests ?></span><?php endif; ?></a>
                <a href="system-settings.php" class="btn btn-secondary">System Settings</a>
            </div>
        </div>
    </div>

    <?php include '../includes/admin_footer.php'; ?>
    <script src="../assets/js/admin.js"></script>
</body>
</html>
