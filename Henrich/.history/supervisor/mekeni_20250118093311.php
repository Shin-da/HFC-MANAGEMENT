<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';

// Initialize empty analytics array with default values
$mekeni_analytics = [
    'sales_overview' => [],
    'inventory_summary' => [
        'total_products' => 0,
        'low_stock' => 0,
        'out_of_stock' => 0
    ],
    'top_products' => [],
    'inventory_status' => []
];

// Consolidated Mekeni Analytics with error handling
try {
    // Sales Overview
    $result = $conn->query("
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
        LIMIT 6");
    
    if ($result && $result->num_rows > 0) {
        $mekeni_analytics['sales_overview'] = $result->fetch_all(MYSQLI_ASSOC);
    }

    // Inventory Summary
    $result = $conn->query("
        SELECT 
            COUNT(*) as total_products,
            SUM(CASE WHEN availablequantity <= reorderpoint THEN 1 ELSE 0 END) as low_stock,
            SUM(CASE WHEN availablequantity = 0 THEN 1 ELSE 0 END) as out_of_stock
        FROM productlist 
        WHERE supplier = 'Mekeni'");
    
    if ($result && $result->num_rows > 0) {
        $mekeni_analytics['inventory_summary'] = $result->fetch_assoc();
    }

    // Top Products
    $result = $conn->query("
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
        LIMIT 5");
    
    if ($result && $result->num_rows > 0) {
        $mekeni_analytics['top_products'] = $result->fetch_all(MYSQLI_ASSOC);
    }

    // Inventory Status
    $result = $conn->query("
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
        ORDER BY pl.availablequantity ASC");
    
    if ($result && $result->num_rows > 0) {
        $mekeni_analytics['inventory_status'] = $result->fetch_all(MYSQLI_ASSOC);
    }

} catch (Exception $e) {
    error_log("Error in Mekeni analytics: " . $e->getMessage());
}

// Calculate additional metrics with null checks
$total_sales = !empty($mekeni_analytics['sales_overview']) ? 
    array_sum(array_column($mekeni_analytics['sales_overview'], 'total_sales')) : 0;
$total_orders = !empty($mekeni_analytics['sales_overview']) ? 
    array_sum(array_column($mekeni_analytics['sales_overview'], 'order_count')) : 0;

// Add new function for order suggestions
function calculateOrderSuggestions($inventory_status, $sales_overview) {
    $suggestions = [];
    
    foreach ($inventory_status as $product) {
        $monthly_demand = $product['monthly_demand'];
        $current_stock = $product['availablequantity'];
        $reorder_point = $product['reorderpoint'];
        
        // Calculate average daily demand
        $daily_demand = $monthly_demand / 30;
        
        // Calculate days of stock remaining
        $days_remaining = $daily_demand > 0 ? ceil($current_stock / $daily_demand) : 999;
        
        // Calculate suggested order quantity
        // Base order on 30 days of stock plus safety stock
        $safety_stock = ceil($daily_demand * 7); // 7 days safety stock
        $suggested_quantity = ceil($daily_demand * 30) + $safety_stock - $current_stock;
        
        if ($suggested_quantity > 0 || $current_stock <= $reorder_point) {
            $suggestions[] = [
                'productname' => $product['productname'],
                'current_stock' => $current_stock,
                'daily_demand' => $daily_demand,
                'days_remaining' => $days_remaining,
                'suggested_quantity' => $suggested_quantity,
                'priority' => $days_remaining <= 7 ? 'High' : ($days_remaining <= 14 ? 'Medium' : 'Low'),
                'productprice' => $product['productprice']
            ];
        }
    }
    
    // Sort by priority (High > Medium > Low) and then by days remaining
    usort($suggestions, function($a, $b) {
        $priority_order = ['High' => 1, 'Medium' => 2, 'Low' => 3];
        if ($priority_order[$a['priority']] !== $priority_order[$b['priority']]) {
            return $priority_order[$a['priority']] - $priority_order[$b['priority']];
        }
        return $a['days_remaining'] - $b['days_remaining'];
    });
    
    return $suggestions;
}

$order_suggestions = calculateOrderSuggestions($mekeni_analytics['inventory_status'], $mekeni_analytics['sales_overview']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Mekeni Analytics & Order Management</title>
    <?php require '../reusable/header.php'; ?>
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
            // Sales Trend Chart with error handling
            const salesData = <?php echo json_encode($mekeni_analytics['sales_overview'] ?: []); ?>;
            if (salesData.length > 0) {
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
            } else {
                document.getElementById('salesTrendChart').parentElement.innerHTML = 
                    '<div class="alert alert-info">No sales data available</div>';
            }

            // Inventory Status Chart with error handling
            const inventoryData = <?php echo json_encode($mekeni_analytics['inventory_status'] ?: []); ?>;
            if (inventoryData.length > 0) {
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
            } else {
                document.getElementById('inventoryStatusChart').parentElement.innerHTML = 
                    '<div class="alert alert-info">No inventory data available</div>';
            }
        });
    </script>
</body>
</html>

