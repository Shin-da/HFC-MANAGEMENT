<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';
require_once './access_control.php';

// Configure page
Page::setTitle('Dashboard | Supervisor');
Page::setBodyClass('supervisor-body');
Page::set('current_page', 'dashboard');
Page::addScript('../assets/js/dashboard.js');
Page::addScript('../assets/js/supervisor.js');

// Start output buffering
ob_start();
?>

<!-- Your page content here -->
<div class="dashboard-wrapper">
    <!-- Dashboard Content -->
    <div class="dashboard-wrapper animate__animated animate__fadeIn">
        <!-- Dashboard header -->
        <div class="dashboard-header">
            <h1>Supervisor Dashboard</h1>
            <div class="date-filter">
                <select id="timeRange" onchange="updateDashboard(this.value)">
    COALESCE(i.availablequantity, 0) as availablequantity
FROM products p
LEFT JOIN inventory i ON p.productcode = i.productcode
WHERE COALESCE(i.availablequantity, 0) < 5 
AND COALESCE(i.availablequantity, 0) > 0";

$result = $conn->query($sql);

// For metrics query, use proper COALESCE
$metrics_query = "SELECT 
    (SELECT COUNT(*) FROM inventory WHERE COALESCE(availablequantity, 0) <= 5) as low_stock_count,
    (SELECT COUNT(*) FROM inventory WHERE COALESCE(availablequantity, 0) = 0) as out_of_stock_count,
    (SELECT COUNT(*) FROM customerorder WHERE DATE(orderdate) = CURRENT_DATE) as today_orders,
    (SELECT COALESCE(SUM(ordertotal), 0) FROM customerorder WHERE DATE(orderdate) = CURRENT_DATE) as today_revenue,
    -- New online metrics
    (SELECT COUNT(*) FROM customerorder 
     WHERE ordertype = 'Online' AND DATE(orderdate) = CURRENT_DATE) as today_online_orders,
    (SELECT COALESCE(SUM(ordertotal), 0) FROM customerorder 
     WHERE ordertype = 'Online' AND DATE(orderdate) = CURRENT_DATE) as today_online_revenue,
    (SELECT COUNT(DISTINCT customerid) FROM customeraccount 
     WHERE accounttype = 'Customer' AND accountstatus = 'Active') as active_online_customers";

