<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';
require_once './access_control.php';

// Initialize dashboard data
$dashboard_data = [];

// Get metrics data from database
$today = date('Y-m-d');

// Initialize metrics array
$dashboard_data['metrics'] = [
    'low_stock_count' => 0,
    'out_of_stock_count' => 0,
    'today_orders' => 0,
    'today_revenue' => 0.00,
    'today_online_orders' => 0,
    'today_online_revenue' => 0.00
];

// Get low stock count
$query = "SELECT COUNT(*) as count FROM products WHERE quantity <= reorder_level AND quantity > 0";
$result = $conn->query($query);
if ($result && $row = $result->fetch_assoc()) {
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
</div>

<script>
    const dashboardData = <?php echo json_encode($dashboard_data); ?>;
</script>

<?php
// Render the page with the buffered content
Page::render(ob_get_clean());
?>