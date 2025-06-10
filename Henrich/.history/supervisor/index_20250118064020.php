<?php
require '../session/session.php';
require '../database/dbconnect.php';

// Retrieve data from database
$total_sales_revenue = 0;
$total_orders = 0;
$best_selling_products = array();
$sales_by_category = array();
$customer_orders = array();

// Query to retrieve total sales revenue
$query = "SELECT SUM(productprice * quantity) AS total_sales_revenue FROM orderlog";
$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_sales_revenue = $row['total_sales_revenue'];
}

// Query to retrieve total number of orders
$query = "SELECT COUNT(orderid) AS total_orders FROM orderlog";
$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_orders = $row['total_orders'];
}

// Query to retrieve best-selling products
$query = "SELECT productname, SUM(quantity) AS total_quantity
                    FROM orderlog
                    GROUP BY productname
                    ORDER BY total_quantity DESC
                    LIMIT 5";
$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $best_selling_products[] = $row;
    }
}

// Query to retrieve sales by category
$query = "SELECT productweight, SUM(quantity) AS total_quantity
                    FROM orderlog
                    GROUP BY productweight";
$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $sales_by_category[] = $row;
    }
}

// Query to retrieve customer orders
$query = "SELECT * FROM customerorder";
$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $customer_orders[] = $row;
    }
}

// Populate chart data when page is first loaded
$chart_labels_chart = array();
$chart_data_chart = array();
$sql_polar = "SELECT pl.productname, SUM(oh.ordertotal) AS total_sales
                        FROM customerorder oh
                        JOIN productlist pl ON oh.orderdescription LIKE CONCAT('%', pl.productname, '%')
                        GROUP BY pl.productname
                        ORDER BY total_sales DESC
                        LIMIT 5";
$result_polar = $conn->query($sql_polar);
if ($result_polar->num_rows > 0) {
    while ($row = $result_polar->fetch_assoc()) {
        $chart_labels_chart[] = $row["productname"];
        $chart_data_chart[] = $row["total_sales"];
    }
}

// Convert the data to JSON format
$chart_labels_json = json_encode($chart_labels_chart);
$chart_data_json = json_encode($chart_data_chart);

// Add these new queries at the top with other PHP queries
$today = date('Y-m-d');

// Product performance metrics
$low_stock_count = $conn->query("SELECT COUNT(*) FROM inventory WHERE availablequantity <= 5")->fetch_row()[0];
$out_of_stock = $conn->query("SELECT COUNT(*) FROM inventory WHERE availablequantity = 0")->fetch_row()[0];

// Order metrics for today
$today_orders = $conn->query("SELECT COUNT(*) FROM customerorder WHERE DATE(orderdate) = '$today'")->fetch_row()[0];
$today_revenue = $conn->query("SELECT SUM(ordertotal) FROM customerorder WHERE DATE(orderdate) = '$today'")->fetch_row()[0];

