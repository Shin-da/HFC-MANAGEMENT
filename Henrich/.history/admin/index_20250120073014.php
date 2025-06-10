<?php
require_once 'access_control.php';
require_once '../includes/config.php';
require_once '../includes/session.php';

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
    <?php
include '../reusable/head.php';
</head>
<body class="admin-body">
    <?php 
    $current_page = 'dashboard';
    include '../includes/admin_sidebar.php'; 
    ?>
    
    <main class="admin-main">
        <div class="admin-container">
            <div class="page-header">
                <div class="header-title">
                    <h2><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h2>
                    <p class="text-muted">Welcome back, <?= htmlspecialchars($_SESSION['username']) ?></p>
                </div>
                <div class="header-actions">
                    <button class="btn btn-refresh" onclick="refreshStats()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                    <button class="btn btn-primary" onclick="window.location.href='system-settings.php'">
                        <i class="fas fa-cogs"></i> Settings
                    </button>
                </div>
            </div>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success fade-out">
                    <i class="fas fa-check-circle"></i>
                    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            
            <div class="dashboard-grid">
                <div class="stat-card primary">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-content">
                        <h3>Active Supervisors</h3>
                        <p class="stat-number" id="supervisorCount"><?= $supervisor_count ?></p>
                    </div>
                </div>
                
                <div class="stat-card warning">
                    <div class="stat-icon"><i class="fas fa-user-clock"></i></div>
                    <div class="stat-content">
                        <h3>Pending Requests</h3>
                        <p class="stat-number" id="requestCount"><?= $pending_requests ?></p>
                    </div>
                </div>
            </div>

            <div class="quick-actions">
                <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
                <div class="action-grid">
                    <a href="manage-supervisors.php" class="action-card">
                        <i class="fas fa-user-tie"></i>
                        <span>Manage Supervisors</span>
                    </a>
                    <a href="manage-account-requests.php" class="action-card">
                        <i class="fas fa-user-plus"></i>
                        <span>Account Requests</span>
                        <?php if($pending_requests > 0): ?>
                            <span class="badge pulse"><?= $pending_requests ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="system-settings.php" class="action-card">
                        <i class="fas fa-cogs"></i>
                        <span>System Settings</span>
                    </a>
                </div>
            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/admin-dashboard.js"></script>
</body>
</html>
