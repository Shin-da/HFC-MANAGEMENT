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
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .metric-card i {
            font-size: 2.5rem;
            margin-right: 1rem;
        }

        .metric-card.warning { border-left: 4px solid #ffc107; }
        .metric-card.danger { border-left: 4px solid #dc3545; }
        .metric-card.success { border-left: 4px solid #28a745; }

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
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>

<body>
    <?php include '../reusable/sidebar.php'; ?>
    <section class="panel"> <!-- === Dashboard === -->
        <?php include '../reusable/navbarNoSearch.html'; // TOP NAVBAR 
        ?>
        <div class="dashboard">
            <div class="left-panel">
                <div class="container" style="margin-bottom: 10px;">
                    <div style="display: flex; justify-content: center; align-items: center; margin: 20px ; gap: 10px;">
                        <div class="chart-container">
                            <div class="chart-title">Total Sales Revenue</div>
                            <div class="chart-value">&#x20B1;<?php echo number_format($total_sales_revenue, 2); ?></div>
                        </div>
                        <div class="chart-container">
                            <div class="chart-summary" style="display: flex; justify-content: center; align-items: center; gap: 20px;">
                                <div class="chart-summary-item">
                                    <div class="chart-label">Total Orders</div>
                                    <div class="chart-value"><?php echo $total_orders; ?></div>
                                </div>
                                <div class="chart-summary-item">
                                    <div class="chart-label">Average Order Value</div>
                                    <div class="chart-value">&#x20B1;<?php echo $total_orders > 0 ? number_format($total_sales_revenue / $total_orders, 2) : '0.00'; ?></div>
                                </div>
                                <div class="chart-summary-item">
                                    <div class="chart-label">Total Customers</div>
                                    <div class="chart-value">
                                        <?php
                                        $total_customers = $conn->query("SELECT COUNT(DISTINCT customername) FROM customerorder")->fetch_row()[0];
                                        echo $total_customers;
                                        ?>
                                    </div>
                                </div>
                                <div class="chart-summary-item">
                                    <div class="chart-label">Average Orders per Customer</div>
                                    <div class="chart-value"><?php echo $total_customers > 0 ? number_format($total_orders / $total_customers, 2) : '0.00'; ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="boxes">
                            <a href="customerorder.php" class="box box1">
                                <!-- Sale  -->
                                <i class='bx bx-cart'></i>
                                <span class="text">Pending Orders</span>
                                <span class="number">
                                    <?php
                                    $pendingOrders = $conn->query("SELECT COUNT(*) FROM customerorder WHERE status = 'Pending'")->fetch_row()[0];
                                    echo $pendingOrders;
                                    ?>
                                </span>
                                <!-- <span class="arrow"> <i class='bx bx-right-arrow-alt'></i></span> -->
                            </a>
                            <a href="customerorder.php" class="box box2">
                                <i class='bx bx-check-circle'></i>
                                <span class="text">Completed Orders</span>
                                <span class="number">
                                    <?php
                                    $completedOrders = $conn->query("SELECT COUNT(*) FROM customerorder WHERE status = 'Completed'")->fetch_row()[0];
                                    echo $completedOrders;
                                    ?>
                                </span>
                            </a>
                            <a href="customerorder.php" class="box box3">
                                <i class='bx bx-loader-circle'></i>
                                <span class="text">Processing Orders</span>
                                <span class="number">
                                    <?php
                                    $processingOrders = $conn->query("SELECT COUNT(*) FROM customerorder WHERE status = 'Processing'")->fetch_row()[0];
                                    echo $processingOrders;
                                    ?>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="panel-content container recent-orders">
                    <div class="chart-title">
                        <div class="content-header">
                            <i class='bx bx-receipt'></i>
                            <span class="text">Recent Orders</span>
                            <a href="customerorder.php" class="view-all">View All</a>
                        </div>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-direction: row; flex-wrap: wrap;  margin-top: 10px;">
                        <div class="order-list chart-container" style="width: calc(50% - 10px); min-width: 300px;">
                            <?php
                            $sql = "SELECT * FROM customerorder WHERE ordertype = 'Walk-in' ORDER BY datecompleted DESC LIMIT 10";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                            ?>
                                <div class="order-list-header">
                                    <span class="text">Walk-in Orders</span>
                                </div>
                                <?php
                                while ($row = $result->fetch_assoc()) {
                                ?>
                                    <a href="customerorderdetail.php?hid=<?php echo $row['hid']; ?>" class="order-item" style="display: flex; align-items: center; margin-bottom: 10px; padding: 10px; background-color: #f9f9f9; border-radius: 5px; text-decoration: none; color: #333;">
                                        <div style="flex: 1;  margin-right: 10px;">
                                            <span class="text" style="display: block; font-weight: bold;"><?php echo date('m/d/Y', strtotime($row['datecompleted'])); ?></span>
                                        </div>
                                        <div style="flex: 3; margin-right: 10px;">
                                            <span class="text" style="display: block;"><?php echo $row['customername']; ?></span>
                                        </div>
                                        <div style="flex: 1; margin-right: 10px;">
                                            <span class="text" style="display: block;">&#x20B1; <?php echo number_format($row['ordertotal'], 2); ?></span>
                                        </div>
                                    </a>
                                <?php
                                }
                                ?>
                            <?php
                            } else {
                            ?>
                                <p style="text-align: center; color: #777;">No recent walk-in orders yet.</p>
                            <?php
                            }
                            ?>
                        </div>

                        <div class="order-list chart-container" style="width: calc(50% - 10px); min-width: 300px; background-color: var(--orange-color); border-radius: 5px;">
                            <?php
                            $sql = "SELECT * FROM customerorder WHERE ordertype = 'Online' ORDER BY datecompleted DESC LIMIT 10";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                            ?>
                                <div class="order-list-header">
                                    <span class="text">Online Orders</span>
                                </div>
                                <?php
                                while ($row = $result->fetch_assoc()) {
                                ?>
                                    <a href="customerorderdetail.php?hid=<?php echo $row['hid']; ?>" class="order-item" style="display: flex; align-items: center; margin-bottom: 10px; padding: 10px; background-color: #f9f9f9; border-radius: 5px; text-decoration: none; color: #333;">
                                        <div style="flex: 1;  margin-right: 10px;">
                                            <span class="text" style="display: block; font-weight: bold;"><?php echo date('m/d/Y', strtotime($row['datecompleted'])); ?></span>
                                        </div>
                                        <div style="flex: 3; margin-right: 10px;">
                                            <span class="text" style="display: block;"><?php echo $row['customername']; ?></span>
                                        </div>
                                        <div style="flex: 1; margin-right: 10px;">
                                            <span class="text" style="display: block;">&#x20B1;<?php echo number_format($row['ordertotal'], 2); ?></span>
                                        </div>
                                    </a>
                                <?php
                                }
                                ?>
                            <?php
                            } else {
                            ?>
                                <p style="text-align: center; color: #777;">No recent online orders yet.</p>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <div class="overview" style=" overflow-x: auto;">
                    <div class="chart-container " style="max-width: 100%;">
                        <div class="chart-title">Customer Orders Over Time</div>
                        <canvas id="customerOrdersChart" class="chart" style="max-height: 300px;"></canvas>
                        <div class="chart-summary" style="overflow-x: auto;">
                            <?php
                            foreach ($customer_orders as $order) {
                                echo '<div class="chart-summary-item">';
                                echo '<div class="chart-label">' . $order['orderdate'] . '</div>';
                                echo '<div class="chart-value">' . $order['ordertotal'] . '</div>';
                                echo '</div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
<div class="right-panel">
    <div class="overview">
        <div class="alertbox"><!-- Alerts -->
            <div class="content-header">
                <i class='bx bx-bell'></i>
                <span class="text">Low Stock Alert</span>
                <?php
                $alerts = $conn->query("SELECT COUNT(*) FROM inventory WHERE onhandquantity <= 10")->fetch_row()[0];
                ?>
                <span class="number"> <?php echo $alerts; ?> </span>
            </div>
            <?php
            $sql = "SELECT productname, onhandquantity FROM inventory WHERE onhandquantity <= 10 ORDER BY onhandquantity ASC";
            $result = $conn->query($sql);
            ?>
            <div class="alerts" style="overflow-y: scroll; max-height: 300px;">
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {

                        if ($row['onhandquantity'] <= 5) {
                            $color = 'danger';
                        } else if ($row['onhandquantity'] <= 10) {
                            $color = 'warning';
                        } else {
                            $color = 'legend';
                        }
                ?>
                        <div class="alertbox-content" style="border-left: solid 2px  <?php echo $color; ?>; margin-bottom: 10px;">
                            <div class="alert">
                                <p><?php echo $row['productname']; ?> has <?php echo $row['onhandquantity'] >= 0 ? $row['onhandquantity'] : ''; ?> packs left.</p>
                            </div>
                        </div>
                    <?php
                    }
                } else {
                    ?>
                    <div class="alertbox-content" style="background-color: var(--sidebar-color); margin-bottom: 10px;">
                        <div class="alert">
                            <p>No products with low stock.</p>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
    </div>
    <div class="overview">
        <div class="chart-container">
            <div class="chart-title">Best Selling Products</div>
            <canvas id="bestSellingProductsChart" class="chart" style="max-width: 60%;"></canvas>
            <div class="chart-summary">
                <?php
                foreach ($best_selling_products as $product) {
                    echo '<div class="chart-summary-item">';
                    echo '<div class="chart-label">' . $product['productname'] . '</div>';
                    echo '<div class="chart-value">' . $product['total_quantity'] . '</div>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>
    <div class="overview" style=" width: auto;">
        <div class="chart-container ">
            <div class="chart-title">Sales by Category</div>
            <canvas id="salesByCategoryChart" class="chart"></canvas>
            <div class="chart-summary">
                <?php
                foreach ($sales_by_category as $category) {
                    echo '<div class="chart-summary-item">';
                    echo '<div class="chart-label">' . $category['productweight'] . '</div>';
                    echo '<div class="chart-value">' . $category['total_quantity'] . '</div>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>
    <div class="overview">
        <div class="chart-container">
            <div class="chart-title">Calendar and Weather</div>
            <div id="calendar" style="max-width: 60%;"></div>
            <div id="weather" style="margin-top: 20px;"></div>
        </div>
    </div>
</div>
</div>
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
  </script>
   <?php include'script.php'; ?>
</body>

</html>