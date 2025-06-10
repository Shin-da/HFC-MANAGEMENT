/*************  ✨ Codeium Command 🌟  *************/
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

        'inventory_summary' => $conn->query("
            SELECT 
                COUNT(*) as total_products,
                SUM(CASE WHEN availablequantity <= reorderpoint THEN 1 ELSE 0 END) as low_stock,
                SUM(CASE WHEN availablequantity = 0 THEN 1 ELSE 0 END) as out_of_stock
            FROM productlist 
            WHERE supplier = 'Mekeni'")->fetch_assoc(),

        'top_products' => $conn->query("
            SELECT 
                pl.productname,
                pl.availablequantity,
                pl.productprice,
                COUNT(co.orderid) as total_orders,
                SUM(co.ordertotal) as total_sales
            FROM productlist pl
            LEFT JOIN customerorder co ON co.orderdescription LIKE CONCAT('%', pl.productname, '%')
            WHERE pl.supplier = 'Mekeni'
            GROUP BY pl.productname, pl.availablequantity, pl.productprice
            ORDER BY total_orders DESC
            LIMIT 5")->fetch_all(MYSQLI_ASSOC),

        'inventory_status' => $conn->query("
            SELECT 
                pl.productname,
                pl.availablequantity,
                pl.reorderpoint,
                pl.productprice,
                COUNT(co.orderid) as monthly_demand
            FROM productlist pl
            LEFT JOIN customerorder co ON co.orderdescription LIKE CONCAT('%', pl.productname, '%')
                AND co.orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
            WHERE pl.supplier = 'Mekeni'
            GROUP BY pl.productname, pl.availablequantity, pl.reorderpoint, pl.productprice
            HAVING pl.availablequantity <= pl.reorderpoint
            ORDER BY pl.availablequantity ASC")->fetch_all(MYSQLI_ASSOC)
    ];
} catch (Exception $e) {
    error_log("Error in Mekeni analytics: " . $e->getMessage());
    $mekeni_analytics = [];
}

// Calculate additional metrics
$total_sales = array_sum(array_column($mekeni_analytics['sales_overview'], 'total_sales'));
$total_orders = array_sum(array_column($mekeni_analytics['sales_overview'], 'order_count'));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Mekeni Analytics & Order Management</title>
    <?php require '../reusable/header.php'; ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
    <style>
        /* ... existing styles ... */
        
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .metric-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }

        .metric-card h4 {
            color: #666;
            margin-bottom: 0.5rem;
        }

        .metric-card .value {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--accent-color);
        }

        .chart-wrapper {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
        }

        .product-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1rem;
        }

        .product-card {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            border-left: 4px solid var(--accent-color);
        }

        .status-indicator {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .status-low {
            background: #fff3cd;
            color: #856404;
        }

        .status-out {
            background: #f8d7da;
            color: #721c24;
        }

        .status-good {
            background: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>
    <?php include '../reusable/sidebar.php'; ?>
    <section class="panel">
        <?php include '../reusable/navbarNoSearch.html'; ?>

        <div class="container-fluid">
            <!-- Header with Mekeni Logo -->
            <div class="text-center my-4">
                <img src="../resources/images/Mekeni-Ph-Logo.svg" alt="Mekeni Logo" style="width: 150px;">
            </div>

            <!-- Key Metrics -->
            <div class="metrics-grid">
                <div class="metric-card">
                    <h4>Total Sales</h4>
                    <div class="value">₱<?php echo number_format($total_sales, 2); ?></div>
                </div>
                <div class="metric-card">
                    <h4>Total Orders</h4>
                    <div class="value"><?php echo $total_orders; ?></div>
                </div>
                <div class="metric-card">
                    <h4>Low Stock Items</h4>
                    <div class="value"><?php echo $mekeni_analytics['inventory_summary']['low_stock']; ?></div>
                </div>
                <div class="metric-card">
                    <h4>Out of Stock</h4>
                    <div class="value"><?php echo $mekeni_analytics['inventory_summary']['out_of_stock']; ?></div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="row">
                <div class="col-md-6">
                    <div class="chart-wrapper">
                        <canvas id="salesTrendChart"></canvas>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="chart-wrapper">
                        <canvas id="inventoryStatusChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Products Section -->
            <div class="product-list mt-4">
                <?php foreach ($mekeni_analytics['inventory_status'] as $product): ?>
                    <div class="product-card">
                        <h5><?php echo htmlspecialchars($product['productname']); ?></h5>
                        <p>Current Stock: <?php echo $product['availablequantity']; ?></p>
                        <p>Reorder Point: <?php echo $product['reorderpoint']; ?></p>
                        <p>Monthly Demand: <?php echo $product['monthly_demand']; ?></p>
                        <span class="status-indicator <?php 
                            echo $product['availablequantity'] === 0 ? 'status-out' : 
                                ($product['availablequantity'] <= $product['reorderpoint'] ? 'status-low' : 'status-good'); 
                        ?>">
                            <?php 
                            echo $product['availablequantity'] === 0 ? 'Out of Stock' : 
                                ($product['availablequantity'] <= $product['reorderpoint'] ? 'Low Stock' : 'In Stock'); 
                            ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php require '../reusable/footer.php'; ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sales Trend Chart
            const salesData = <?php echo json_encode($mekeni_analytics['sales_overview']); ?>;
            new Chart(document.getElementById('salesTrendChart'), {
                type: 'line',
                data: {
                    labels: salesData.map(item => item.month),
                    datasets: [{
                        label: 'Sales (₱)',
                        data: salesData.map(item => item.total_sales),
                        borderColor: '#4CAF50',
                        fill: false
                    }, {
                        label: 'Orders',
                        data: salesData.map(item => item.order_count),
                        borderColor: '#2196F3',
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Sales Trends'
                        }
                    }
                }
            });

            // Inventory Status Chart
            const inventoryData = <?php echo json_encode($mekeni_analytics['inventory_status']); ?>;
            new Chart(document.getElementById('inventoryStatusChart'), {
                type: 'bar',
                data: {
                    labels: inventoryData.map(item => item.productname),
                    datasets: [{
                        label: 'Current Stock',
                        data: inventoryData.map(item => item.availablequantity),
                        backgroundColor: '#4CAF50'
                    }, {
                        label: 'Reorder Point',
                        data: inventoryData.map(item => item.reorderpoint),
                        backgroundColor: '#FFC107'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Inventory Status'
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>

/******  1986744b-ff27-4724-966e-b29682f56424  *******/