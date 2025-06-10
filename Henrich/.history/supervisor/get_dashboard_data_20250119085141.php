<?php
require '../database/dbconnect.php';
require '../session/session.php';

$days = isset($_GET['days']) ? intval($_GET['days']) : 30;

// Reuse the dashboard data query structure from index.php but modify the date ranges
$dashboard_data = [
    'metrics' => $conn->query("
        SELECT 
            (SELECT COUNT(*) FROM inventory WHERE availablequantity <= 5) as low_stock_count,
            (SELECT COUNT(*) FROM inventory WHERE availablequantity = 0) as out_of_stock_count,
            (SELECT COUNT(*) FROM customerorder WHERE DATE(orderdate) = CURRENT_DATE) as today_orders,
            (SELECT COUNT(*) FROM customerorder WHERE ordertype = 'Delivery' AND DATE(orderdate) = CURRENT_DATE) as today_online_orders
    ")->fetch_assoc(),
    
    'sales_trends' => $conn->query("
        SELECT 
            DATE(ol.orderdate) as date,
            COUNT(DISTINCT ol.orderid) as order_count,
            SUM(ol.quantity * ol.productprice) as daily_sales
        FROM orderlog ol
        WHERE ol.orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL $days DAY)
        GROUP BY DATE(ol.orderdate)
        ORDER BY date
    ")->fetch_all(MYSQLI_ASSOC)
    // Add other queries as needed
];

header('Content-Type: application/json');
echo json_encode($dashboard_data);
