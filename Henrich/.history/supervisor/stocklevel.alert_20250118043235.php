<?php
// Check database connection
if (!isset($conn)) {
    require '../database/dbconnect.php';
}

// Get low stock and out of stock items
$lowStockItems = $conn->query("SELECT * FROM inventory WHERE onhandquantity > 0 AND onhandquantity <= 10 ORDER BY onhandquantity ASC");
$outOfStockItems = $conn->query("SELECT * FROM inventory WHERE onhandquantity = 0 ORDER BY productname ASC");

// Check for query errors
if (!$lowStockItems || !$outOfStockItems) {
    echo "Error fetching stock data: " . $conn->error;
    exit;
}
?>

<div class="stock-alerts-container">
    <h3 class="alert-header">Stock Alerts</h3>
    
    <!-- Out of Stock Alerts -->
    <?php if ($outOfStockItems->num_rows > 0): ?>
        <div class="alert-section">
            <h4>Out of Stock Items (<?php echo $outOfStockItems->num_rows; ?>)</h4>
            <div class="alert-list">
                <?php while ($item = $outOfStockItems->fetch_assoc()): ?>
                    <div class="alert-item out-of-stock">
                        <i class='bx bx-error-circle'></i>
                        <span class="item-name"><?php echo htmlspecialchars($item['productname']); ?></span>
                        <span class="stock-count">0 units</span>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Low Stock Alerts -->
    <?php if ($lowStockItems->num_rows > 0): ?>
        <div class="alert-section">
            <h4>Low Stock Items (<?php echo $lowStockItems->num_rows; ?>)</h4>
            <div class="alert-list">
                <?php while ($item = $lowStockItems->fetch_assoc()): ?>
                    <div class="alert-item low-stock">
                        <i class='bx bx-error'></i>
                        <span class="item-name"><?php echo htmlspecialchars($item['productname']); ?></span>
                        <span class="stock-count"><?php echo $item['onhandquantity']; ?> units left</span>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($outOfStockItems->num_rows == 0 && $lowStockItems->num_rows == 0): ?>
        <div class="no-alerts">
            <i class='bx bx-check-circle'></i>
            <p>All stock levels are normal</p>
        </div>
    <?php endif; ?>
</div>

<style>
.stock-alerts-container {
    background: #fff;
    border-radius: 8px;
    padding: 15px;
    width: 100%;
    max-width: 400px;
}

.alert-header {
    color: #333;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

.alert-section {
    margin-bottom: 20px;
}

.alert-section h4 {
    color: #666;
    margin-bottom: 10px;
    font-size: 0.9rem;
}

.alert-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.alert-item {
    display: flex;
    align-items: center;
    padding: 10px;
    border-radius: 4px;
    gap: 10px;
    font-size: 0.9rem;
}

.alert-item.out-of-stock {
    background-color: #ffe5e5;
    color: #d63031;
}

.alert-item.low-stock {
    background-color: #fff3cd;
    color: #856404;
}

.alert-item i {
    font-size: 1.2rem;
}

.item-name {
    flex: 1;
}

.stock-count {
    font-weight: bold;
}

.no-alerts {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px;
    color: #2ecc71;
    text-align: center;
}

.no-alerts i {
    font-size: 2rem;
    margin-bottom: 10px;
}
</style>




