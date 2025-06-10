<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';
require_once './access_control.php';

// Initialize dashboard data
$dashboard_data = [
    'metrics' => [
        'low_stock_count' => 0,
        'out_of_stock_count' => 0,
        'today_orders' => 0,
        'today_revenue' => 0,
        'today_online_orders' => 0,
        'today_online_revenue' => 0
    ],
    'recent_orders' => [
        'walk_in' => [],
        'online' => [],
        'delivery' => []
    ]
];

try {
    // Get low stock count
    $query = "SELECT COUNT(*) as count FROM products WHERE quantity <= reorder_level AND quantity > 0";
    $result = $conn->query($query);
    if ($result) {
        $dashboard_data['metrics']['low_stock_count'] = $result->fetch_assoc()['count'];
    }

    // Get out of stock count
    $query = "SELECT COUNT(*) as count FROM products WHERE quantity = 0";
    $result = $conn->query($query);
    if ($result) {
        $dashboard_data['metrics']['out_of_stock_count'] = $result->fetch_assoc()['count'];
    }

    // Get today's orders
    $today = date('Y-m-d');
    $query = "SELECT COUNT(*) as count, COALESCE(SUM(ordertotal), 0) as total 
              FROM orders 
              WHERE DATE(orderdate) = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $today);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $dashboard_data['metrics']['today_orders'] = $result['count'];
    $dashboard_data['metrics']['today_revenue'] = $result['total'];

    // Get today's online orders
    $query = "SELECT COUNT(*) as count, COALESCE(SUM(ordertotal), 0) as total 
              FROM orders 
              WHERE DATE(orderdate) = ? AND order_type = 'online'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $today);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $dashboard_data['metrics']['today_online_orders'] = $result['count'];
    $dashboard_data['metrics']['today_online_revenue'] = $result['total'];

    // Get recent orders by type
    foreach (['walk_in', 'online', 'delivery'] as $type) {
        $query = "SELECT o.*, c.customername 
                  FROM orders o 
                  LEFT JOIN customers c ON o.customer_id = c.customer_id 
                  WHERE o.order_type = ? 
                  ORDER BY o.orderdate DESC, o.timeoforder DESC 
                  LIMIT 5";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $type);
        $stmt->execute();
        $dashboard_data['recent_orders'][$type] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

} catch (Exception $e) {
    error_log("Dashboard data error: " . $e->getMessage());
    // Set error message in session if needed
    $_SESSION['error'] = "Error loading dashboard data";
}

// Configure page
Page::setTitle('Dashboard | Supervisor');
Page::setBodyClass('supervisor-body');
Page::set('current_page', 'dashboard');
Page::addScript('/assets/js/dashboard.js');
Page::addScript('/assets/js/supervisor.js');

// Start output buffering
ob_start();
?>

<!-- Your page content here -->
<div class="dashboard-wrapper">
    <!-- Dashboard Content -->
    <div class="dashboard-wrapper animate__animated animate__fadeIn">
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
</div>

<script>
    const dashboardData = <?php echo json_encode($dashboard_data); ?>;
</script>

<?php
// Render the page with the buffered content
Page::render(ob_get_clean());
?>