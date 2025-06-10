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
    <link rel="stylesheet" href="../resources/css/dashboard.css">
    <link rel="stylesheet" href="../resources/css/stock-pages.css">
</head>
<body>
    <?php include '../reusable/sidebar.php'; ?>
    <section class="panel">
        <?php include '../reusable/navbarNoSearch.html'; ?>
        
        <div class="dashboard-container animate-slide-in">
            <!-- Stock Overview -->
            <div class="stock-overview">
                <div class="chart-card">
                    <div class="chart-header">
                        <h3>Stock Levels Overview</h3>
                    </div>
                    <div class="chart-container">
                        <?php include 'stocklevel.chart.php'; ?>
                    </div>
                </div>

                <div class="stock-alerts">
                    <h3>Stock Alerts</h3>
                    <?php include 'stocklevel.alert.php'; ?>
                </div>
            </div>

            <!-- Inventory Management -->
            <div class="inventory-section">
                    <span class="subtitle">Stock Management Dashboard</span>
                </div>
                <div class="action-buttons">
                    <a class="btn add-btn" href="add.stockmovement.php">
                        <i class="bx bx-plus"></i> Encode to Inventory
                    </a>
                </div>
            </div>

            <!-- Search and Filter Section -->
            <div class="search-filter-section">
                <div class="search-box">
                    <input type="text" id="general-search" onkeyup="searchTable()" 
                           placeholder="Search inventory...">
                </div>
                <div class="filter-controls">
                    <select id="category-filter" onchange="filterByCategory()">
                        <option value="">All Categories</option>
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