$dashboard_data = [
    'metrics' => $conn->query($metrics_query)->fetch_assoc(),

    // Enhanced sales trends with channel split
    'sales_trends' => $conn->query("
        SELECT 
            DATE(ol.orderdate) as date,
            COUNT(DISTINCT ol.orderid) as order_count,
            SUM(ol.quantity * ol.productprice) as daily_sales,
            COUNT(DISTINCT CASE WHEN co.ordertype = 'Online' THEN ol.orderid END) as online_orders,
            SUM(CASE WHEN co.ordertype = 'Online' THEN ol.quantity * ol.productprice ELSE 0 END) as online_sales
        FROM orderlog ol
        JOIN customerorder co ON ol.orderid = co.orderid
        WHERE ol.orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
        GROUP BY DATE(ol.orderdate)
        ORDER BY date
    ")->fetch_all(MYSQLI_ASSOC),

    // Product demand prediction
    'product_predictions' => $conn->query("
        SELECT 
            p.productcode,
            p.productname,
            i.availablequantity,
            COUNT(ol.orderid) as monthly_orders,
            SUM(ol.quantity) as monthly_quantity,
            CEIL(SUM(ol.quantity) / 30 * 7) as weekly_forecast,
            CASE 
                WHEN i.availablequantity < CEIL(SUM(ol.quantity) / 30 * 7) 
                THEN 'Reorder Required'
                ELSE 'Stock Sufficient'
            END as stock_recommendation
        FROM products p
        LEFT JOIN inventory i ON p.productcode = i.productcode
        LEFT JOIN orderlog ol ON p.productcode = ol.productcode 
            AND ol.orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
        GROUP BY p.productcode, p.productname, i.availablequantity
    ")->fetch_all(MYSQLI_ASSOC),

    // Customer behavior analytics
    'customer_analytics' => $conn->query("
        SELECT 
            ca.accounttype,
            COUNT(DISTINCT co.orderid) as total_orders,
            AVG(co.ordertotal) as avg_order_value,
            COUNT(DISTINCT co.customername) as unique_customers,
            SUM(co.ordertotal) / COUNT(DISTINCT co.customername) as customer_lifetime_value
        FROM customerorder co
        LEFT JOIN customeraccount ca ON co.customername = ca.customername
        WHERE co.orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
        GROUP BY ca.accounttype
    ")->fetch_all(MYSQLI_ASSOC),

    // Online order fulfillment analytics
    'online_fulfillment' => $conn->query("
        SELECT 
            status,
            COUNT(*) as order_count,
            AVG(TIMESTAMPDIFF(HOUR, orderdate, IFNULL(datecompleted, NOW()))) as avg_fulfillment_hours
        FROM customerorder
        WHERE ordertype = 'Delivery'
        AND orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)
        GROUP BY status
    ")->fetch_all(MYSQLI_ASSOC),

    // Add category performance data
    'category_performance' => $conn->query("
        SELECT 
            p.productcategory,
            COUNT(ol.orderid) as order_count,
            SUM(ol.quantity * ol.productprice) as revenue
        FROM products p
        LEFT JOIN orderlog ol ON p.productcode = ol.productcode
        WHERE ol.orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
        GROUP BY p.productcategory
        ORDER BY revenue DESC
    ")->fetch_all(MYSQLI_ASSOC),

    // Add inventory status data
    'inventory_status' => $conn->query("
        SELECT 
            i.productcode,
            p.productname,
            p.productcategory,
            i.availablequantity,
            COUNT(ol.orderid) as monthly_demand,
            SUM(ol.quantity) as total_quantity_sold
        FROM inventory i
        JOIN products p ON i.productcode = p.productcode
        LEFT JOIN orderlog ol ON p.productcode = ol.productcode
            AND ol.orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
        GROUP BY i.productcode, p.productname, p.productcategory, i.availablequantity
        ORDER BY total_quantity_sold DESC
    ")->fetch_all(MYSQLI_ASSOC),

    // ...existing code...

    // Add recent orders data
    'recent_orders' => [
        'walk_in' => $conn->query("
            SELECT 
                orderdate,
                customername,
                ordertotal,
                status,
                timeoforder,
                orderdescription,
                ordertype
            FROM customerorder 
            WHERE ordertype = 'Walk-in'
            AND orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)
            ORDER BY orderdate DESC, timeoforder DESC 
            LIMIT 5
        ")->fetch_all(MYSQLI_ASSOC),

        'online' => $conn->query("
            SELECT 
                orderdate,
                customername,
                ordertotal,
                status,
                timeoforder,
                orderdescription,
                ordertype
            FROM customerorder 
            WHERE ordertype IN ('Online', 'Delivery')
            AND orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)
            ORDER BY orderdate DESC, timeoforder DESC 
            LIMIT 5
        ")->fetch_all(MYSQLI_ASSOC),

        'delivery' => $conn->query("
            SELECT 
                orderdate,
                customername,
                ordertotal,
                status,
                timeoforder,
                orderdescription,
                ordertype
            FROM customerorder 
            WHERE ordertype = 'Delivery'
            AND orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)
            ORDER BY orderdate DESC, timeoforder DESC 
            LIMIT 5
        ")->fetch_all(MYSQLI_ASSOC)
    ]
];

// Error handling for empty results
foreach ($dashboard_data as $key => $value) {
    if (is_null($value)) {
        $dashboard_data[$key] = [];
    }
}

// Start output buffering for content
ob_start();
?>

<!-- Dashboard Content -->
<div class="dashboard-wrapper animate__animated animate__fadeIn">
    <!-- Dashboard header -->
    <div class="dashboard-header">
        <h1>Supervisor Dashboard</h1>
        <div class="date-filter">
            <select id="timeRange" onchange="updateDashboard(this.value)">
                <option value="7">Last 7 Days</option>
                <option value="30" selected>Last 30 Days</option>
                <option value="90">Last 90 Days</option>
            </select>
        </div>
    </div>

    <!-- Key Metrics Section -->
    <div class="metrics-overview animate__animated animate__fadeInUp">
        <div class="stats-grid">
            <div class="stat-card warning">
                <div class="stat-icon"><i class='bx bx-error-circle'></i></div>
                <div class="stat-content">
                    <h3>Low Stock</h3>
                    <p class="stat-value"><?php echo $dashboard_data['metrics']['low_stock_count']; ?></p>
                    <p class="stat-label">Products</p>
                </div>
            </div>
            
            <div class="stat-card danger">
                <div class="stat-icon"><i class='bx bx-x-circle'></i></div>
                <div class="stat-content">
                    <h3>Out of Stock</h3>
                    <p class="stat-value"><?php echo $dashboard_data['metrics']['out_of_stock_count']; ?></p>
                    <p class="stat-label">Products</p>
                </div>
            </div>

            <div class="stat-card success">
                <div class="stat-icon"><i class='bx bx-check-circle'></i></div>
                <div class="stat-content">
                    <h3>Today's Orders</h3>
                    <p class="stat-value"><?php echo $dashboard_data['metrics']['today_orders']; ?></p>
                    <p class="stat-label">₱<?php echo number_format($dashboard_data['metrics']['today_revenue'], 2); ?></p>
                </div>
            </div>

            <div class="stat-card info">
                <div class="stat-icon"><i class='bx bx-shopping-bag'></i></div>
                <div class="stat-content">
                    <h3>Online Sales</h3>
                    <p class="stat-value"><?php echo $dashboard_data['metrics']['today_online_orders']; ?></p>
                    <p class="stat-label">₱<?php echo number_format($dashboard_data['metrics']['today_online_revenue'], 2); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Dashboard Content -->
    <div class="dashboard-grid">
        <!-- Sales Overview -->
        <div class="dashboard-section wide">
            <div class="section-header">
                <h2>Sales Overview</h2>
                <div class="section-actions">
                    <button class="btn-icon" onclick="exportSalesData()">
                        <i class='bx bx-download'></i>
                    </button>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="salesTrendsChart"></canvas>
            </div>
        </div>

        <!-- Category Performance -->
        <div class="dashboard-section">
            <h2>Category Performance</h2>
            <div class="chart-container">
                <canvas id="categoryPerformanceChart"></canvas>
            </div>
        </div>

        <!-- Inventory Health -->
        <div class="dashboard-section">
            <h2>Inventory Status</h2>
            <div class="chart-container">
                <canvas id="inventoryHealthChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Orders Section -->
    <div class="orders-section">
        <div class="section-header">
            <h2>Recent Orders</h2>
            <div class="tab-navigation">
                <button class="tab-btn active" onclick="showOrders('walk-in')">Walk-in</button>
                <button class="tab-btn" onclick="showOrders('online')">Online</button>
                <button class="tab-btn" onclick="showOrders('delivery')">Delivery</button>
            </div>
        </div>
        
        <!-- Rest of the orders section remains the same -->
        <div class="grid-container grid-3">
            <div class="card">
                <h3 class="section-title">Recent Walk-in Orders</h3>
                <div class="orders-list scrollbar-thin">
                    <?php if (!empty($dashboard_data['recent_orders']['walk_in'])): ?>
                        <?php foreach ($dashboard_data['recent_orders']['walk_in'] as $order): ?>
                            <div class="order-item">
                                <div class="order-header">
                                    <span class="order-date">
                                        <?php echo date('M d, Y', strtotime($order['orderdate'])); ?>
                                        <small><?php echo date('h:i A', strtotime($order['timeoforder'])); ?></small>
                                    </span>
                                    <span class="order-status <?php echo strtolower($order['status']); ?>">
                                        <?php echo $order['status']; ?>
                                    </span>
                                </div>
                                <div class="order-details">
                                    <div class="customer-info">
                                        <span class="customer-name"><?php echo $order['customername']; ?></span>
                                        <span class="order-amount">₱<?php echo number_format($order['ordertotal'], 2); ?></span>
                                    </div>
                                    <div class="order-items">
                                        <?php echo $order['orderdescription']; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-data">No recent walk-in orders</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card">
                <h3 class="section-title">Recent Online Orders</h3>
                <div class="orders-list scrollbar-thin">
                    <?php if (!empty($dashboard_data['recent_orders']['online'])): ?>
                        <?php foreach ($dashboard_data['recent_orders']['online'] as $order): ?>
                            <div class="order-item">
                                <div class="order-header">
                                    <span class="order-date">
                                        <?php echo date('M d, Y', strtotime($order['orderdate'])); ?>
                                        <small><?php echo date('h:i A', strtotime($order['timeoforder'])); ?></small>
                                    </span>
                                    <span class="order-status <?php echo strtolower($order['status']); ?>">
                                        <?php echo $order['status']; ?>
                                    </span>
                                </div>
                                <div class="order-details">
                                    <div class="customer-info">
                                        <span class="customer-name"><?php echo $order['customername']; ?></span>
                                        <span class="order-amount">₱<?php echo number_format($order['ordertotal'], 2); ?></span>
                                    </div>
                                    <div class="order-items">
                                        <?php echo $order['orderdescription']; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-data">No recent online orders</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card">
                <h3 class="section-title">Recent Delivery Orders</h3>
                <div class="orders-list scrollbar-thin">
                    <?php if (!empty($dashboard_data['recent_orders']['delivery'])): ?>
                        <?php foreach ($dashboard_data['recent_orders']['delivery'] as $order): ?>
                            <div class="order-item">
                                <div class="order-header">
                                    <span class="order-date">
                                        <?php echo date('M d, Y', strtotime($order['orderdate'])); ?>
                                        <small><?php echo date('h:i A', strtotime($order['timeoforder'])); ?></small>
                                    </span>
                                    <span class="order-status <?php echo strtolower($order['status']); ?>">
                                        <?php echo $order['status']; ?>
                                    </span>
                                </div>
                                <div class="order-details">
                                    <div class="customer-info">
                                        <span class="customer-name"><?php echo $order['customername']; ?></span>
                                        <span class="order-amount">₱<?php echo number_format($order['ordertotal'], 2); ?></span>
                                    </div>
                                    <div class="order-items">
                                        <?php echo $order['orderdescription']; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-data">No recent delivery orders</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const dashboardData = <?php echo json_encode($dashboard_data); ?>;
</script>

<?php
// Render the page
Page::render(ob_get_clean());
?>