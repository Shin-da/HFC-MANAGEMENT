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
        </div>
    </section>

    <script src="../resources/js/table.js"></script>
    <script src="../resources/js/stocklevel.js"></script>
    <?php include_once("../reusable/footer.php"); ?>
</body>
</html>