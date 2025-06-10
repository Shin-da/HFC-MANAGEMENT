<?php
require '../session/session.php';
require '../database/dbconnect.php';

// Consolidated Analytics Queries
$dashboard_data = [
    // Basic Metrics
    'metrics' => $conn->query("
        SELECT 
            (SELECT COUNT(*) FROM  WHERE quantity <= reorderpoint) as low_stock_count,
            (SELECT COUNT(*) FROM productlist WHERE quantity = 0) as out_of_stock_count,
            (SELECT COUNT(*) FROM customerorder WHERE DATE(orderdate) = CURRENT_DATE) as today_orders,
            (SELECT COALESCE(SUM(ordertotal), 0) FROM customerorder WHERE DATE(orderdate) = CURRENT_DATE) as today_revenue
    ")->fetch_assoc(),

    // Sales Trends (Last 7 days)
    'sales_trends' => $conn->query("
        SELECT 
            DATE(orderdate) as date,
            COUNT(*) as order_count,
            SUM(ordertotal) as daily_sales
        FROM customerorder 
        WHERE orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)
        GROUP BY DATE(orderdate)
        ORDER BY date
    ")->fetch_all(MYSQLI_ASSOC),

    // Category Performance
    'category_performance' => $conn->query("
        SELECT 
            productcategory,
            COUNT(co.orderid) as order_count,
            SUM(co.ordertotal) as revenue
        FROM productlist pl
        LEFT JOIN customerorder co ON co.orderdescription LIKE CONCAT('%', pl.productname, '%')
        GROUP BY productcategory
    ")->fetch_all(MYSQLI_ASSOC),

    // Inventory Health
    'inventory_health' => $conn->query("
        SELECT 
            productname,
            availablequantity,
            reorderpoint,
            (SELECT COUNT(orderid) FROM customerorder 
             WHERE orderdescription LIKE CONCAT('%', productname, '%')
             AND orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
            ) as monthly_demand
        FROM productlist
    ")->fetch_all(MYSQLI_ASSOC),

    // Order Status Distribution
    'order_status' => $conn->query("
        SELECT 
            status,
            COUNT(*) as count,
            COUNT(*) * 100.0 / SUM(COUNT(*)) OVER() as percentage
        FROM customerorder
        GROUP BY status
    ")->fetch_all(MYSQLI_ASSOC),

    // Recent Orders
    'recent_orders' => [
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
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Supervisor</title>
    <?php require '../reusable/header.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
    <style>
        /* Dashboard Layout */
        .dashboard-wrapper {
            padding: 1.5rem;
            background: var(--panel-color);
        }

        /* Grid System */
        .grid-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .grid-2 {
            grid-template-columns: repeat(2, 1fr);
        }

        @media (max-width: 1200px) {
            .grid-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .grid-container, .grid-2 {
                grid-template-columns: 1fr;
            }
        }

        /* Cards */
        .card {
            background: var(--sidebar-color);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .metric-card {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .metric-card i {
            font-size: 2.5rem;
            padding: 0.75rem;
            border-radius: 8px;
        }

        .metric-info h3 {
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            color: var(--text-color-light);
        }

        .metric-info p {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-color);
        }

        /* Status Colors */
        .warning { border-left: 4px solid #ffc107; }
        .danger { border-left: 4px solid #dc3545; }
        .success { border-left: 4px solid #28a745; }

        /* Charts Section */
        .chart-container {
            min-height: 300px;
            margin-bottom: 1rem;
        }

        /* Orders Section */
        .orders-list {
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
                        <p><?php echo $dashboard_data['metrics']['low_stock_count']; ?> products</p>
                    </div>
                </div>
                <div class="card metric-card danger">
                    <i class='bx bx-x-circle'></i>
                    <div class="metric-info">
                        <h3>Out of Stock</h3>
                        <p><?php echo $dashboard_data['metrics']['out_of_stock_count']; ?> products</p>
                    </div>
                </div>
                <div class="card metric-card success">
                    <i class='bx bx-check-circle'></i>
                    <div class="metric-info">
                        <h3>Today's Orders</h3>
                        <p><?php echo $dashboard_data['metrics']['today_orders']; ?> orders</p>
                        <p>₱<?php echo number_format($dashboard_data['metrics']['today_revenue'], 2); ?></p>
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
                    </div>
                </div>
                <div class="card">
                    <h3 class="section-title">Recent Online Orders</h3>
                    <div class="orders-list scrollbar-thin">
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
                    </div>
                </div>
            </div>
        </div>
        
<?php include_once("../reusable/footer.php"); ?>        
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
                labels: <?php echo json_encode(array_column($dashboard_data['sales_trends'], 'date')); ?>,
                datasets: [{
                    label: 'Daily Revenue',
                    data: <?php echo json_encode(array_column($dashboard_data['sales_trends'], 'daily_sales')); ?>,
                    borderColor: '#4CAF50',
                    tension: 0.1
                }, {
                    label: 'Order Count',
                    data: <?php echo json_encode(array_column($dashboard_data['sales_trends'], 'order_count')); ?>,
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
                labels: <?php echo json_encode(array_column($dashboard_data['category_performance'], 'productcategory')); ?>,
                datasets: [{
                    label: 'Revenue',
                    data: <?php echo json_encode(array_column($dashboard_data['category_performance'], 'revenue')); ?>,
                    backgroundColor: '#4CAF50'
                }, {
                    label: 'Order Count',
                    data: <?php echo json_encode(array_column($dashboard_data['category_performance'], 'order_count')); ?>,
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
                            }, $dashboard_data['inventory_health']);
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
                labels: <?php echo json_encode(array_column($dashboard_data['order_status'], 'status')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($dashboard_data['order_status'], 'percentage')); ?>,
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
    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Sales Trends Chart with better visualization
    const salesCtx = document.getElementById('salesTrendsChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_column($dashboard_data['sales_trends'], 'date')); ?>,
            datasets: [{
                label: 'Daily Sales',
                data: <?php echo json_encode(array_column($dashboard_data['sales_trends'], 'daily_sales')); ?>,
                borderColor: '#4CAF50',
                backgroundColor: 'rgba(76, 175, 80, 0.1)',
                fill: true,
                tension: 0.4
            }, {
                label: 'Orders',
                data: <?php echo json_encode(array_column($dashboard_data['sales_trends'], 'order_count')); ?>,
                borderColor: '#2196F3',
                backgroundColor: 'rgba(33, 150, 243, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Last 7 Days Performance'
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // ... rest of existing chart initializations ...
});
</script>
    <?php include 'script.php'; ?>
</body>
</html>