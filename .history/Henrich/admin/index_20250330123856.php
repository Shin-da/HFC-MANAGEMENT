<?php
require_once 'access_control.php';
require_once '../includes/Page.php';
require_once '../includes/functions.php';

// Initialize page
Page::setTitle('Admin Dashboard - HFC Admin');
Page::setBodyClass('admin-page');
Page::setCurrentPage('dashboard');
Page::setAdminPage(true);

// Add required styles
Page::addStyle('../assets/css/admin.css');
Page::addStyle('../assets/css/dashboard.css');

// Add required scripts
Page::addScript('../assets/js/dashboard.js');
Page::addScript('https://cdn.jsdelivr.net/npm/chart.js');

ob_start();
?>

<div class="container-fluid">
    <section class="panel">
        <div class="container-fluid">
            <div class="dashboard-header">
                <div class="title">
                    <span>
                        <h2>Admin Dashboard</h2>
                    </span>
                    <span style="font-size: 12px;">Overview of system statistics and activities</span>
                </div>
                <div class="title">
                    <span><?php echo date('l, F jS'); ?></span>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-card-body">
                            <div class="stat-card-icon bg-primary">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-card-info">
                                <h5>Total Users</h5>
                                <?php
                                try {
                                    $stmt = $GLOBALS['pdo']->query("SELECT COUNT(*) FROM users");
                                    $userCount = $stmt->fetchColumn();
                                    echo "<p class='stat-number'>" . number_format($userCount) . "</p>";
                                } catch (PDOException $e) {
                                    echo "<p class='stat-number'>Error</p>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-card-body">
                            <div class="stat-card-icon bg-success">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div class="stat-card-info">
                                <h5>Supervisors</h5>
                                <?php
                                try {
                                    $stmt = $GLOBALS['pdo']->query("SELECT COUNT(*) FROM users WHERE role = 'supervisor'");
                                    $supervisorCount = $stmt->fetchColumn();
                                    echo "<p class='stat-number'>" . number_format($supervisorCount) . "</p>";
                                } catch (PDOException $e) {
                                    echo "<p class='stat-number'>Error</p>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-card-body">
                            <div class="stat-card-icon bg-warning">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="stat-card-info">
                                <h5>Pending Requests</h5>
                                <?php
                                try {
                                    $stmt = $GLOBALS['pdo']->query("SELECT COUNT(*) FROM account_requests WHERE status = 'pending'");
                                    $requestCount = $stmt->fetchColumn();
                                    echo "<p class='stat-number'>" . number_format($requestCount) . "</p>";
                                } catch (PDOException $e) {
                                    echo "<p class='stat-number'>Error</p>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-card-body">
                            <div class="stat-card-icon bg-info">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <div class="stat-card-info">
                                <h5>Active Users</h5>
                                <?php
                                try {
                                    $stmt = $GLOBALS['pdo']->query("SELECT COUNT(*) FROM users WHERE status = 'active'");
                                    $activeCount = $stmt->fetchColumn();
                                    echo "<p class='stat-number'>" . number_format($activeCount) . "</p>";
                                } catch (PDOException $e) {
                                    echo "<p class='stat-number'>Error</p>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="chart-card">
                        <div class="chart-card-header">
                            <h5>User Registration Trend</h5>
                        </div>
                        <div class="chart-card-body">
                            <canvas id="registrationChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="chart-card">
                        <div class="chart-card-header">
                            <h5>User Distribution by Role</h5>
                        </div>
                        <div class="chart-card-body">
                            <canvas id="roleDistributionChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="activity-card">
                        <div class="activity-card-header">
                            <h5>Recent Activities</h5>
                        </div>
                        <div class="activity-card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Activity</th>
                                            <th>User</th>
                                            <th>Role</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        try {
                                            $stmt = $GLOBALS['pdo']->query("
                                                SELECT a.*, u.username, u.role 
                                                FROM activities a 
                                                LEFT JOIN users u ON a.user_id = u.user_id 
                                                ORDER BY a.activity_date DESC 
                                                LIMIT 5
                                            ");
                                            $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                            foreach ($activities as $activity) {
                                                $statusClass = match($activity['status']) {
                                                    'success' => 'success',
                                                    'warning' => 'warning',
                                                    'error' => 'danger',
                                                    default => 'secondary'
                                                };
                                                
                                                echo "<tr>
                                                    <td>" . htmlspecialchars($activity['description']) . "</td>
                                                    <td>" . htmlspecialchars($activity['username']) . "</td>
                                                    <td>" . htmlspecialchars($activity['role']) . "</td>
                                                    <td>" . date('M d, Y H:i', strtotime($activity['activity_date'])) . "</td>
                                                    <td><span class='badge badge-{$statusClass}'>" . htmlspecialchars($activity['status']) . "</span></td>
                                                </tr>";
                                            }
                                        } catch (PDOException $e) {
                                            echo "<tr><td colspan='5'>Error loading activities</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
// Registration Chart Data
<?php
try {
    $stmt = $GLOBALS['pdo']->query("
        SELECT DATE(created_at) as date, COUNT(*) as count 
        FROM users 
        GROUP BY DATE(created_at) 
        ORDER BY date DESC 
        LIMIT 7
    ");
    $registrationData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $dates = array_column($registrationData, 'date');
    $counts = array_column($registrationData, 'count');
    
    echo "const registrationDates = " . json_encode(array_reverse($dates)) . ";\n";
    echo "const registrationCounts = " . json_encode(array_reverse($counts)) . ";\n";
} catch (PDOException $e) {
    echo "const registrationDates = [];\n";
    echo "const registrationCounts = [];\n";
}

// Role Distribution Data
try {
    $stmt = $GLOBALS['pdo']->query("
        SELECT role, COUNT(*) as count 
        FROM users 
        GROUP BY role
    ");
    $roleData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $roles = array_column($roleData, 'role');
    $roleCounts = array_column($roleData, 'count');
    
    echo "const roles = " . json_encode($roles) . ";\n";
    echo "const roleCounts = " . json_encode($roleCounts) . ";\n";
} catch (PDOException $e) {
    echo "const roles = [];\n";
    echo "const roleCounts = [];\n";
}
?>

// Initialize Charts
document.addEventListener('DOMContentLoaded', function() {
    // Registration Trend Chart
    new Chart(document.getElementById('registrationChart'), {
        type: 'line',
        data: {
            labels: registrationDates,
            datasets: [{
                label: 'New Registrations',
                data: registrationCounts,
                borderColor: '#4e73df',
                tension: 0.1,
                fill: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Role Distribution Chart
    new Chart(document.getElementById('roleDistributionChart'), {
        type: 'doughnut',
        data: {
            labels: roles,
            datasets: [{
                data: roleCounts,
                backgroundColor: [
                    '#4e73df',
                    '#1cc88a',
                    '#36b9cc',
                    '#f6c23e'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>

<?php
$content = ob_get_clean();
Page::render($content);
?>