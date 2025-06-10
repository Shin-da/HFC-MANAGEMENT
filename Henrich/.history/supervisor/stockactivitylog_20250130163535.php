<?php
require '../includes/session.php';
require '../includes/config.php';
require '../includes/Page.php';

require_once './access_control.php';

$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Set page title and styles
Page::setTitle('Stock Activity Log');
Page::addStyle('../assets/css/stock-pages.css');

Page::addStyle('../assets/css/dashboard.css');
Page::addStyle('../assets/css/shared-dashboard.css');
Page::addStyle('../assets/css/table.css');
Page::addStyle('../assets/css/stockactivitylog.css');
Page::addStyle('../assets/css/animations.css');

// Add theme styles
Page::addStyle('/assets/css/theme-toggle.css');
Page::addScript('/assets/js/theme-toggle.js');

// Add required scripts
Page::addScript('https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js'); 
Page::addScript('https://cdn.jsdelivr.net/npm/chart.js'); // Change from charts.js to chart.js
Page::addScript('../assets/js/stockactivitylog.js');

// Add activity tracking function
function logActivity($type, $details, $userId) {
    global $conn;
    $stmt = $conn->prepare("
        INSERT INTO stockactivitylog 
            (activity_type, details, user_id, timestamp, ip_address) 
        VALUES (?, ?, ?, NOW(), ?)
    ");
    $stmt->bind_param("ssis", $type, $details, $userId, $_SERVER['REMOTE_ADDR']);
    return $stmt->execute();
}

// Add activity categorization
$activityTypes = [
    'STOCK_IN' => 'Stock Receipt',
    'STOCK_OUT' => 'Stock Release',
    'ADJUSTMENT' => 'Stock Adjustment',
    'TRANSFER' => 'Stock Transfer'
];

// Add this function to format numbers
function formatNumber($number) {
    return number_format($number, 0, '.', ',');
}

// Fetch activity statistics before starting output
try {
    // Today's activities
    $todayQuery = $conn->prepare("
        SELECT COUNT(*) as count 
        FROM stockactivitylog 
        WHERE DATE(dateencoded) = CURRENT_DATE()
    ");
    $todayQuery->execute();
    $todayActivities = $todayQuery->get_result()->fetch_assoc()['count'];

    // This week's activities
    $weekQuery = $conn->prepare("
        SELECT COUNT(*) as count 
        FROM stockactivitylog 
        WHERE dateencoded >= DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY)
    ");
    $weekQuery->execute();
    $weekActivities = $weekQuery->get_result()->fetch_assoc()['count'];

    // This month's activities
    $monthQuery = $conn->prepare("
        SELECT COUNT(*) as count 
        FROM stockactivitylog 
        WHERE MONTH(dateencoded) = MONTH(CURRENT_DATE()) 
        AND YEAR(dateencoded) = YEAR(CURRENT_DATE())
    ");
    $monthQuery->execute();
    $monthActivities = $monthQuery->get_result()->fetch_assoc()['count'];

} catch (Exception $e) {
    error_log("Error fetching activity statistics: " . $e->getMessage());
    $todayActivities = $weekActivities = $monthActivities = 0;
}

ob_start();
?>

<div class="dashboard-wrapper">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <h1>Stock Activity Dashboard</h1>
            <div class="header-actions">
                <button class="btn-export" onclick="exportToExcel()">
                    <i class='bx bx-export'></i> Export Report
                </button>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card animate-fade-in delay-1">
            <div class="stat-icon success">
                <i class='bx bx-calendar'></i>
            </div>
            <div class="stat-content">
                <h3>Today's Activities</h3>
                <div class="stat-value"><?= formatNumber($todayActivities) ?></div>
                <div class="stat-label">Last 24 hours</div>
            </div>
        </div>
        <div class="stat-card animate-fade-in delay-2">
            <div class="stat-icon info">
                <i class='bx bx-calendar-week'></i>
            </div>
            <div class="stat-content">
                <h3>Weekly Activities</h3>
                <div class="stat-value"><?= formatNumber($weekActivities) ?></div>
                <div class="stat-label">Last 7 days</div>
            </div>
        </div>
        <div class="stat-card animate-fade-in delay-3">
            <div class="stat-icon warning">
                <i class='bx bx-calendar-alt'></i>
            </div>
            <div class="stat-content">
                <h3>Monthly Activities</h3>
                <div class="stat-value"><?= formatNumber($monthActivities) ?></div>
                <div class="stat-label">This month</div>
            </div>
        </div>
    </div>

    <!-- Activity Charts -->
    <div class="dashboard-grid animate-fade-in delay-2">
        <div class="chart-card wide">
            <div class="card-header">
                <h3>Monthly Activity Trends</h3>
                <div class="card-actions">
                    <select id="trendTimeframe" class="form-select">
                        <option value="monthly">Monthly</option>
                        <option value="weekly">Weekly</option>
                    </select>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="monthlyTrends"></canvas>
            </div>
        </div>
    </div>

    <div class="dashboard-grid">
        <!-- Activity Distribution -->
        <div class="chart-card">
            <h3>Activity Distribution</h3>
            <div class="chart-container">
                <canvas id="productDistribution"></canvas>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="activity-feed">
            <h3>Recent Activities</h3>
            <div class="activity-list scrollbar-thin">
                <!-- Activity items will be loaded dynamically -->
            </div>
        </div>
    </div>

    <!-- Advanced Filters -->
    <div class="filters-section">
        <div class="filter-group">
            <select id="activityType" class="form-select">
                <option value="">All Activities</option>
                <!-- ...options... -->
            </select>
            <div class="date-range">
                <input type="date" id="startDate" class="form-control">
                <input type="date" id="endDate" class="form-control">
            </div>
            <button id="applyFilters" class="btn-primary">Apply Filters</button>
        </div>
    </div>

    <!-- Activity Log Table -->
    <div class="table-section animate-fade-in delay-3">
        <div class="table-container theme-aware">
            <div class="table-header">
                <div class="title">
                    <h2>Stock Activity Log</h2>
                    <span style="font-size: 12px;">Encoded by Batch</span>
                </div>
                <div class="title">
                    <span><?php echo date('l, F jS') ?></span>
                </div>
            </div>

            <?php
            // Pagination setup
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $offset = ($page - 1) * $limit;

            $totalRecords = $conn->query("SELECT COUNT(*) FROM stockactivitylog")->fetch_row()[0];
            $totalPages = ceil($totalRecords / $limit);

            // Fetch records
            $result = $conn->query("SELECT * FROM stockactivitylog ORDER BY dateencoded DESC LIMIT $offset, $limit");
            ?>

            <!-- Table content here -->
            <div class="container-fluid" style="overflow-x: auto;">
                <table class="table" id="myTable">
                    <thead>
                        <tr>
                            <th>Batch ID</th>
                            <th>Date of Arrival</th>
                            <th>Date Encoded</th>
                            <th>Encoder</th>
                            <th>Description [productcode (pieces)]</th>
                            <th>Total Number Of Boxes</th>
                            <th>Overall Total Weight (kg)</th>
                        </tr>
                    </thead>
                    <thead>
                        <tr class="filter-row">
                            <th style="padding: 0;"><input type="text" placeholder="Search Batch ID ... " id="batchid-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 0)"></th>
                            <th><input type="text" placeholder="Search Date of Arrival ... " id="dateofarrival-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 1)"></th>
                            <th><input type="text" placeholder="Search Date Encoded ... " id="dateencoded-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 2)"></th>
                            <th><input type="text" placeholder="Search Encoder ... " id="encoder-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 3)"></th>
                            <th><input type="text" placeholder="Search Description ... " id="description-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 4)"></th>
                            <th><input type="text" placeholder="Search Total Number Of Boxes ... " id="totalNumberOfBoxes-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 5)"></th>
                            <th><input type="text" placeholder="Search Overall Total Weight (kg) ... " id="overalltotalweight-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 6)"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0):
                            while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['batchid']) ?></td>
                                    <td><?= htmlspecialchars($row['dateofarrival']) ?></td>
                                    <td><?= htmlspecialchars($row['dateencoded']) ?></td>
                                    <td><?= htmlspecialchars($row['encoder']) ?></td>
                                    <td><?= nl2br(htmlspecialchars($row['description'])) ?></td>
                                    <td><?= htmlspecialchars($row['totalNumberOfBoxes']) ?> boxes</td>
                                    <td><?= number_format($row['overalltotalweight'], 2) ?> kg</td>
                                </tr>
                            <?php endwhile;
                        else: ?>
                            <tr><td colspan="7">No records found</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination-wrapper">
                <div class="dataTables_info">
                    Showing <?= $offset + 1 ?> to <?= min($offset + $limit, $totalRecords) ?> of <?= $totalRecords ?> entries
                </div>
                <ul class="pagination">
                    <li><a href="?page=<?= max(1, $page - 1) ?>&limit=<?= $limit ?>" class="prev">&laquo;</a></li>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li><a href="?page=<?= $i ?>&limit=<?= $limit ?>" class="page <?= $page == $i ? 'active' : '' ?>"><?= $i ?></a></li>
                    <?php endfor; ?>
                    <li><a href="?page=<?= min($totalPages, $page + 1) ?>&limit=<?= $limit ?>" class="next">&raquo;</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/stockactivitylog.js"></script>

<?php
$content = ob_get_clean();
Page::render($content);
?>
