<?php
require_once 'access_control.php';
require_once '../includes/Page.php';
require_once '../includes/functions.php';

// Initialize page
Page::setTitle('Admin Dashboard - HFC Management');
Page::setBodyClass('admin-dashboard');
Page::setCurrentPage('index');

// Add required styles & scripts
Page::addStyle('../assets/css/variables.css');
Page::addStyle('../assets/css/style.css');
Page::addStyle('../assets/css/sidebar.css');
Page::addStyle('../assets/css/navbar.css');
Page::addStyle('../assets/css/theme.css');
Page::addStyle('../assets/css/admin-navbar.css');
Page::addStyle('../assets/css/dashboard.css');
Page::addStyle('../assets/css/admin.css');
Page::addStyle('../assets/css/admin-dashboard.css');
Page::addStyle('../assets/css/shared-dashboard.css');
Page::addStyle('../assets/css/admin-layout.css');
Page::addStyle('../assets/css/calendar.css');
Page::addStyle('../assets/css/form.css');
Page::addStyle('../assets/css/table.css');
Page::addStyle('../assets/css/customer-pages.css');
Page::addStyle('../assets/css/sales.css');
Page::addStyle('../assets/css/nav-sidebar.css');
Page::addStyle('../assets/css/components/navbar.css');
Page::addStyle('../assets/css/components/notification-dropdown.css');
Page::addStyle('../assets/css/components/message-dropdown.css');
Page::addScript('https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js');
Page::addScript('../assets/js/admin-dashboard.js');
Page::addScript('../assets/js/product_rank.js');
Page::addScript('../assets/js/customer-pages.js');
Page::addScript('../assets/js/form.js');
Page::addScript('../assets/js/layout-init.js');
Page::addScript('../assets/js/layout-manager.js');
Page::addScript('../assets/js/notification-handler.js');
Page::addScript('../assets/js/notification-dropdown.js');
Page::addScript('../assets/js/sidebar-dropdown.js');
Page::addScript('../assets/js/sidebar.js');
Page::addScript('../assets/js/theme.js');
Page::addScript('../assets/js/theme-manager.js');
Page::addScript('../assets/js/navbar.js');

ob_start();

