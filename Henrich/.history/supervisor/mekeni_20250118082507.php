<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';

// Fetch analytics data
$current_month = date('m');
$current_year = date('Y');

// Get top selling products
$top_products_query = "SELECT 
    p.productname,
    SUM(o.quantity) as total_sold,
    p.quantity as current_stock,
    p.reorderpoint
    FROM products p
    LEFT JOIN orderitems o ON p.productid = o.productid
    WHERE p.supplier = 'Mekeni'
    GROUP BY p.productid
    ORDER BY total_sold DESC
    LIMIT 5";
$top_products = $conn->query($top_products_query);

// Get products that need reordering
$reorder_query = "SELECT 
    productname,
    quantity,
    reorderpoint,
    ROUND(AVG(daily_sales), 2) as avg_daily_sales,
    ROUND(quantity / AVG(daily_sales), 0) as days_until_empty
    FROM (
        SELECT 
            p.productname,
            p.quantity,
            p.reorderpoint,
            DATE(o.orderdate) as sale_date,
            SUM(oi.quantity) as daily_sales
        FROM products p
        LEFT JOIN orderitems oi ON p.productid = oi.productid
        LEFT JOIN customerorder o ON oi.orderid = o.orderid
        WHERE p.supplier = 'Mekeni'
        GROUP BY p.productid, DATE(o.orderdate)
    ) as daily_stats
    GROUP BY productname
    HAVING quantity <= reorderpoint
    ORDER BY days_until_empty ASC";
$reorder_products = $conn->query($reorder_query);

// Get monthly sales trend
$trends_query = "SELECT 
    DATE_FORMAT(o.orderdate, '%Y-%m') as month,
    SUM(oi.quantity * oi.price) as total_sales
    FROM orderitems oi
    JOIN customerorder o ON oi.orderid = o.orderid
    JOIN products p ON oi.productid = p.productid
    WHERE p.supplier = 'Mekeni'
    GROUP BY DATE_FORMAT(o.orderdate, '%Y-%m')
    ORDER BY month DESC
    LIMIT 6";
