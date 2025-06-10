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
        WHERE ordertype = 'Delivery'
        ORDER BY orderdate DESC, timeoforder DESC 
        LIMIT 5
    ")->fetch_all(MYSQLI_ASSOC)
];
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Supervisor</title>
    <!-- <?php require '../reusable/header.php'; ?> -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.1/fullcalendar.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.1/locales-all.min.js"></script>
    <style>
        .panel {
            position: relative;
            left: 220px;
            min-height: 100vh;
            margin-top: 25px;
            width: calc(100% - 220px);
            /* padding: 10px 14px; */
            transition: var(--tran-05);
            background-color: var(--panel-color);
        }

        .panel-content {
            border-radius: 5px;
            /* margin-top: 10px; */
            padding: 10px;
            /* border: 1px solid var(--grey-inactive); */
            box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.371), 0 4px 8px 0 rgba(0, 0, 0, 0.05);
        }

        .left-panel {
            width: calc(80vw - 220px);
            transition: var(--tran-05);
            /* margin-right: 10px; */
        }

        .right-panel {
            max-width: calc(30vw - (80vw - 220px));
            /* width: 300px; */
            transition: var(--tran-05);
        }

        .dashboard {
            transition: var(--tran-05);
            background-color: var(--panel-color);
            display: grid;
            /* grid-template-columns: 3fr 2fr; */
            gap: 10px;
        }

        .container {
            margin: 0 auto;
            padding: 12px;
            border-radius: 5px;
            background-color: var(--sidebar-color);
            border: 1px solid var(--border-color);
        }

        .container-fluid {
            transition: var(--tran-05);
            margin: 0 auto;
            padding: 12px;
            border-radius: 5px;
            background-color: var(--sidebar-color);
            border: 1px solid var(--border-color);
        }

        .overview {
            width: 100%;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-evenly;
            gap: 2%;
            /* padding: 20px 0; */
            margin: 10px 0;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            background-color: var(--sidebar-color);
        }

        /* Alertbox */
        .alertbox {
            max-height: 200px;
            max-width: 100%;
            min-width: 300px;
            background-color: var(--sidebar-color);
            border-radius: 5px;
            padding-bottom: 10px;
            margin-bottom: 10px;
            /* border: 3px solid var(--border-color);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); */
        }

        .content-header {
            display: flex;
            align-items: center;
            padding: 10px;
            /* border-bottom: 1px solid var(--border-color); */
        }

        .alertbox .content-header {
            justify-content: space-between;
        }

        .sales-report .content-header {
            justify-content: space-around;
        }

        .sales-report {
            /* width: auto; */
            max-height: 300px;
            border-radius: 5px;
        }

        .alerts {
            overflow-y: scroll;
            height: 150px;
            /* background-color: var(--panel-color);
            border-top-right-radius: 5px;
            border-top-left-radius: 5px; */
            padding: 10px;
            /* border-top: 1px solid var(--border-color); */
        }

        .alertbox .alertbox-content {
            background-color: var(--accent-color);
            padding: 10px;
            display: grid;
            grid-template-columns: 1fr auto;
            align-items: center;
            margin-bottom: 10px;
            border-bottom: 1px solid var(--border-color);
            gap: 10px;
        }

        .alertbox .warning {
            background-color: var(--warning-color);
        }

        .alertbox .danger {
            background-color: var(--danger-color);
            color: var(--);
        }

        .boxes {
            display: flex;
            justify-content: space-evenly;
            flex-wrap: wrap;
        }

        .box {
            position: relative;
            display: flex;
            /* flex-direction: column; */
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-around;

            width: 250px;
            margin-bottom: 10px;
            height: max-content;
            border-radius: 5px;
            background: var(--sidebar-color);
            color: var(--text-color);
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);

            @media screen and (max-width: 530px) {
                width: 80%;
                margin-bottom: 10px;
            }
        }

        .box i {
            font-size: 2.5rem;
        }

        .box .number {
            font-size: 1.5rem;
            font-weight: 500;
        }

        .box .arrow {
            margin-right: -10px;
            background-color: var(--toggle-color);

        }

        .box:hover {
            cursor: pointer;
            background-color: var(--border-color);
            transition: var(--tran-05);
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            scale: 1.03;
        }

        .box1 {
            color: var(--primary-color);
        }

        .box2 {
            color: var(--success-color);
        }

        .box3 {
            color: var(--danger-color);
        }


        .title {
            font-size: 20px;
            font-weight: 500;
            color: var(--accent-color);
        }

        .charts {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            width: 100%;
        }

        .chart-container {
            /* width: 100%; */
            /* margin-bottom: 20px; */
            background: #fefefe;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        @media only screen and (max-width: 768px) {
            .chart-container {
                width: 100%;
            }
        }

        .chart-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #555;
        }

        .chart {
            height: 300px;
        }

        .chart-summary {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }

        .chart-summary-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        .chart-summary-item:not(:last-child) {
            margin-right: 20px;
        }

        @media only screen and (max-width: 768px) {
            .chart-summary {
                flex-direction: column;
                align-items: center;
            }

            .chart-summary-item:not(:last-child) {
                margin-right: 0;
                margin-bottom: 20px;
            }
        }

        .chart-label {
            font-size: 14px;
            color: #777;
        }

        .chart-value {
            font-size: 20px;
            color: #333;
        }

        .metrics-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .metric-card {
            display: flex;
            align-items: center;
            padding: 1.5rem;
            border-radius: 8px;
            background: var(--sidebar-color);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .metric-card i {
            font-size: 2.5rem;
            margin-right: 1rem;
        }

        .metric-card.warning {
            border-left: 4px solid #ffc107;
        }

        .metric-card.danger {
            border-left: 4px solid #dc3545;
        }

        .metric-card.success {
            border-left: 4px solid #28a745;
        }

        .status-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .product-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .product-card {
            background: var(--sidebar-color);
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .analytics-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin-top: 2rem;
            padding: 0 1.5rem;
        }

        .chart-card {
            background: var(--sidebar-color);
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .chart-card h3 {
            margin-bottom: 1rem;
            color: var(--text-color);
            font-size: 1.1rem;
        }

        @media (max-width: 1024px) {
            .analytics-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
/*************  ✨ Codeium Command 🌟  *************/
    </section>
    <!-- Add after analytics-grid div -->

<body>
    <?php include '../reusable/sidebar.php'; ?>
    <section class="panel"> <!-- === Dashboard === -->
        <?php include '../reusable/navbarNoSearch.html'; // TOP NAVBAR 
        ?>

        <!-- Add this in the HTML section -->
        <div class="dashboard">
            <!-- Key Metrics Section -->
            <div class="metrics-overview">
                <div class="metric-card warning">
                    <i class='bx bx-error-circle'></i>
                    <div class="metric-info">
                        <h3>Low Stock Alert</h3>
                        <p><?php echo $low_stock_count; ?> products</p>
                    </div>
                </div>
                <div class="metric-card danger">
                    <i class='bx bx-x-circle'></i>
                    <div class="metric-info">
                        <h3>Out of Stock</h3>
                        <p><?php echo $out_of_stock; ?> products</p>
                    </div>
                </div>
                <div class="metric-card success">
                    <i class='bx bx-check-circle'></i>
                    <div class="metric-info">
                        <h3>Today's Orders</h3>
                        <p><?php echo $today_orders; ?> orders</p>
                        <p>₱<?php echo number_format($today_revenue, 2); ?></p>
                    </div>
                </div>
            </div>

            <!-- Real-time Order Status -->
            <div class="order-status-section">
                <h3>Order Status Distribution</h3>
                <div class="status-cards">
                    <?php foreach ($status_distribution as $status): ?>
                        <div class="status-card <?php echo strtolower($status['status']); ?>">
                            <h4><?php echo $status['status']; ?></h4>
                            <p><?php echo $status['count']; ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Top Products Performance -->
            <div class="top-products">
                <h3>Top Performing Products (This Month)</h3>
                <div class="product-list">
                    <?php foreach ($top_products as $product): ?>
                        <div class="product-card">
                            <div class="product-info">
                                <h4><?php echo $product['productname']; ?></h4>
                                <p>Sold: <?php echo $product['total_sold']; ?> units</p>
                                <p>Revenue: ₱<?php echo number_format($product['revenue'], 2); ?></p>
                            </div>
                            <div class="product-chart">
                                <!-- Add a small sparkline chart here if desired -->
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Add this after the top-products div but before closing the dashboard div -->
            <div class="analytics-grid">
                <!-- Sales Trends Chart -->
                <div class="chart-card">
                    <h3>Sales Trends (Last 30 Days)</h3>
                    <canvas id="salesTrendsChart"></canvas>
                </div>

                <!-- Category Performance Chart -->
                <div class="chart-card">
                    <h3>Category Performance</h3>
                    <canvas id="categoryPerformanceChart"></canvas>
                </div>

                <!-- Inventory Health Chart -->
                <div class="chart-card">
                    <h3>Inventory Health</h3>
                    <canvas id="inventoryHealthChart"></canvas>
                </div>

                <!-- Order Fulfillment Chart -->
                <div class="chart-card">
                    <h3>Order Fulfillment Rate</h3>
                    <canvas id="fulfillmentChart"></canvas>
                </div>
            </div>
        </div>
    </section>
/******  4ffc2f7c-f746-464c-9d2c-4ff985ebfc7b  *******/

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
                        $bubble_data = array_map(function($item) {
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
                        '#F44336'  // Pending
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