<?php
ob_start();
session_start();
require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/access_control.php';
require_once '../includes/Page.php';

// Set page title and body class
Page::setTitle('Inventory Dashboard');
Page::setBodyClass('inventory-dashboard');

// Get inventory statistics
try {
    // Total products
    $sqlTotalProducts = "SELECT COUNT(*) as total FROM products";
    $totalProductsResult = $conn->query($sqlTotalProducts);
    $totalProducts = $totalProductsResult->fetch_assoc()['total'];
    
    // Total inventory value
    $sqlInventoryValue = "SELECT SUM(availablequantity * unit_price) as total_value FROM inventory";
    $inventoryValueResult = $conn->query($sqlInventoryValue);
    $inventoryValue = $inventoryValueResult->fetch_assoc()['total_value'] ?? 0;
    
    // Low stock products
    $sqlLowStock = "SELECT COUNT(*) as count FROM inventory WHERE availablequantity <= reorder_point";
    $lowStockResult = $conn->query($sqlLowStock);
    $lowStockCount = $lowStockResult->fetch_assoc()['count'];
    
    // Out of stock products
    $sqlOutOfStock = "SELECT COUNT(*) as count FROM inventory WHERE availablequantity = 0";
    $outOfStockResult = $conn->query($sqlOutOfStock);
    $outOfStockCount = $outOfStockResult->fetch_assoc()['count'];
    
    // Recent movements
    $sqlRecentMovements = "SELECT sm.*, p.productname 
                          FROM stockmovement sm
                          JOIN products p ON sm.productcode = p.productcode
                          ORDER BY sm.dateencoded DESC LIMIT 10";
    $recentMovementsResult = $conn->query($sqlRecentMovements);
    $recentMovements = [];
    while ($row = $recentMovementsResult->fetch_assoc()) {
        $recentMovements[] = $row;
    }
    
    // Category distribution
    $sqlCategories = "SELECT productcategory, COUNT(*) as count, 
                    SUM(availablequantity) as total_quantity,
                    SUM(availablequantity * unit_price) as total_value
                    FROM inventory
                    GROUP BY productcategory
                    ORDER BY total_value DESC";
    $categoriesResult = $conn->query($sqlCategories);
    $categories = [];
    while ($row = $categoriesResult->fetch_assoc()) {
        $categories[] = $row;
    }
    
    // Top products by value
    $sqlTopProducts = "SELECT i.productcode, p.productname, i.availablequantity, 
                      i.unit_price, (i.availablequantity * i.unit_price) as total_value
                      FROM inventory i
                      JOIN products p ON i.productcode = p.productcode
                      ORDER BY total_value DESC
                      LIMIT 5";
    $topProductsResult = $conn->query($sqlTopProducts);
    $topProducts = [];
    while ($row = $topProductsResult->fetch_assoc()) {
        $topProducts[] = $row;
    }
    
    // Critical stock alerts
    $sqlStockAlerts = "SELECT i.productcode, p.productname, i.availablequantity, 
                      i.reorder_point, i.unit_price
                      FROM inventory i
                      JOIN products p ON i.productcode = p.productcode
                      WHERE i.availablequantity <= i.reorder_point
                      ORDER BY (i.availablequantity / i.reorder_point) ASC
                      LIMIT 10";
    $stockAlertsResult = $conn->query($sqlStockAlerts);
    $stockAlerts = [];
    while ($row = $stockAlertsResult->fetch_assoc()) {
        $stockAlerts[] = $row;
    }
    
} catch (Exception $e) {
    error_log("Inventory dashboard error: " . $e->getMessage());
    $error = "An error occurred while fetching inventory data: " . $e->getMessage();
}

// Page content start
ob_start();
?>

