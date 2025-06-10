<?php
require
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Add detailed activity tracking
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
?>

<!DOCTYPE html>
<html>
<head>
    <title>Stock Activity Log</title>
    <?php require '../reusable/header.php'; ?>
    <link rel="stylesheet" type="text/css" href="../resources/css/table.css">
    <link rel="stylesheet" type="text/css" href="../resources/css/stock-pages.css">
</head>

<body>
<?php require '../reusable/sidebar.php'; ?>
    <?php include '../reusable/navbar.html'; ?>
    <section class="panel inventory-theme">
        
        <div class="container-fluid">
            <?php
            // Get activity statistics
            $today = date('Y-m-d');
            $todayActivitiesQuery = $conn->query("SELECT COUNT(*) as today_count FROM stockactivitylog WHERE DATE(dateencoded) = '$today'");
            $weekActivitiesQuery = $conn->query("SELECT COUNT(*) as week_count FROM stockactivitylog WHERE dateencoded >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
            $monthActivitiesQuery = $conn->query("SELECT COUNT(*) as month_count FROM stockactivitylog WHERE MONTH(dateencoded) = MONTH(CURRENT_DATE()) AND YEAR(dateencoded) = YEAR(CURRENT_DATE())");
            
            $todayActivities = $todayActivitiesQuery->fetch_assoc()['today_count'];
            $weekActivities = $weekActivitiesQuery->fetch_assoc()['week_count'];
            $monthActivities = $monthActivitiesQuery->fetch_assoc()['month_count'];
            ?>

            <!-- Activity Summary -->
            <div class="activity-summary">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="summary-card">
                            <h3>Today's Activities</h3>
                            <div class="value"><?= $todayActivities ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="summary-card">
                            <h3>This Week's Activities</h3>
                            <div class="value"><?= $weekActivities ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="summary-card">
                            <h3>This Month's Activities</h3>
                            <div class="value"><?= $monthActivities ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Visualization Section -->
            <div class="visualization-wrapper">
                <div class="chart-container">
                    <canvas id="monthlyTrends"></canvas>
                </div>
                <div class="chart-container">
                    <canvas id="productDistribution"></canvas>
                </div>
            </div>

            <!-- Activity Log Table -->
            <div class="table-container">
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

        <?php include_once("../reusable/footer.php"); ?>
    </section>

    <script src="../resources/js/table.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Monthly Trends Chart
            const monthlyTrends = new Chart(document.getElementById('monthlyTrends'), {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Activity Volume',
                        data: [65, 59, 80, 81, 56, 55],
                        borderColor: '#4CAF50'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Monthly Activity Trends'
                        }
                    }
                }
            });

            // Product Distribution Chart
            const productDistribution = new Chart(document.getElementById('productDistribution'), {
                type: 'pie',
                data: {
                    labels: ['Product A', 'Product B', 'Product C'],
                    datasets: [{
                        data: [300, 50, 100],
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Product Distribution'
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
