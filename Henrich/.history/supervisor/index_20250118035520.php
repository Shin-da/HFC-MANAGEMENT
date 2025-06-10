<?php
require '../session/session.php';
require '../database/dbconnect.php';

// Get current date
$today = date('Y-m-d');

// Initialize arrays and variables
$metrics = [
    'daily' => [
        'orders' => 0,
        'revenue' => 0,
        'avg_order_value' => 0
    ],
    'total' => [
        'orders' => 0,
        'revenue' => 0,
        'customers' => 0
    ],
    'inventory' => [
        'low_stock' => 0,
        'out_of_stock' => 0
    ]
];

// Fetch metrics data
$metrics['daily']['orders'] = $conn->query("SELECT COUNT(*) FROM customerorder WHERE DATE(orderdate) = '$today'")->fetch_row()[0];
$metrics['daily']['revenue'] = $conn->query("SELECT SUM(ordertotal) FROM customerorder WHERE DATE(orderdate) = '$today'")->fetch_row()[0];
$metrics['total']['orders'] = $conn->query("SELECT COUNT(*) FROM orderlog")->fetch_row()[0];
$metrics['total']['revenue'] = $conn->query("SELECT SUM(productprice * quantity) FROM orderlog")->fetch_row()[0];
$metrics['total']['customers'] = $conn->query("SELECT COUNT(DISTINCT customername) FROM customerorder")->fetch_row()[0];
$metrics['inventory']['low_stock'] = $conn->query("SELECT COUNT(*) FROM inventory WHERE availablequantity <= 5")->fetch_row()[0];
$metrics['inventory']['out_of_stock'] = $conn->query("SELECT COUNT(*) FROM inventory WHERE availablequantity = 0")->fetch_row()[0];

// Order status counts
$order_status = [
    'pending' => $conn->query("SELECT COUNT(*) FROM customerorder WHERE status = 'Pending'")->fetch_row()[0],
    'processing' => $conn->query("SELECT COUNT(*) FROM customerorder WHERE status = 'Processing'")->fetch_row()[0],
    'completed' => $conn->query("SELECT COUNT(*) FROM customerorder WHERE status = 'Completed'")->fetch_row()[0]
];

// Top performing products
$top_products = $conn->query("
    SELECT 
        p.productname,
        SUM(o.quantity) as total_sold,
        SUM(o.quantity * o.productprice) as revenue,
        i.availablequantity as current_stock
    FROM orderlog o
    JOIN productlist p ON o.productcode = p.productcode
    JOIN inventory i ON p.productcode = i.productcode
    WHERE MONTH(o.orderdate) = MONTH(CURRENT_DATE)
    GROUP BY p.productname, i.availablequantity
    ORDER BY total_sold DESC
    LIMIT 5
")->fetch_all(MYSQLI_ASSOC);

// Recent orders
$recent_orders = [
    'walk_in' => $conn->query("
        SELECT orderdate, customername, ordertotal, status
        FROM customerorder 
        WHERE ordertype = 'Walk-in'
        ORDER BY orderdate DESC LIMIT 5
    ")->fetch_all(MYSQLI_ASSOC),
    'delivery' => $conn->query("
        SELECT orderdate, customername, ordertotal, status
        FROM customerorder 
        WHERE ordertype = 'Delivery'
        ORDER BY orderdate DESC LIMIT 5
    ")->fetch_all(MYSQLI_ASSOC)
];

// Low stock alerts
$low_stock_items = $conn->query("
    SELECT 
        productname,
        availablequantity,
        CASE 
            WHEN availablequantity <= 0 THEN 'danger'
            WHEN availablequantity <= 5 THEN 'warning'
            ELSE 'normal'
        END as alert_level
    FROM inventory
    WHERE availablequantity <= 10
    ORDER BY availablequantity ASC
")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Supervisor Dashboard</title>
    <?php require '../reusable/header.php'; ?>
    <!-- Add any additional CSS/JS here -->
</head>
<body>
    <?php require '../reusable/sidebar.php'; ?>
    <section class="panel">
        <?php require '../reusable/navbarNoSearch.html'; ?>
        
        <!-- Main Dashboard Grid -->
        <div class="dashboard-grid">
            <!-- Key Metrics Section -->
            <div class="metrics-overview">
                <!-- Daily Performance -->
                <div class="metric-card">
                    <h3>Today's Performance</h3>
                    <div class="metric-value">₱<?php echo number_format($metrics['daily']['revenue'], 2); ?></div>
                    <div class="metric-label"><?php echo $metrics['daily']['orders']; ?> Orders</div>
                </div>
                
                <!-- Inventory Alerts -->
                <div class="metric-card <?php echo $metrics['inventory']['low_stock'] > 0 ? 'warning' : ''; ?>">
                    <h3>Inventory Alerts</h3>
                    <div class="metric-value"><?php echo $metrics['inventory']['low_stock']; ?></div>
                    <div class="metric-label">Low Stock Items</div>
                </div>
                
                <!-- Order Status -->
                <div class="metric-card">
                    <h3>Order Status</h3>
                    <div class="status-grid">
                        <div class="status-item pending">
                            <span class="count"><?php echo $order_status['pending']; ?></span>
                            <span class="label">Pending</span>
                        </div>
                        <div class="status-item processing">
                            <span class="count"><?php echo $order_status['processing']; ?></span>
                            <span class="label">Processing</span>
                        </div>
                        <div class="status-item completed">
                            <span class="count"><?php echo $order_status['completed']; ?></span>
                            <span class="label">Completed</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Products Section -->
            <div class="top-products-section">
                <!-- Content for top products -->
            </div>

            <!-- Recent Orders Section -->
            <div class="recent-orders-section">
                <!-- Content for recent orders -->
            </div>

            <!-- Low Stock Alerts Section -->
            <div class="alerts-section">
                <!-- Content for low stock alerts -->
            </div>
        </div>
    </section>

    <?php require '../reusable/footer.php'; ?>
</body>
</html>