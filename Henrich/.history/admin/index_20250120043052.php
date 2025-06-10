<?php
require_once 'access_control.php';
require_once '../includes/config.php';

// Get counts for dashboard with error handling
try {
    $supervisor_count = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'supervisor'")->fetch_assoc()['count'];
    
    // Check if employees table exists
    $table_exists = $conn->query("SHOW TABLES LIKE 'employees'")->num_rows > 0;
    $employee_count = $table_exists ? 
        $conn->query("SELECT COUNT(*) as count FROM employees")->fetch_assoc()['count'] : 
        0;
    
    // Check if account_requests table exists
    $table_exists = $conn->query("SHOW TABLES LIKE 'account_requests'")->num_rows > 0;
    $pending_requests = $table_exists ? 
        $conn->query("SELECT COUNT(*) as count FROM account_requests WHERE status = 'pending'")->fetch_assoc()['count'] : 
        0;
} catch (Exception $e) {
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