// Order status distribution
$status_distribution = $conn->query("
        SELECT status, COUNT(*) as count 
        FROM customerorder 
        GROUP BY status
    ")->fetch_all(MYSQLI_ASSOC);

// Top performing products this month
$top_products = $conn->query("
        SELECT p.productname, SUM(o.quantity) as total_sold, SUM(o.quantity * o.productprice) as revenue
        FROM orderlog o
        JOIN productlist p ON o.productcode = p.productcode
        WHERE MONTH(o.orderdate) = MONTH(CURRENT_DATE)
        GROUP BY p.productname
        ORDER BY total_sold DESC
        LIMIT 5
    ")->fetch_all(MYSQLI_ASSOC);

// Sales trends data (last 30 days)
$sales_trends = $conn->query("
    SELECT 
        DATE(orderdate) as date,
        COUNT(*) as order_count,
        SUM(ordertotal) as daily_revenue
    FROM customerorder 
    WHERE orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
    GROUP BY DATE(orderdate)
    ORDER BY date
")->fetch_all(MYSQLI_ASSOC);

// Product category performance
$category_performance = $conn->query("
    SELECT 
        pl.productcategory,
        COUNT(DISTINCT o.orderid) as order_count,
        SUM(o.quantity) as total_quantity,
        SUM(o.quantity * o.productprice) as revenue
    FROM orderlog o
    JOIN productlist pl ON o.productcode = pl.productcode
    GROUP BY pl.productcategory
")->fetch_all(MYSQLI_ASSOC);

// Inventory health metrics
$inventory_health = $conn->query("
    SELECT 
        i.productcode,
        p.productname,
        i.availablequantity,
        COALESCE(SUM(o.quantity), 0) as monthly_demand
    FROM inventory i
    LEFT JOIN productlist p ON i.productcode = p.productcode
    LEFT JOIN orderlog o ON i.productcode = o.productcode 
        AND o.orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
    GROUP BY i.productcode, p.productname, i.availablequantity
")->fetch_all(MYSQLI_ASSOC);

// Order fulfillment rate
$fulfillment_rate = $conn->query("
    SELECT 
        status,
        COUNT(*) as count,
        COUNT(*) * 100.0 / (SELECT COUNT(*) FROM customerorder) as percentage
    FROM customerorder
    GROUP BY status
")->fetch_all(MYSQLI_ASSOC);

// Get recent orders
$recent_orders = [
    'walk_in' => $conn->query("
        SELECT 
            orderdate,
            customername,
            ordertotal,
            status,
            timeoforder,
            orderdescription
        FROM customerorder 
        WHERE ordertype = 'Walk-in'
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
            orderdescription
        FROM customerorder 
        WHERE ordertype = 'Online'
        ORDER BY orderdate DESC, timeoforder DESC 
        LIMIT 5
    ")->fetch_all(MYSQLI_ASSOC)
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Supervisor</title>
    <?php require '../reusable/header.php'; ?>
    <link rel="stylesheet" href="../resources/css/dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include '../reusable/sidebar.php'; ?>
    <section class="panel">
        <?php include '../reusable/navbarNoSearch.html'; ?>
        
        <div class="dashboard-container animate-slide-in">
            <!-- Quick Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="icon" style="background: var(--stats-bg-1)">
                        <i class='bx bx-shopping-bag'></i>
                    </div>
                    <div class="value"><?= $today_orders ?></div>
                    <div class="label">Today's Orders</div>
                    <div class="sub-value">₱<?= number_format($today_revenue, 2) ?></div>
                </div>

                <div class="stat-card">
                    <div class="icon" style="background: var(--stats-bg-2)">
                        <i class='bx bx-box'></i>
                    </div>
                    <div class="value"><?= $low_stock_count ?></div>
                    <div class="label">Low Stock Items</div>
                </div>

                <div class="stat-card">
                    <div class="icon" style="background: var(--stats-bg-3)">
                        <i class='bx bx-check-circle'></i>
                    </div>
                    <div class="value"><?= $fulfillment_rate[0]['percentage'] ?>%</div>
                    <div class="label">Order Fulfillment Rate</div>
                </div>

                <div class="stat-card">
                    <div class="icon" style="background: var(--stats-bg-4)">
                        <i class='bx bx-trending-up'></i>
                    </div>
                    <div class="value"><?= $total_sales_revenue ?></div>
                    <div class="label">Total Revenue</div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="charts-grid">
                <div class="chart-card">
                    <div class="chart-header">
                        <h3>Sales Trends</h3>
                        <div class="chart-actions">
                            <button class="btn-action" onclick="updateChartPeriod('week')">Week</button>
                            <button class="btn-action" onclick="updateChartPeriod('month')">Month</button>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="salesTrendsChart"></canvas>
                    </div>
                </div>

                <div class="chart-card">
                    <div class="chart-header">
                        <h3>Category Performance</h3>
                    </div>
                    <div class="chart-container">
                        <canvas id="categoryPerformanceChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="activity-list">
                <h3>Recent Orders</h3>
                <?php foreach (array_slice($recent_orders['walk_in'], 0, 5) as $order): ?>
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class='bx bx-package'></i>
            max-height: 400px;
            overflow-y: auto;
            padding-right: 0.5rem;
        }

        .order-item {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 8px;
            background: var(--panel-color);
            border: 1px solid var(--border-color);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .order-status {
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .order-status.pending { background: #fff3cd; color: #856404; }
        .order-status.processing { background: #cce5ff; color: #004085; }
        .order-status.completed { background: #d4edda; color: #155724; }

        /* Utilities */
        .section-title {
            font-size: 1.1rem;
            margin-bottom: 1rem;
            color: var(--text-color);
            font-weight: 600;
        }

        .flex-between {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .scrollbar-thin::-webkit-scrollbar {
            width: 4px;
        }

        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <?php include '../reusable/sidebar.php'; ?>
    <section class="panel">
        <?php include '../reusable/navbarNoSearch.html'; ?>
        
        <div class="dashboard-wrapper">
            <!-- Metrics Overview -->
            <div class="grid-container">
                <!-- Your existing metric cards with new classes -->
                <div class="card metric-card warning">
                    <i class='bx bx-error-circle'></i>
                    <div class="metric-info">
                        <h3>Low Stock Alert</h3>
                        <p><?php echo $low_stock_count; ?> products</p>
                    </div>
                </div>
                <div class="card metric-card danger">
                    <i class='bx bx-x-circle'></i>
                    <div class="metric-info">
                        <h3>Out of Stock</h3>
                        <p><?php echo $out_of_stock; ?> products</p>
                    </div>
                </div>
                <div class="card metric-card success">
                    <i class='bx bx-check-circle'></i>
                    <div class="metric-info">
                        <h3>Today's Orders</h3>
                        <p><?php echo $today_orders; ?> orders</p>
                        <p>₱<?php echo number_format($today_revenue, 2); ?></p>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid-container grid-2">
                <div class="card">
                    <h3 class="section-title">Sales Trends</h3>
                    <div class="chart-container">
                        <canvas id="salesTrendsChart"></canvas>
                    </div>
                </div>
                <div class="card">
                    <h3 class="section-title">Category Performance</h3>
                    <div class="chart-container">
                        <canvas id="categoryPerformanceChart"></canvas>
                    </div>
                </div>
                <div class="card">
                    <h3 class="section-title">Inventory Health</h3>
                    <div class="chart-container">
                        <canvas id="inventoryHealthChart"></canvas>
                    </div>
                </div>
                <div class="card">
                    <h3 class="section-title">Order Fulfillment Rate</h3>
                    <div class="chart-container">
                        <canvas id="fulfillmentChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Orders Section -->
            <div class="grid-container grid-2">
                <div class="card">
                    <h3 class="section-title">Recent Walk-in Orders</h3>
                    <div class="orders-list scrollbar-thin">
                        <?php foreach ($recent_orders['walk_in'] as $order): ?>
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
                    </div>
                </div>
                <div class="card">
                    <h3 class="section-title">Recent Online Orders</h3>
                    <div class="orders-list scrollbar-thin">
                        <?php foreach ($recent_orders['online'] as $order): ?>
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
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php require '../reusable/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
    <script>
        $(document).ready(function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                initialDate: moment(),
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,dayGridWeek,dayGridDay'
                }
            });
            calendar.render();

            var location = 'Pampanga, Philippines';
            var unit = 'c';
            var lang = 'en';
            var weather = new Weather(location, unit, lang);
            weather.getWeatherData(function(data) {
                var html = `<p>Current weather in <strong>${data.location}</strong> is <strong>${data.currently}</strong> with a temperature of <strong>${data.temperature} ${data.unit}</strong>.</p>`;
                $('#weather').html(html);
            });
        });

        // Sales Trends Chart
        const salesTrendsCtx = document.getElementById('salesTrendsChart').getContext('2d');
        new Chart(salesTrendsCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($sales_trends, 'date')); ?>,
                datasets: [{
                    label: 'Daily Revenue',
                    data: <?php echo json_encode(array_column($sales_trends, 'daily_revenue')); ?>,
                    borderColor: '#4CAF50',
                    tension: 0.1
                }, {
                    label: 'Order Count',
                    data: <?php echo json_encode(array_column($sales_trends, 'order_count')); ?>,
                    borderColor: '#2196F3',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });

        // Category Performance Chart
        const categoryCtx = document.getElementById('categoryPerformanceChart').getContext('2d');
        new Chart(categoryCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($category_performance, 'productcategory')); ?>,
                datasets: [{
                    label: 'Revenue',
                    data: <?php echo json_encode(array_column($category_performance, 'revenue')); ?>,
                    backgroundColor: '#4CAF50'
                }, {
                    label: 'Order Count',
                    data: <?php echo json_encode(array_column($category_performance, 'order_count')); ?>,
                    backgroundColor: '#2196F3'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Inventory Health Chart
        const inventoryCtx = document.getElementById('inventoryHealthChart').getContext('2d');
        new Chart(inventoryCtx, {
            type: 'bubble',
            data: {
                datasets: [{
                    label: 'Stock vs Demand',
                    data: <?php
                            $bubble_data = array_map(function ($item) {
                                return [
                                    'x' => $item['availablequantity'],
                                    'y' => $item['monthly_demand'],
                                    'r' => max(5, min(20, $item['availablequantity'] / 10))
                                ];
                            }, $inventory_health);
                            echo json_encode($bubble_data);
                            ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.6)'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Available Quantity'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Monthly Demand'
                        }
                    }
                }
            }
        });

        // Order Fulfillment Chart
        const fulfillmentCtx = document.getElementById('fulfillmentChart').getContext('2d');
        new Chart(fulfillmentCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_column($fulfillment_rate, 'status')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($fulfillment_rate, 'percentage')); ?>,
                    backgroundColor: [
                        '#4CAF50', // Completed
                        '#FFC107', // Processing
                        '#F44336' // Pending
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
    <?php include 'script.php'; ?>
</body>
</html>