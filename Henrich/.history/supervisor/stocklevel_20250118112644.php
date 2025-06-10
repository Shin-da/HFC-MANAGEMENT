<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Stock Management</title>
    <?php require '../reusable/header.php'; ?>
    <link rel="stylesheet" type="text/css" href="../resources/css/table.css">
    <link rel="stylesheet" type="text/css" href="../resources/css/shared-dashboard.css">
</head>
<body>
    <?php include '../reusable/sidebar.php'; ?>
    <section class="panel">
        <?php include '../reusable/navbarNoSearch.html'; ?>
        
        <div class="container-fluid dashboard-container">
            <!-- Stats Container -->
            <div class="stats-container">
                <div class="stat-card">
                    <h3>Total Products</h3>
                    <div class="value"><?= $stats['total_products'] ?></div>
                </div>
                <div class="stat-card">
                    <h3>Low Stock Items</h3>
                    <div class="value"><?= $stats['low_stock'] ?></div>
                </div>
                <div class="stat-card">
                    <h3>Out of Stock</h3>
                    <div class="value"><?= $stats['out_of_stock'] ?></div>
                </div>
                <div class="stat-card">
                    <h3>Total Value</h3>
                    <div class="value">₱<?= number_format($stats['total_value'], 2) ?></div>
                </div>
            </div>

            <!-- Stock Overview Section -->
            <div class="stock-overview">
                <div class="stock-chart">
                    <div class="table-header">
                        <h2>Stock Levels Overview</h2>
                    </div>
                    <?php include 'stocklevel.chart.php'; ?>
                </div>
                <div class="stock-alerts">
                    <?php include 'stocklevel.alert.php'; ?>
                </div>
            </div>

            <!-- Inventory Management Section -->
            <div class="container-fluid inventory-section">
                <div class="table-header">
                    <div class="title-section">
                        <?php
                        $categories = $conn->query("SELECT DISTINCT productcategory FROM inventory");
                        while($cat = $categories->fetch_assoc()) {
                            echo "<option value='".$cat['productcategory']."'>".$cat['productcategory']."</option>";
                        }
                        ?>
                    </select>
                    <select id="stock-status" onchange="filterByStock()">
                        <option value="">All Stock Status</option>
                        <option value="low">Low Stock</option>
                        <option value="out">Out of Stock</option>
                        <option value="normal">Normal</option>
                    </select>
                </div>
            </div>

            <!-- Inventory Table -->
            <div class="table-responsive">
                <?php include 'stocklevel.table.php'; ?>
            </div>
        </div>
    </section>

    <script src="../resources/js/table.js"></script>
    <script src="../resources/js/stocklevel.js"></script>
    <?php include_once("../reusable/footer.php"); ?>
</body>
</html>