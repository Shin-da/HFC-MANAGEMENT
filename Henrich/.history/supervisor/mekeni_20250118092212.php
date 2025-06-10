<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';

// Consolidated Mekeni Analytics
try {
    $mekeni_analytics = [
        'sales_overview' => $conn->query("
            SELECT 
                DATE_FORMAT(co.orderdate, '%Y-%m') as month,
                COUNT(*) as order_count,
                SUM(co.ordertotal) as total_sales,
            AVG(co.ordertotal) as avg_order_value
        FROM customerorder co
        JOIN productlist pl ON co.orderdescription LIKE CONCAT('%', pl.productname, '%')
        WHERE pl.supplier = 'Mekeni'
        GROUP BY DATE_FORMAT(co.orderdate, '%Y-%m')
        ORDER BY month DESC
        LIMIT 6")->fetch_all(MYSQLI_ASSOC),

    // Top Products
    'top_products' => $conn->query("
        SELECT 
            pl.productname,
            pl.availablequantity,
            COUNT(co.orderid) as total_orders,
            SUM(co.ordertotal) as total_sales
        FROM productlist pl
        LEFT JOIN customerorder co ON co.orderdescription LIKE CONCAT('%', pl.productname, '%')
        WHERE pl.supplier = 'Mekeni'
        GROUP BY pl.productname, pl.availablequantity
        ORDER BY total_orders DESC
        LIMIT 5")->fetch_all(MYSQLI_ASSOC),

    // Inventory Status
    'inventory_status' => $conn->query("
        SELECT 
            pl.productname,
            pl.availablequantity,
            pl.reorderpoint,
            COUNT(co.orderid) as demand_count
        FROM productlist pl
        LEFT JOIN customerorder co ON co.orderdescription LIKE CONCAT('%', pl.productname, '%')
            AND co.orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
        WHERE pl.supplier = 'Mekeni'
        GROUP BY pl.productname, pl.availablequantity, pl.reorderpoint
        HAVING pl.availablequantity <= pl.reorderpoint
        ORDER BY pl.availablequantity ASC")->fetch_all(MYSQLI_ASSOC)
];
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
                                <?php foreach($mekeni_analytics['top_products'] as $product): ?>
                                    <div class="product-stat">
                                        <h4><?php echo htmlspecialchars($product['productname']); ?></h4>
                                        <p>Total Orders: <?php echo $product['total_orders']; ?></p>
                                        <p>Total Sales: <?php echo $product['total_sales']; ?></p>
                                        <p>Current Stock: <?php echo $product['availablequantity']; ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Reorder Suggestions -->
                            <div class="analytics-card">
                                <h3>Reorder Suggestions</h3>
                                <?php foreach($mekeni_analytics['inventory_status'] as $product): ?>
                                    <div class="suggestion-card <?php echo $product['availablequantity'] < $product['reorderpoint'] ? 'urgent-reorder' : ''; ?>">
                                        <h4><?php echo htmlspecialchars($product['productname']); ?></h4>
                                        <p>Current Stock: <?php echo $product['availablequantity']; ?></p>
                                        <p>Reorder Point: <?php echo $product['reorderpoint']; ?></p>
                                        <button class="action-button" onclick="createOrder('<?php echo $product['productname']; ?>')">
                                            Create Order
                                        </button>
                                    </div>
                                <?php endforeach; ?>
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
                    labels: <?php echo json_encode(array_column($mekeni_analytics['sales_overview'], 'month')); ?>,
                    datasets: [{
                        label: 'Monthly Sales',
                        data: <?php echo json_encode(array_column($mekeni_analytics['sales_overview'], 'total_sales')); ?>,
                        borderColor: '#4CAF50',
                        tension: 0.1
                    }, {
                        label: 'Order Count',
                        data: <?php echo json_encode(array_column($mekeni_analytics['sales_overview'], 'order_count')); ?>,
                        borderColor: '#2196F3',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Value'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Month'
                            }
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Mekeni Sales Trends'
                        }
                    }
                }
            });

            // Add inventory status chart
            const inventoryCtx = document.getElementById('inventoryChart').getContext('2d');
            new Chart(inventoryCtx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode(array_column($mekeni_analytics['inventory_status'], 'productname')); ?>,
                    datasets: [{
                        label: 'Current Stock',
                        data: <?php echo json_encode(array_column($mekeni_analytics['inventory_status'], 'availablequantity')); ?>,
                        backgroundColor: '#4CAF50'
                    }, {
                        label: 'Reorder Point',
                        data: <?php echo json_encode(array_column($mekeni_analytics['inventory_status'], 'reorderpoint')); ?>,
                        backgroundColor: '#FFC107'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Quantity'
                            }
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Inventory Status'
                        }
                    }
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
