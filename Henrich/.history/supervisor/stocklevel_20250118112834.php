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