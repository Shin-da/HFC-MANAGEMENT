<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>

<!DOCTYPE html>
<html>

<head>
    <title>INVENTORY</title>
    <?php require '../reusable/header.php'; ?>
    <link rel="stylesheet" type="text/css" href="../resources/css/table.css">
    <link rel="stylesheet" type="text/css" href="../resources/css/stockmovement.css">
</head>

<body>

    <?php include '../reusable/sidebar.php';  // Sidebar          
    ?>

    <section class=" panel"><!-- === stock_activity_log === -->
        <?php include '../reusable/navbarNoSearch.html'; // TOP NAVBAR             
        ?>

/*************  ✨ Codeium Command ⭐  *************/
            <!-- Add visualization container -->
            <div id="visualizations">
                <canvas id="monthlyTrends"></canvas>
                <canvas id="productDistribution"></canvas>
            </div>
            <!-- Add batch comparison tool -->
            <div class="batch-comparison">
                <select id="batch1"></select>
                <select id="batch2"></select>
                <button onclick="compareBatches()">Compare Batches</button>
            </div>
            <!-- Add alert configuration -->
            <div class="alert-config">
                <h3>Alert Settings</h3>
                <input type="number" id="lowStockThreshold" placeholder="Low stock threshold">
                <button onclick="saveAlertSettings()">Save Settings</button>
            </div>
            <?php
            // Get today's activities count
            $today = date('Y-m-d');
            $todayActivitiesQuery = $conn->query("SELECT COUNT(*) as today_count
                FROM stockactivitylog
                WHERE DATE(dateencoded) = '$today'");
            $todayActivities = $todayActivitiesQuery->fetch_assoc()['today_count'];

            // Get additional activity statistics
            $weekActivitiesQuery = $conn->query("SELECT COUNT(*) as week_count
                FROM stockactivitylog
                WHERE dateencoded >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
            $weekActivities = $weekActivitiesQuery->fetch_assoc()['week_count'];

            $monthActivitiesQuery = $conn->query("SELECT COUNT(*) as month_count
                FROM stockactivitylog
                WHERE MONTH(dateencoded) = MONTH(CURRENT_DATE())
                AND YEAR(dateencoded) = YEAR(CURRENT_DATE())");
            $monthActivities = $monthActivitiesQuery->fetch_assoc()['month_count'];
            ?>
            <div class="container-fluid">
                <!-- Activity Summary -->
                <div class="summary-wrapper">
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
                <!-- Batch Comparison -->
                <div class="comparison-wrapper">
                    <div class="batch-comparison">
                        <div class="form-group">
                            <select id="batch1" class="form-select">
                                <!-- Options populated dynamically -->
                            </select>
                            <select id="batch2" class="form-select">
                                <!-- Options populated dynamically -->
                            </select>
                            <button class="btn btn-primary">Compare Batches</button>
                        </div>
                    </div>
                </div>
                <!-- Activity Log Table -->
                <div class="table-wrapper">
                    <div class="table-container">
                        <!-- ...existing table code... -->
                    </div>
                    <!-- ...existing pagination code... -->
                </div>
            </div>
/******  66e4f226-8066-4c4e-b706-a0a6cadf751d  *******/
        <!-- stock_activity_log   -->
        <div class="container-fluid">
            <div class="table-header">
                <div class="title">
                    <h2>Stock Activity Log</h2>
                    <span style="font-size: 12px;">Encoded by Batch (adding and display only)</span>
                </div>
                <div class="title">
                    <span><?php echo date('l, F jS') ?></span>

                </div>
            </div>


            <div class="table-header">

                <form class="form">
                    <button>
                        <svg width="17" height="16" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-labelledby="search">
                            <path d="M7.667 12.667A5.333 5.333 0 107.667 2a5.333 5.333 0 000 10.667zM14.334 14l-2.9-2.9" stroke="currentColor" stroke-width="1.333" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </button>
                    <input class="input" id="general-search" onkeyup="search()" placeholder="Search the table..." required="" type="text">
                    <button class="reset" type="reset">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </form>

                <?php // pagination for stock management table
                $page = isset($_GET['page']) ? $_GET['page'] : 1;
                $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
                $start = ($page - 1) * $limit;
                $items = $conn->query("SELECT * FROM inventory  LIMIT $start, $limit");
                $totalRecords = $conn->query("SELECT COUNT(*) FROM stockactivitylog")->fetch_row()[0];
                $totalPages = ceil($totalRecords / $limit);
                ?>
                <div style=" display: flex; justify-content: space-around; align-items: center; width: 100%;">
                    <div class="dataTables_info" id="example_info" role="status" aria-live="polite">Showing <?= $start + 1 ?> to <?= $start + $limit ?> of <?= $totalRecords ?> entries</div>
                    <div class="filter-box"> <!-- Filter results by number of entries -->
                        <label for="limit">Show</label>
                        <select id="limit" onchange="location.href='?page=<?= $page ?>&limit=' + this.value">
                            <option value="10" <?php echo $limit == 10 ? 'selected' : '' ?>>10</option>
                            <option value="25" <?php echo $limit == 25 ? 'selected' : '' ?>>25</option>
                            <option value="50" <?php echo $limit == 50 ? 'selected' : '' ?>>50</option>
                            <option value="100" <?php echo $limit == 100 ? 'selected' : '' ?>>100</option>
                        </select>
                        <label for="limit">entries</label>
                    </div>
                </div>
            </div>
            <?php // pagination for stock_management table
            $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
            $offset = ($page - 1) * $limit;

            $stockManagementTableSQL = "SELECT * FROM stockactivitylog ORDER BY batchid ASC LIMIT $limit OFFSET $offset";
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            $sql = $stockManagementTableSQL;
            $result = $conn->query($sql);
            ?>
            <div class="">
                <div class="container-fluid" style="overflow-x: scroll">

                    <!-- Inventory Tab -->
                    <table class="table" id="myTable">
                        <thead>
                            <tr>
                                <th>Batch ID</th> <!-- batchid -->
                                <th>Date of Arrival</th> <!-- dateofarrival -->
                                <th>Date Encoded</th> <!-- dateencoded -->
                                <th>Encoder</th> <!-- encoder -->
                                <th>Description [productcode (pieces)]</th> <!-- description -->
                                <th>Total Number Of Boxes</th> <!-- totalNumberOfBoxes -->
                                <th>Overall Total Weight (kg)</th> <!-- overalltotalweight -->
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
                        <tbody id="table-body">
                            <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $batchid = $row['batchid'];
                                    $dateofarrival = $row['dateofarrival'];
                                    $dateencoded = $row['dateencoded'];
                                    $encoder = $row['encoder'];
                                    $description = $row['description'];
                                    $totalNumberOfBoxes = $row['totalNumberOfBoxes'];
                                    $overalltotalweight = $row['overalltotalweight'];
                            ?>
                                    <tr>
                                        <td><?= $batchid ?></td>
                                        <td><?= $dateofarrival ?></td>
                                        <td><?= $dateencoded ?></td>
                                        <td><?= $encoder ?></td>
                                        <td>
                                            <?php
                                            $orderDescription = explode(", ", $description ?? '');
                                            $description = "";
                                            foreach ($orderDescription as $desc) {
                                                if (strlen($description) + strlen($desc) + 1 > 50) {
                                                    $description .= "...";
                                                    break;
                                                }
                                                $description .= "- " . $desc . "<br>";
                                            }
                                            echo $description;
                                            ?>
                                        </td>
                                        <td><?= $totalNumberOfBoxes ?> boxes</td>
                                        <td><?= number_format($overalltotalweight, 2) ?> kg</td>
                                    </tr>
                            <?php
                                }
                            } else {
                                echo "<tr><td colspan='10'>0 results</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="container" style="display: flex; justify-content: center; flex-direction: column; align-items: center; ">
                    <ul class="pagination"><!-- Pagination for stock management -->
                        <li><a href="?page=<?= $page - 1 <= 1 ? 1 : $page - 1 ?>" class="prev">&laquo;</a></li>
                        <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                            <li><a href="?page=<?= $i ?>" class="page <?= $page == $i ? 'active' : '' ?>"><?= $i ?></a></li>
                        <?php } ?>
                        <li><a href="?page=<?= $page + 1 > $totalPages ? $totalPages : $page + 1 ?>" class="next">&raquo;</a></li>
                    </ul>

                </div>
            </div>



        </div>

    </section>

</body>
<script src="../resources/js/table.js"> </script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function initializeCharts() {
        // Add chart initialization code
    }

    function compareBatches() {
        // Add batch comparison logic
    }

    function saveAlertSettings() {
        // Add alert settings logic
    }

    // Initialize charts on page load
    document.addEventListener('DOMContentLoaded', initializeCharts);
</script>
<?php include_once("../reusable/footer.php"); ?>

</html>
