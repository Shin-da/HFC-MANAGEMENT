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
                <div class="section-header">
                    <h2>Inventory Management</h2>
                    <div class="actions">
                        <a href="add.stockmovement.php" class="btn-action">
                            <i class="bx bx-plus"></i> Add Stock
                        </a>
                    </div>
                </div>

                <!-- Enhanced Search and Filters -->
                <div class="filters-container">
                    <div class="search-box">
                        <input type="text" id="inventory-search" 
                               placeholder="Search inventory..."
                               onkeyup="searchInventory()">
                    </div>
                    <div class="filter-controls">
                        <select id="category-filter" onchange="filterInventory()">
                            <option value="">All Categories</option>
                            <?php
                            $categories = $conn->query("SELECT DISTINCT productcategory FROM inventory");
                            while($cat = $categories->fetch_assoc()) {
                                echo "<option value='".$cat['productcategory']."'>".$cat['productcategory']."</option>";
                            }
                            ?>
                        </select>
                        <select id="stock-status" onchange="filterInventory()">
                            <option value="">All Stock Status</option>
                            <option value="low">Low Stock</option>
                            <option value="out">Out of Stock</option>
                            <option value="normal">Normal</option>
                        </select>
                    </div>
                </div>

                <!-- Inventory Table -->
                <div class="inventory-table">
                    <?php include 'stocklevel.table.php'; ?>
                </div>
            </div>
        </div>
    </section>

    <script src="../resources/js/table.js"></script>
    <script>
    function searchInventory() {
        // Enhanced search functionality
        /* ...search logic... */
    }

    function filterInventory() {
        // Enhanced filter functionality
        /* ...filter logic... */
    }

    // Initialize with auto-refresh
    document.addEventListener('DOMContentLoaded', () => {
        initializeInventoryPage();
        startAutoRefresh();
    });
    </script>
</body>
</html>