<div class="inventory-dashboard-container">
    <h1 class="dashboard-title">Inventory Dashboard</h1>
    
    <!-- Stats Overview Cards -->
    <div class="stats-cards">
        <div class="stat-card total-products">
            <div class="stat-icon">
                <i class="bx bx-package"></i>
            </div>
            <div class="stat-content">
                <h3>Total Products</h3>
                <p class="stat-value"><?= number_format($totalProducts) ?></p>
            </div>
        </div>
        
        <div class="stat-card inventory-value">
            <div class="stat-icon">
                <i class="bx bx-money"></i>
            </div>
            <div class="stat-content">
                <h3>Inventory Value</h3>
                <p class="stat-value">₱<?= number_format($inventoryValue, 2) ?></p>
            </div>
        </div>
        
        <div class="stat-card low-stock">
            <div class="stat-icon">
                <i class="bx bx-error-circle"></i>
            </div>
            <div class="stat-content">
                <h3>Low Stock Items</h3>
                <p class="stat-value"><?= number_format($lowStockCount) ?></p>
            </div>
        </div>
        
        <div class="stat-card out-of-stock">
            <div class="stat-icon">
                <i class="bx bx-x-circle"></i>
            </div>
            <div class="stat-content">
                <h3>Out of Stock</h3>
                <p class="stat-value"><?= number_format($outOfStockCount) ?></p>
            </div>
        </div>
    </div>
    
    <div class="dashboard-grid">
        <!-- Critical Stock Alerts -->
        <div class="dashboard-card stock-alerts">
            <div class="card-header">
                <h3>Critical Stock Alerts</h3>
                <a href="inventory.php?filter=low" class="view-all">View All</a>
            </div>
            <div class="card-content">
                <?php if (empty($stockAlerts)): ?>
                    <p class="no-data">No stock alerts at this time</p>
                <?php else: ?>
                    <table class="stock-alerts-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Current Stock</th>
                                <th>Reorder Point</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stockAlerts as $alert): ?>
                                <tr>
                                    <td>
                                        <div class="product-info">
                                            <span class="product-code"><?= $alert['productcode'] ?></span>
                                            <span class="product-name"><?= $alert['productname'] ?></span>
                                        </div>
                                    </td>
                                    <td><?= number_format($alert['availablequantity']) ?></td>
                                    <td><?= number_format($alert['reorder_point']) ?></td>
                                    <td>
                                        <?php 
                                        $ratio = $alert['availablequantity'] / max(1, $alert['reorder_point']);
                                        if ($alert['availablequantity'] == 0):
                                        ?>
                                            <span class="status-badge out-of-stock">Out of Stock</span>
                                        <?php elseif ($ratio <= 0.25): ?>
                                            <span class="status-badge critical">Critical</span>
                                        <?php elseif ($ratio <= 0.5): ?>
                                            <span class="status-badge warning">Warning</span>
                                        <?php else: ?>
                                            <span class="status-badge low">Low</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Recent Stock Movements -->
        <div class="dashboard-card recent-movements">
            <div class="card-header">
                <h3>Recent Stock Movements</h3>
                <a href="stockactivitylog.php" class="view-all">View All</a>
            </div>
            <div class="card-content">
                <?php if (empty($recentMovements)): ?>
                    <p class="no-data">No recent movements</p>
                <?php else: ?>
                    <table class="movements-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentMovements as $movement): ?>
                                <tr>
                                    <td><?= date('M d, Y', strtotime($movement['dateencoded'])) ?></td>
                                    <td>
                                        <div class="product-info">
                                            <span class="product-code"><?= $movement['productcode'] ?></span>
                                            <span class="product-name"><?= $movement['productname'] ?></span>
                                        </div>
                                    </td>
                                    <td><?= number_format($movement['totalpacks']) ?></td>
                                    <td>
                                        <?php if ($movement['movement_type'] == 'IN'): ?>
                                            <span class="movement-badge in">IN</span>
                                        <?php elseif ($movement['movement_type'] == 'OUT'): ?>
                                            <span class="movement-badge out">OUT</span>
                                        <?php else: ?>
                                            <span class="movement-badge adjustment">ADJUSTMENT</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Top Products by Value -->
        <div class="dashboard-card top-products">
            <div class="card-header">
                <h3>Top Products by Value</h3>
            </div>
            <div class="card-content">
                <?php if (empty($topProducts)): ?>
                    <p class="no-data">No products available</p>
                <?php else: ?>
                    <table class="top-products-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Total Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($topProducts as $product): ?>
                                <tr>
                                    <td>
                                        <div class="product-info">
                                            <span class="product-code"><?= $product['productcode'] ?></span>
                                            <span class="product-name"><?= $product['productname'] ?></span>
                                        </div>
                                    </td>
                                    <td><?= number_format($product['availablequantity']) ?></td>
                                    <td>₱<?= number_format($product['unit_price'], 2) ?></td>
                                    <td>₱<?= number_format($product['total_value'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Category Distribution -->
        <div class="dashboard-card category-distribution">
            <div class="card-header">
                <h3>Category Distribution</h3>
            </div>
            <div class="card-content">
                <?php if (empty($categories)): ?>
                    <p class="no-data">No category data available</p>
                <?php else: ?>
                    <div class="category-chart-container">
                        <canvas id="categoryChart" width="400" height="300"></canvas>
                    </div>
                    <table class="category-table">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Products</th>
                                <th>Quantity</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td><?= $category['productcategory'] ?></td>
                                    <td><?= number_format($category['count']) ?></td>
                                    <td><?= number_format($category['total_quantity']) ?></td>
                                    <td>₱<?= number_format($category['total_value'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Category distribution chart
    <?php if (!empty($categories)): ?>
    const categoryChart = document.getElementById('categoryChart');
    const categoryData = <?= json_encode($categories) ?>;
    
    new Chart(categoryChart, {
        type: 'doughnut',
        data: {
            labels: categoryData.map(item => item.productcategory),
            datasets: [{
                label: 'Total Value',
                data: categoryData.map(item => item.total_value),
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                    '#fd7e14', '#6f42c1', '#20c9a6', '#5a5c69', '#858796'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.raw || 0;
                            return label + ': ₱' + Number(value).toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }
                    }
                }
            }
        }
    });
    <?php endif; ?>
});
</script>

