<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Calculate inventory statistics
try {
    $conn->begin_transaction();

    // Modified query to match actual table structure
    $inventoryStats = $conn->query("
        SELECT 
            COUNT(DISTINCT i.productcode) as total_products,
            SUM(CASE WHEN i.availablequantity <= 10 THEN 1 ELSE 0 END) as low_stock,
            SUM(CASE WHEN i.availablequantity = 0 THEN 1 ELSE 0 END) as out_of_stock,
            SUM(i.availablequantity * COALESCE(p.unit_price, 0)) as total_value
        FROM inventory i
        LEFT JOIN productlist p ON i.productcode = p.productcode
    ")->fetch_assoc();

    // Add fallback values if query fails
    if (!$inventoryStats) {
        $inventoryStats = [
            'total_products' => 0,
            'low_stock' => 0,
            'out_of_stock' => 0,
            'total_value' => 0
        ];
    }

    // Add real-time monitoring
    echo "<script>
        function checkStockLevels() {
            fetch('check_stock_levels.php')
                .then(response => response.json())
                .then(data => {
                    if (data.alerts.length > 0) {
                        notifyLowStock(data.alerts);
                    }
                });
        }
        setInterval(checkStockLevels, 300000); // Check every 5 minutes
    </script>";

    // Change productlist to products in the trends query
    $trendsQuery = "SELECT 
        DATE(sm.dateencoded) as date,
        SUM(CAST(sm.totalpieces AS DECIMAL) * CAST(p.unit_price AS DECIMAL)) as daily_value
    FROM stockmovement sm
    JOIN productlist p ON sm.productcode = p.productcode
    WHERE sm.dateencoded >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)
    GROUP BY DATE(sm.dateencoded)
    ORDER BY date";

    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    error_log("Inventory stats error: " . $e->getMessage());
    // Set default values on error
    $inventoryStats = [
        'total_products' => 0,
        'low_stock' => 0,
        'out_of_stock' => 0,
        'total_value' => 0
    ];
}

// Get inventory value trends for the last 7 days
$trendsQuery = "SELECT 
    DATE(sm.dateencoded) as date,
    SUM(CAST(sm.totalpieces AS DECIMAL) * CAST(p.unit_price AS DECIMAL)) as daily_value
FROM stockmovement sm
JOIN products p ON sm.productcode = p.productcode
WHERE sm.dateencoded >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)
GROUP BY DATE(sm.dateencoded)
ORDER BY date";

$trendsResult = $conn->query($trendsQuery);
$trendsData = [
    'dates' => [],
    'values' => []
];

if ($trendsResult) {
    while ($row = $trendsResult->fetch_assoc()) {
        $trendsData['dates'][] = $row['date'];
        $trendsData['values'][] = $row['daily_value'];
    }
}

// Convert PHP array to JavaScript object
$trendsDataJSON = json_encode($trendsData);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Stock Management</title>
    <?php require '../reusable/header.php'; ?>
    <?php require '../reusable/header.alerts.php'; ?>
    <link rel="stylesheet" type="text/css" href="../resources/css/stocklevel.css">
    <link rel="stylesheet" type="text/css" href="../resources/css/table.css">
    <link rel="stylesheet" type="text/css" href="../resources/css/shared-dashboard.css">
    <link rel="stylesheet" type="text/css" href="../resources/css/stock-pages.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const trendsData = <?php echo $trendsDataJSON; ?>;
    </script>
</head>

<body>
    <?php require '../reusable/sidebar.php'; ?>
    <?php include '../reusable/navbar.html'; ?>
    <section class="panel inevntory-theme">

        <div class="container-fluid dashboard-container">
            <!-- Stats Container -->
            <div class="stats-container">
                <div class="stat-card">
                    <h3>Total Products</h3>
                    <div class="value"><?= isset($inventoryStats['total_products']) ? $inventoryStats['total_products'] : 0 ?></div>
                </div>
                <div class="stat-card">
                    <h3>Low Stock Items</h3>
                    <div class="value"><?= isset($inventoryStats['low_stock']) ? $inventoryStats['low_stock'] : 0 ?></div>
                </div>
                <div class="stat-card">
                    <h3>Out of Stock</h3>
                    <div class="value"><?= isset($inventoryStats['out_of_stock']) ? $inventoryStats['out_of_stock'] : 0 ?></div>
                </div>
                <div class="stat-card">
                    <h3>Total Value</h3>
                    <div class="value">₱<?= isset($inventoryStats['total_value']) ? number_format($inventoryStats['total_value'], 2) : 0 ?></div>
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

            <div class="table-container">
                <!-- Inventory Management Section -->
                <div class="container-fluid inventory-section">
                    <div class="table-header">
                        <div class="title-section">
                            <h2>INVENTORY</h2>
                            <span class="subtitle">Stock Management Dashboard</span>
                        </div>
                        <div class="action-buttons">
                            <button class="btn export-btn" onclick="exportToExcel()">
                                <i class="bx bx-export"></i> Export to Excel
                            </button>
                            <button class="btn export-btn" onclick="exportToPDF()">
                                <i class="bx bx-file"></i> Export to PDF
                            </button>
                            <a class="btn add-btn" href="add.stockmovement.php">
                                <i class="bx bx-plus"></i> Encode to Inventory
                            </a>
                        </div>
                    </div>

                    <!-- Value Trends Chart -->
                    <div class="stock-trends">
                        <div class="table-header">
                            <h2>Inventory Value Trends</h2>
                        </div>
                        <canvas id="valueTrendsChart" style="width: 100%; height: 300px;"></canvas>
                    </div>

                    <!-- Stock Movement History -->
                    <div class="stock-history">
                        <div class="table-header">
                            <h2>Recent Stock Movements</h2>
                        </div>
                        <div class="table-responsive">
                            <?php include 'stockmovement.history.php'; ?>
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
                                while ($cat = $categories->fetch_assoc()) {
                                    echo "<option value='" . $cat['productcategory'] . "'>" . $cat['productcategory'] . "</option>";
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
            </div>
        </div>

        <?php include_once("../reusable/footer.php"); ?>
    </section>

    <script src="../resources/js/table.js"></script>
    <script src="../resources/js/stocklevel.js"></script>
    <script>
        function saveAlertSettings() {
            const threshold = document.getElementById('lowStockThreshold').value;
            document.cookie = `lowStockThreshold=${threshold};path=/;max-age=31536000`;

            Alerts.toast('Alert settings saved successfully!');

            // Check stock levels
            const boxes = <?= $stats['total_boxes'] ?>;
            if (boxes <= threshold) {
                Alerts.inventory.lowStock('Current stock', boxes, threshold);
            }
        }

        function confirmDelete(productId) {
            Alerts.confirm(
                'Delete Product',
                'Are you sure you want to delete this product?',
                () => deleteProduct(productId)
            );
        }

        function stockUpdate(type, quantity, product) {
            Alerts.inventory.updateSuccess(type);
        }
    </script>
</body>

</html>