// Fetch dashboard statistics with error handling
try {
    $totalUsers = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
    $totalOrders = $conn->query("SELECT COUNT(*) FROM customerorder")->fetch_row()[0];
    $totalProducts = $conn->query("SELECT COUNT(*) FROM products")->fetch_row()[0];
    $pendingRequests = $conn->query("SELECT COUNT(*) FROM account_requests WHERE status = 'pending'")->fetch_row()[0];

    // Fetch chart data
    $salesData = $conn->query("
        SELECT DATE(created_at) as date, COUNT(*) as total
        FROM customerorder
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        GROUP BY DATE(created_at)
        ORDER BY date ASC
    ")->fetch_all(MYSQLI_ASSOC);

    $activityData = $conn->query("
        SELECT DATE(created_at) as date, COUNT(*) as total
        FROM activity_logs
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        GROUP BY DATE(created_at)
        ORDER BY date ASC
    ")->fetch_all(MYSQLI_ASSOC);

} catch (mysqli_sql_exception $e) {
    error_log("Error fetching dashboard data: " . $e->getMessage());
    $totalUsers = $totalOrders = $totalProducts = $pendingRequests = 0;
    $salesData = $activityData = [];
}
?>

<div class="admin-container fade-in">
    <div class="dashboard-header">
        <div class="welcome-section">
            <h1>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></h1>
            <p class="timestamp"><?= date('l, F j, Y') ?></p>
        </div>
        <div class="quick-actions">
            <button class="admin-btn admin-btn-primary" onclick="location.href='manage-users.php'">
                <i class='bx bxs-user-plus'></i> Add User
            </button>
            <button class="admin-btn admin-btn-secondary" onclick="location.href='system-settings.php'">
                <i class='bx bxs-cog'></i> Settings
            </button>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <i class='bx bxs-user-account'></i>
            <h3>Total Users</h3>
            <p id="totalUsersCount"><?= number_format($totalUsers) ?></p>
        </div>
        <div class="stat-card">
            <i class='bx bxs-shopping-bag'></i>
            <h3>Total Orders</h3>
            <p id="totalOrdersCount"><?= number_format($totalOrders) ?></p>
        </div>
        <div class="stat-card">
            <i class='bx bxs-box'></i>
            <h3>Products</h3>
            <p id="totalProductsCount"><?= number_format($totalProducts) ?></p>
        </div>
        <div class="stat-card">
            <i class='bx bxs-user-plus'></i>
            <h3>Pending Requests</h3>
            <p id="pendingRequestsCount"><?= number_format($pendingRequests) ?></p>
        </div>
    </div>

    <!-- Enhanced Charts Section -->
    <div class="charts-section">
        <div class="chart-card">
            <div class="chart-header">
                <h3>Sales Overview</h3>
                <select id="salesPeriod" class="chart-select">
                    <option value="7">Last 7 Days</option>
                    <option value="30">Last 30 Days</option>
                    <option value="90">Last 90 Days</option>
                </select>
            </div>
            <div class="chart-body">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
        
        <div class="chart-card">
            <div class="chart-header">
                <h3>User Activity</h3>
                <select id="activityPeriod" class="chart-select">
                    <option value="7">Last 7 Days</option>
                    <option value="30">Last 30 Days</option>
                    <option value="90">Last 90 Days</option>
                </select>
            </div>
            <div class="chart-body">
                <canvas id="activityChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Enhanced Activity Feed -->
    <div class="activity-feed">
        <h3>Recent Activity</h3>
        <div class="activity-list">
            <?php
            try {
                $activities = $conn->query("
                    SELECT al.*, u.username 
                    FROM activity_logs al
                    LEFT JOIN users u ON al.user_id = u.user_id
                    ORDER BY al.created_at DESC 
                    LIMIT 5
                ");
                
                if ($activities && $activities->num_rows > 0) {
                    while ($activity = $activities->fetch_assoc()):
                    ?>
                    <div class="activity-item">
                        <i class='bx bxs-bell'></i>
                        <div class="activity-details">
                            <p><?= htmlspecialchars($activity['description']) ?></p>
                            <small><?= date('M d, Y H:i', strtotime($activity['created_at'])) ?></small>
                        </div>
                    </div>
                    <?php 
                    endwhile;
                } else {
                    echo '<div class="no-activity">No recent activity</div>';
                }
            } catch (mysqli_sql_exception $e) {
                error_log("Error fetching activity logs: " . $e->getMessage());
                echo '<div class="error-message">Unable to load activity logs</div>';
            }
            ?>
        </div>
    </div>
</div>

<script>
    const BASE_URL = '<?= BASE_URL ?>';
</script>

<script>
// Initialize dashboard data
document.addEventListener('DOMContentLoaded', async function() {
    try {
        const response = await fetch(`${BASE_URL}admin/api/get-dashboard-data.php`);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.message || 'Failed to load dashboard data');
        }

        // Update stats counters
        updateStats(data.stats);
        
        // Initialize charts
        if (typeof initializeCharts === 'function') {
            initializeCharts(data);
        }

    } catch (error) {
        console.error('Dashboard initialization error:', error);
        showNotification('Error loading dashboard data', 'error');
    }
});

function updateStats(stats) {
    const statsMap = {
        'users': 'totalUsersCount',
        'orders': 'totalOrdersCount',
        'products': 'totalProductsCount',
        'requests': 'pendingRequestsCount'
    };

    Object.entries(statsMap).forEach(([key, elementId]) => {
        const element = document.getElementById(elementId);
        if (element && stats[key]) {
            element.textContent = parseInt(stats[key]).toLocaleString();
        }
    });
}

function showNotification(message, type = 'error') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => notification.remove(), 3000);
}
</script>

<?php
$content = ob_get_clean();
Page::render($content);
?>