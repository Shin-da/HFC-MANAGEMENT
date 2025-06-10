<?php
require '../session/session.php';
require '../database/dbconnect.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');


// Consolidated Analytics Queries
$dashboard_data = [
    // Basic Metrics
    'metrics' => $conn->query("
        SELECT 
            (SELECT COUNT(*) FROM inventory WHERE availablequantity <= reorder_point) as low_stock_count,
            (SELECT COUNT(*) FROM inventory WHERE availablequantity = 0) as out_of_stock_count,
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
            reorder_point,
            (SELECT COUNT(orderid) FROM customerorder 
             WHERE orderdescription LIKE CONCAT('%', productname, '%')
             AND orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
            ) as monthly_demand
        FROM inventory
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
<html>
<head>
    <title>Dashboard</title>
    <?php require '../reusable/header.php'; ?>
    <link rel="stylesheet" href="../resources/css/theme-variants.css">
    <style>
        .dashboard-container {
            padding: 2rem;
            background: var(--surface);
        }

        .section-divider {
            margin: 2rem 0;
            padding: 1rem;
            background: var(--surface);
            border-radius: 8px;
            border-left: 4px solid var(--primary);
        }

        .section-divider h2 {
            color: var(--text-primary);
            font-size: 1.25rem;
            margin: 0;
        }

        /* Sales Section */
        .sales-section .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .sales-section .stat-card {
            background: var(--card-bg);
            border-radius: 8px;
            padding: 1.5rem;
            border-left: 4px solid var(--df5c36);
            box-shadow: 0 2px 4px rgba(54, 48, 45, 0.1);
        }

        .sales-section .chart-container {
            background: var(--card-bg);
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid var(--operation-border);
        }

        /* Inventory Section */
        .inventory-section .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .inventory-section .stat-card {
            background: var(--card-bg);
            border-radius: 8px;
            padding: 1.5rem;
            border-left: 4px solid var(--385a41);
            box-shadow: 0 2px 4px rgba(54, 48, 45, 0.1);
        }

        .status-indicator {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .action-card {
            background: var(--card-bg);
            border-radius: 8px;
            padding: 1.5rem;
            text-align: center;
            transition: var(--tran-03);
            border: 1px solid var(--border);
        }

        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(54, 48, 45, 0.1);
        }

        .action-card i {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: var(--accent);
        }

        /* Status Colors for Sales */
        .sales-status-pending { 
            background: rgba(222, 154, 69, 0.1);
            color: var(--warning);
        }
        
        .sales-status-completed {
            background: rgba(166, 171, 138, 0.1);
            color: var(--success);
        }

        /* Status Colors for Inventory */
        .inventory-status-low {
            background: rgba(223, 92, 54, 0.1);
            color: var(--status-critical);
        }

        .inventory-status-good {
            background: rgba(166, 171, 138, 0.1);
            color: var(--status-good);
        }
    </style>
</head>

<body>
    <?php include '../reusable/sidebar.php'; ?>
    <section class="panel">
        <?php include '../reusable/navbarNoSearch.html'; ?>

        <div class="dashboard-container">
            <!-- Quick Actions -->
            <div class="quick-actions">
                <a href="customerorder.php" class="action-card">
                    <i class='bx bx-cart'></i>
                    <h3>New Order</h3>
                </a>
                <a href="stockactivitylog.php" class="action-card">
                    <i class='bx bx-package'></i>
                    <h3>Stock Movement</h3>
                </a>
                <a href="reports.php" class="action-card">
                    <i class='bx bx-line-chart'></i>
                    <h3>Reports</h3>
                </a>
            </div>

            <!-- Sales Section -->
            <div class="section-divider">
                <h2>Sales Overview</h2>
            </div>
            <div class="sales-section">
                <div class="stats-container">
                    <!-- Sales Stats -->
                </div>
                <div class="chart-container">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <!-- Inventory Section -->
            <div class="section-divider">
                <h2>Inventory Overview</h2>
            </div>
            <div class="inventory-section">
                <div class="stats-container">
                    <!-- Inventory Stats -->
                </div>
                <div class="chart-container">
                    <canvas id="inventoryChart"></canvas>
                </div>
            </div>
        </div>

        <?php include_once("../reusable/footer.php"); ?>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Sales Chart Configuration
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Sales',
                    data: [12, 19, 3, 5, 2, 3],
                    borderColor: '#df5c36',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Monthly Sales Trend'
                    }
                }
            }
        });

        // Inventory Chart Configuration
        const inventoryCtx = document.getElementById('inventoryChart').getContext('2d');
        new Chart(inventoryCtx, {
            type: 'bar',
            data: {
                labels: ['Low Stock', 'Good Stock', 'Excess Stock'],
                datasets: [{
                    label: 'Stock Levels',
                    data: [12, 19, 3],
                    backgroundColor: [
                        '#df5c36',
                        '#a6ab8a',
                        '#598777'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Current Stock Status'
                    }
                }
            }
        });
    </script>
</body>
</html>