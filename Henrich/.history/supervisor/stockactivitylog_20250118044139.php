<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Stock Activity Log</title>
    <?php require '../reusable/header.php'; ?>
    <link rel="stylesheet" type="text/css" href="../resources/css/table.css">
</head>
<body>
    <?php include '../reusable/sidebar.php'; ?>
    <section class="panel">
        <?php include '../reusable/navbarNoSearch.html'; ?>

        <div class="container-fluid">
            <!-- Header Section -->
            <div class="page-header">
                <h2>Stock Activity Log</h2>
                <div class="date-display"><?php echo date('l, F jS Y'); ?></div>
            </div>

            <!-- Activity Summary -->
            <div class="activity-summary">
                <?php include 'stockactivity.summary.php'; ?>
            </div>

            <!-- Filter Controls -->
            <div class="filter-section">
                <div class="date-filter">
                    <input type="date" id="log-start-date" onchange="filterLogs()">
                    <input type="date" id="log-end-date" onchange="filterLogs()">
                </div>
                <div class="activity-type">
                    <select id="activity-filter" onchange="filterByActivity()">
                        <option value="">All Activities</option>
                        <option value="add">Stock Addition</option>
                        <option value="remove">Stock Removal</option>
                        <option value="update">Stock Update</option>
                        <option value="transfer">Stock Transfer</option>
                    </select>
                </div>
                <div class="user-filter">
                    <select id="user-filter" onchange="filterByUser()">
                        <option value="">All Users</option>
                        <?php
                        $users = $conn->query("SELECT DISTINCT encoder FROM stockactivitylog");
                        while($user = $users->fetch_assoc()) {
                            echo "<option value='".$user['encoder']."'>".$user['encoder']."</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <!-- Activity Log Table -->
            <div class="table-responsive">
                <?php include 'stockactivity.table.php'; ?>
            </div>
        </div>
    </section>

    <script src="../resources/js/stockactivity.js"></script>
    <?php include_once("../reusable/footer.php"); ?>
</body>
</html>