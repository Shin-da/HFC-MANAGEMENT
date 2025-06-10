<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Stock Movement</title>
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
                <h2>Stock Movement</h2>
                <div class="date-display"><?php echo date('l, F jS Y'); ?></div>
            </div>

            <!-- Filter Controls -->
            <div class="filter-section">
                <div class="date-range">
                    <input type="date" id="start-date" onchange="filterByDate()">
                    <input type="date" id="end-date" onchange="filterByDate()">
                </div>
                <div class="movement-type">
                    <select id="movement-filter" onchange="filterByMovement()">
                        <option value="">All Movements</option>
                        <option value="in">Stock In</option>
                        <option value="out">Stock Out</option>
                        <option value="transfer">Transfer</option>
                    </select>
                </div>
                <div class="search-box">
                    <input type="text" id="movement-search" placeholder="Search movements...">
                </div>
            </div>

            <!-- Movement Table -->
            <div class="table-responsive">
                <?php include 'stockmovement.table.php'; ?>
            </div>
        </div>
    </section>

    <script src="../resources/js/stockmovement.js"></script>
    <?php include_once("../reusable/footer.php"); ?>
</body>
</html>