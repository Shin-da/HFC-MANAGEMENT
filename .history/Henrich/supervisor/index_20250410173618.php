<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';
require_once './access_control.php';

$current_page = basename($_SERVER['PHP_SELF'], '.php');
$_SESSION['current_page'] = $current_page;  
// Initialize database connection
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASSWORD,
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Fetch dashboard metrics
function getDashboardMetrics($pdo)
{
    $metrics = [
        'low_stock_count' => 0,
        'out_of_stock_count' => 0,
        'today_orders' => 0,
        'today_revenue' => 0.00,
        'today_online_orders' => 0,
        'today_online_revenue' => 0.00
    ];

    // Get inventory alerts
    $stmt = $pdo->query("
        SELECT 
            COUNT(CASE WHEN availablequantity <= 10 AND availablequantity > 0 THEN 1 END) as low_stock,
            COUNT(CASE WHEN availablequantity = 0 THEN 1 END) as out_of_stock
        FROM inventory
    ");
    $inventory = $stmt->fetch(PDO::FETCH_ASSOC);
    $metrics['low_stock_count'] = $inventory['low_stock'];
    $metrics['out_of_stock_count'] = $inventory['out_of_stock'];

    // Get today's orders
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as order_count,
            COALESCE(SUM(ordertotal), 0) as total_revenue,
            COUNT(CASE WHEN ordertype = 'online' THEN 1 END) as online_count,
            COALESCE(SUM(CASE WHEN ordertype = 'online' THEN ordertotal ELSE 0 END), 0) as online_revenue
        FROM customerorder
        WHERE DATE(orderdate) = CURRENT_DATE
    ");
    $stmt->execute();
    $orders = $stmt->fetch(PDO::FETCH_ASSOC);

    $metrics['today_orders'] = $orders['order_count'];
    $metrics['today_revenue'] = $orders['total_revenue'];
    $metrics['today_online_orders'] = $orders['online_count'];
    $metrics['today_online_revenue'] = $orders['online_revenue'];

    return $metrics;
}

function getSalesChartData($pdo)
{
    $stmt = $pdo->query("
        SELECT 
            DATE(orderdate) as date,
            COUNT(*) as order_count,
            COALESCE(SUM(ordertotal), 0) as daily_total
        FROM customerorder 
        WHERE orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
        GROUP BY DATE(orderdate)
        ORDER BY date ASC
    ");

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return [
        'labels' => array_map(function ($row) {
            return date('M d', strtotime($row['date']));
        }, $data),
        'data' => array_column($data, 'daily_total'),
        'orderCounts' => array_column($data, 'order_count')
    ];
}

function getCategoryChartData($pdo)
{
    $stmt = $pdo->query("
        SELECT 
            i.productcategory,
            COALESCE(SUM(ol.quantity * ol.unit_price), 0) as total_sales
        FROM inventory i
        LEFT JOIN orderlog ol ON i.productcode = ol.productcode
        WHERE ol.orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
        GROUP BY i.productcategory
        ORDER BY total_sales DESC
        LIMIT 8
    ");

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return [
        'labels' => array_column($data, 'productcategory'),
        'data' => array_column($data, 'total_sales')
    ];
}

function getInventoryChartData($pdo)
{
    $stmt = $pdo->query("
        SELECT 
            productcategory,
            SUM(availablequantity) as available,
            SUM(onhandquantity) as onhand
        FROM inventory
        GROUP BY productcategory
        ORDER BY available DESC
        LIMIT 8
    ");

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return [
        'labels' => array_column($data, 'productcategory'),
        'available' => array_column($data, 'available'),
        'onhand' => array_column($data, 'onhand')
    ];
}

// Fetch recent orders by type
function getRecentOrders($pdo, $type, $limit = 5)
{
    $stmt = $pdo->prepare("
        SELECT 
            co.orderid,
            co.orderdate,
            co.ordertotal,
            co.status,
            co.customername,
            co.timeoforder,
            GROUP_CONCAT(CONCAT(ol.quantity, 'x ', ol.productname) SEPARATOR ', ') as order_items
        FROM customerorder co
        LEFT JOIN orderlog ol ON co.orderid = ol.orderid
        WHERE co.ordertype = :ordertype
        GROUP BY co.orderid
        ORDER BY co.orderdate DESC, co.timeoforder DESC
        LIMIT :limit
    ");

    // Bind parameters explicitly with their types
    $stmt->bindValue(':ordertype', $type, PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Initialize dashboard data
$dashboard_data = [
    'metrics' => getDashboardMetrics($pdo),
    'recent_orders' => [
        'walk_in' => getRecentOrders($pdo, 'walk-in'),
        'online' => getRecentOrders($pdo, 'online'),
        'delivery' => getRecentOrders($pdo, 'delivery')
    ],
    'charts' => [
        'sales' => getSalesChartData($pdo),
        'categories' => getCategoryChartData($pdo),
        'inventory' => getInventoryChartData($pdo)
    ]
];

// Configure page
Page::setTitle('Dashboard | Supervisor');
Page::setBodyClass('supervisor-body');
Page::set('current_page', 'index');
Page::addScript('https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js');
Page::addScript('https://cdn.jsdelivr.net/npm/chart.js'); // Change from charts.js to chart.js

// Add core styles in correct order
Page::addStyle('../assets/css/style.css');
Page::addStyle('../assets/css/variables.css');
Page::addStyle('../assets/css/sidebar.css');
Page::addStyle('../assets/css/dashboard.css');
Page::addStyle('../assets/css/inventory-master.css');
Page::addStyle('../assets/css/sales-master.css');

// Add theme styles and scripts
Page::addStyle('../assets/css/theme-toggle.css');
Page::addScript('../assets/js/theme-toggle.js');
Page::addScript('../assets/js/supervisor-dashboard.js');

// Start output buffering
ob_start();
?>

<div class="dashboard-wrapper" style="width: 100%;">
    <div class="dashboard-header">
        <div class="welcome-section">
            <h1>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></h1>
            <p class="timestamp"><?= date('l, F j, Y') ?></p>
        </div>
        <button class="refresh-btn" onclick="refreshDashboard()">
            <i class='bx bx-refresh'></i>
        </button>
    </div>

    <!-- Inventory Section with inventory-theme -->
    <div class="inventory-theme">
        <div class="quick-stats">
            <div class="stats-grid">
                <div class="stat-card warning">
                    <div class="stat-icon"><i class='bx bx-error-circle'></i></div>
                    <div class="stat-content">
                        <h3>Low Stock</h3>
                        <p class="value"><?php echo $dashboard_data['metrics']['low_stock_count']; ?></p>
                        <p class="stat-label">Products</p>
                    </div>
                </div>

                <div class="stat-card danger">
                    <div class="stat-icon"><i class='bx bx-x-circle'></i></div>
                    <div class="stat-content">
                        <h3>Out of Stock</h3>
                        <p class="value"><?php echo $dashboard_data['metrics']['out_of_stock_count']; ?></p>
                        <p class="stat-label">Products</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory Chart -->
        <div class="chart-card">
            <div class="card-header">
                <h3>Inventory Status</h3>
            </div>
            <div class="chart-container">
                <canvas id="inventoryHealthChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Sales Section with sales-theme -->
    <div class="sales-theme">
        <div class="quick-stats">
            <div class="stats-grid">
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

        <!-- Sales Charts -->
        <div class="dashboard-grid">
            <div class="dashboard-card wide">
                <div class="card-header">
                    <h2>Sales Overview</h2>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="salesTrendsChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="dashboard-card">
                <div class="card-header">
                    <h2>Category Performance</h2>
                </div>
                <div class="chart-container">
                    <canvas id="categoryPerformanceChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Orders Section -->
        <div class="orders-section">
            <div class="section-header">
                <h2>Recent Orders</h2>
                <div class="order-filters">
                    <div class="tab-navigation">
                        <button class="tab-btn active" onclick="showOrders('walk-in')">Walk-in</button>
                        <button class="tab-btn" onclick="showOrders('online')">Online</button>
                        <button class="tab-btn" onclick="showOrders('delivery')">Delivery</button>
                    </div>
                </div>
            </div>

            <div class="orders-grid">
                <div class="card">
                <div class="card-header">
                    <h3 class="section-title">Recent Walk-in Orders</h3>
                    </div>
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
                                            <?php echo $order['order_items']; ?>
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
                <div class="card-header">
                    <h3 class="section-title">Recent Online Orders</h3>
                    </div>
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
                                            <?php echo $order['order_items']; ?>
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
                <div class="card-header">
                    <h3 class="section-title">Recent Delivery Orders</h3>
                    </div>
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
                                            <?php echo $order['order_items']; ?>
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
</div>

<script>
    const dashboardData = <?php echo json_encode($dashboard_data); ?>;
</script>

<script src="../assets/js/theme.js" defer></script>
<script src="../assets/js/supervisor-dashboard.js" defer></script>

<?php
Page::render(ob_get_clean());
?>