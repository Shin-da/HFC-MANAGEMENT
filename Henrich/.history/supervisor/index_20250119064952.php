<?php
require '../session/session.php';
require '../database/dbconnect.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Consolidated Analytics Queries
$dashboard_data = [
    // Basic Metrics
    'metrics' => $conn->query("
        SELECT 
            (SELECT COUNT(*) FROM inventory WHERE availablequantity <= reorderpoint) as low_stock_count,
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
            reorderpoint,
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