<style>
    .inventory-dashboard-container {
        padding: 20px;
        max-width: 1400px;
        margin: 0 auto;
    }
    
    .dashboard-title {
        margin-bottom: 20px;
        color: #333;
    }
    
    .stats-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
        grid-gap: 20px;
        margin-bottom: 20px;
    }
    
    .stat-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: center;
    }
    
    .stat-icon {
        font-size: 2.5rem;
        margin-right: 15px;
        color: #4e73df;
    }
    
    .stat-content h3 {
        margin: 0;
        font-size: 0.9rem;
        color: #5a5c69;
    }
    
    .stat-value {
        font-size: 1.5rem;
        font-weight: bold;
        margin: 5px 0 0;
        color: #333;
    }
    
    .total-products .stat-icon {
        color: #4e73df;
    }
    
    .inventory-value .stat-icon {
        color: #1cc88a;
    }
    
    .low-stock .stat-icon {
        color: #f6c23e;
    }
    
    .out-of-stock .stat-icon {
        color: #e74a3b;
    }
    
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        grid-gap: 20px;
    }
    
    .dashboard-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }
    
    .card-header {
        padding: 15px 20px;
        border-bottom: 1px solid #e3e6f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .card-header h3 {
        margin: 0;
        font-size: 1.1rem;
        color: #333;
    }
    
    .view-all {
        color: #4e73df;
        text-decoration: none;
        font-size: 0.8rem;
        font-weight: 500;
    }
    
    .card-content {
        padding: 20px;
    }
    
    table {
        width: 100%;
        border-collapse: collapse;
    }
    
    table th, table td {
        padding: 10px 15px;
        text-align: left;
        border-bottom: 1px solid #e3e6f0;
    }
    
    table th {
        font-weight: 600;
        color: #5a5c69;
        font-size: 0.85rem;
    }
    
    .product-info {
        display: flex;
        flex-direction: column;
    }
    
    .product-code {
        font-weight: 600;
        color: #4e73df;
    }
    
    .product-name {
        font-size: 0.85rem;
        color: #5a5c69;
    }
    
    .status-badge, .movement-badge {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .status-badge.out-of-stock {
        background: #ffebee;
        color: #e53935;
    }
    
    .status-badge.critical {
        background: #fff3e0;
        color: #ef6c00;
    }
    
    .status-badge.warning {
        background: #fffde7;
        color: #fbc02d;
    }
    
    .status-badge.low {
        background: #e8f5e9;
        color: #43a047;
    }
    
    .movement-badge.in {
        background: #e8f5e9;
        color: #43a047;
    }
    
    .movement-badge.out {
        background: #ffebee;
        color: #e53935;
    }
    
    .movement-badge.adjustment {
        background: #e3f2fd;
        color: #1976d2;
    }
    
    .category-chart-container {
        height: 300px;
        margin-bottom: 20px;
    }
    
    .stock-alerts, .recent-movements, .top-products, .category-distribution {
        grid-column: span 1;
    }
    
    .no-data {
        text-align: center;
        color: #858796;
        padding: 20px 0;
    }
    
    @media (max-width: 992px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
        }
        
        .stock-alerts, .recent-movements, .top-products, .category-distribution {
            grid-column: span 1;
        }
    }
</style>

<?php
$content = ob_get_clean();
Page::render($content);
?> 