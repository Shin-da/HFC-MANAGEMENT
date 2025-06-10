<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Calculate inventory statistics
try {
    // Get total products
    $total_products = $conn->query("SELECT COUNT(*) as total FROM inventory")->fetch_assoc()['total'];
    
    // Get low stock items (where available quantity is below reorder point)
    $low_stock = $conn->query("
        SELECT COUNT(*) as low_stock 
        FROM inventory 
        WHERE availablequantity <= 
            CASE 
                WHEN reorder_point IS NOT NULL THEN reorder_point 
                ELSE 10 
            END
    ")->fetch_assoc()['low_stock'];
    
    // Get out of stock items
    $out_of_stock = $conn->query("
        SELECT COUNT(*) as out_of_stock 
        FROM inventory 
        WHERE availablequantity = 0
    ")->fetch_assoc()['out_of_stock'];
    
    // Calculate total inventory value
    $total_value = $conn->query("
        SELECT SUM(i.availablequantity * p.price) as total_value
        FROM inventory i
        JOIN products p ON i.product_id = p.id
    ")->fetch_assoc()['total_value'];

    // Combine stats into array
    $stats = array(
        'total_products' => $total_products,
        'low_stock' => $low_stock,
        'out_of_stock' => $out_of_stock,
        'total_value' => $total_value ?: 0 // Use 0 if null
    );

} catch (Exception $e) {
    // Log error and set default values if query fails
    error_log("Error calculating inventory stats: " . $e->getMessage());
    $stats = array(
        'total_products' => 0,
        'low_stock' => 0,
        'out_of_stock' => 0,
        'total_value' => 0
    );
}

// Get inventory value trends for the last 7 days
$trendsQuery = "SELECT 
    DATE(dateencoded) as date,
    SUM(totalpieces * productprice) as daily_value
FROM stockmovement sm
JOIN productlist p ON sm.productcode = p.productcode
WHERE dateencoded >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)
GROUP BY DATE(dateencoded)
ORDER BY dateencoded";

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
    <link rel="stylesheet" type="text/css" href="../resources/css/stocklevel.css">
    <link rel="stylesheet" type="text/css" href="../resources/css/table.css">
    <link rel="stylesheet" type="text/css" href="../resources/css/shared-dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const trendsData = <?php echo $trendsDataJSON; ?>;
    </script>
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
                    <div id="valueTrendsChart"></div>
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
        </div>
    </section>

    <script src="../resources/js/table.js"></script>
    <script src="../resources/js/stocklevel.js"></script>
    <?php include_once("../reusable/footer.php"); ?>
</body>
</html>