$trends = $conn->query($trends_query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Mekeni Analytics & Order Management</title>
    <?php require '../reusable/header.php'; ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
    <style>
        button:focus,
        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
        }

        .tabs {
            display: block;
            display: -webkit-flex;
            display: -moz-flex;
            display: flex;
            -webkit-flex-wrap: wrap;
            -moz-flex-wrap: wrap;
            flex-wrap: wrap;
            margin: 0;
            overflow: hidden;
        }

        .tabs [class^="tab"] label,
        .tabs [class*=" tab"] label {
            color: #191212;
            cursor: pointer;
            display: block;
            line-height: 1em;
            padding: 2rem 0;
            text-align: center;
        }

        .tabs [class^="tab"] [type="radio"],
        .tabs [class*=" tab"] [type="radio"] {
            border-bottom: 1px solid rgba(239, 237, 239, 0.5);
            cursor: pointer;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            display: block;
            width: 100%;
            -webkit-transition: all 0.3s ease-in-out;
            -moz-transition: all 0.3s ease-in-out;
            -o-transition: all 0.3s ease-in-out;
            transition: all 0.3s ease-in-out;
        }

        .tabs [class^="tab"] [type="radio"]:hover,
        .tabs [class^="tab"] [type="radio"]:focus,
        .tabs [class*=" tab"] [type="radio"]:hover,
        .tabs [class*=" tab"] [type="radio"]:focus {
            border-bottom: 1px solid var(--accent-color-dark);
        }

        .tabs [class^="tab"] [type="radio"]:checked,
        .tabs [class*=" tab"] [type="radio"]:checked {
            border-bottom: 2px solid var(--accent-color-dark);
        }

        .tabs [class^="tab"] [type="radio"]:checked+div,
        .tabs [class*=" tab"] [type="radio"]:checked+div {
            opacity: 1;
        }

        .tabs [class^="tab"] [type="radio"]+div,
        .tabs [class*=" tab"] [type="radio"]+div {
            display: block;
            opacity: 0;
            padding: 2rem 0;
            width: 90%;
            -webkit-transition: opacity 0.3s ease-in-out;
            -moz-transition: opacity 0.3s ease-in-out;
            -o-transition: opacity 0.3s ease-in-out;
            transition: opacity 0.3s ease-in-out;
        }

        .tabs .tab-2 {
            width: 50%;
        }

        .tabs .tab-2 [type="radio"]+div {
            width: 200%;
            margin-left: 200%;
        }

        .tabs .tab-2 [type="radio"]:checked+div {
            margin-left: 0;
        }

        .tabs .tab-2:last-child [type="radio"]+div {
            margin-left: 100%;
        }

        .tabs .tab-2:last-child [type="radio"]:checked+div {
            margin-left: -100%;
        }

        .analytics-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .analytics-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .suggestion-card {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 10px 0;
        }

        .urgent-reorder {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
        }

        .chart-container {
            height: 300px;
            margin: 20px 0;
        }

        .action-button {
            background: var(--accent-color);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .action-button:hover {
            background: var(--accent-color-dark);
        }
    </style>
</head>
<body>
    <?php include '../reusable/sidebar.php';   // Sidebar   
    ?>

    <!-- === Orders === -->
    <section class=" panel">
        <?php include '../reusable/navbarNoSearch.html'; // TOP NAVBAR         
        ?>

        <div class="container-fluid"> <!-- Stock Management -->
            <div class="table-header" style="justify-content: center">
                <div class="title" style="color:var(--accent-color-dark)">
                    <span>
                       <img src="../resources/images/Mekeni-Ph-Logo.svg" alt="" style="width: 100px; height: 100px;">
                    </span>
                    <span style="font-size: 12px;"> </span>
                </div>
            </div>

        </div>

        <div class="container-fluid">
            <div class="tabs">
                <div class="tab-2">
                    <label for="tab2-1">Dashboard</label>
                    <input id="tab2-1" name="tabs-two" type="radio" checked="checked">
                    <div>
                        <div class="analytics-container">
                            <!-- Sales Overview -->
                            <div class="analytics-card">
                                <h3>Sales Overview</h3>
                                <canvas id="salesTrend"></canvas>
                                <div class="chart-container">
                                    <!-- Sales trend chart will be rendered here -->
                                </div>
                            </div>

                            <!-- Top Selling Products -->
                            <div class="analytics-card">
                                <h3>Top Selling Products</h3>
                                <?php while($product = $top_products->fetch_assoc()): ?>
                                    <div class="product-stat">
                                        <h4><?php echo htmlspecialchars($product['productname']); ?></h4>
                                        <p>Total Sold: <?php echo $product['total_sold']; ?></p>
                                        <p>Current Stock: <?php echo $product['current_stock']; ?></p>
                                    </div>
                                <?php endwhile; ?>
                            </div>

                            <!-- Reorder Suggestions -->
                            <div class="analytics-card">
                                <h3>Reorder Suggestions</h3>
                                <?php while($product = $reorder_products->fetch_assoc()): ?>
                                    <div class="suggestion-card <?php echo $product['days_until_empty'] < 7 ? 'urgent-reorder' : ''; ?>">
                                        <h4><?php echo htmlspecialchars($product['productname']); ?></h4>
                                        <p>Current Stock: <?php echo $product['quantity']; ?></p>
                                        <p>Average Daily Sales: <?php echo $product['avg_daily_sales']; ?></p>
                                        <p>Days Until Empty: <?php echo $product['days_until_empty']; ?></p>
                                        <button class="action-button" onclick="createOrder('<?php echo $product['productname']; ?>')">
                                            Create Order
                                        </button>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-2">
                    <label for="tab2-2">Order Management</label>
                    <input id="tab2-2" name="tabs-two" type="radio">
                    <div>
                        <!-- Add New Order Form -->
                        <div class="order-form">
                            <h3>Create New Order</h3>
                            <form id="newOrderForm" method="POST" action="process_mekeni_order.php">
                                <!-- Add order form fields -->
                            </form>
                        </div>

                        <!-- Order History Table -->
                        <div class="order-history">
                            <h3>Order History</h3>
                            <table class="table">
                                <!-- Add order history table -->
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </section>

    <script>
        // Initialize Charts
        document.addEventListener('DOMContentLoaded', function() {
            // Sales Trend Chart
            const salesCtx = document.getElementById('salesTrend').getContext('2d');
            new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode(array_column($trends->fetch_all(MYSQLI_ASSOC), 'month')); ?>,
                    datasets: [{
                        label: 'Monthly Sales',
                        data: <?php echo json_encode(array_column($trends->fetch_all(MYSQLI_ASSOC), 'total_sales')); ?>,
                        borderColor: '#4CAF50',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        });

        // Order Creation Function
        function createOrder(productName) {
            // Implement order creation logic
        }

        // Add more interactive features as needed
    </script>
</body>
<?php require '../reusable/footer.php'; ?>

</html>