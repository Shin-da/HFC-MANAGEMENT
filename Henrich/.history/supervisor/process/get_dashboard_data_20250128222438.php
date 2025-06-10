<?php
require_once '../database/dbconnect.php';
require_once '../session/session.php';

$days = isset($_GET['days']) ? intval($_GET['days']) : 30;

$dashboard_data = [
    'metrics' => $conn->query("
        SELECT 
            (SELECT COUNT(*) FROM inventory WHERE availablequantity <= 5) as low_stock_count,
            (SELECT COUNT(*) FROM inventory WHERE availablequantity = 0) as out_of_stock_count,
            (SELECT COUNT(*) FROM customerorder WHERE DATE(orderdate) = CURRENT_DATE) as today_orders,
            (SELECT COUNT(*) FROM customerorder WHERE ordertype = 'Online' AND DATE(orderdate) = CURRENT_DATE) as today_online_orders,
            (SELECT COUNT(*) FROM customerorder WHERE ordertype = 'Delivery' AND DATE(orderdate) = CURRENT_DATE) as today_delivery_orders,
            (SELECT COALESCE(SUM(ordertotal), 0) FROM customerorder WHERE ordertype = 'Walk-in' AND DATE(orderdate) = CURRENT_DATE) as today_walkin_revenue,
            (SELECT COALESCE(SUM(ordertotal), 0) FROM customerorder WHERE ordertype = 'Online' AND DATE(orderdate) = CURRENT_DATE) as today_online_revenue,
            (SELECT COALESCE(SUM(ordertotal), 0) FROM customerorder WHERE ordertype = 'Delivery' AND DATE(orderdate) = CURRENT_DATE) as today_delivery_revenue
    ")->fetch_assoc(),
    
    'sales_trends' => $conn->query("
        SELECT 
            DATE(ol.orderdate) as date,
            COUNT(DISTINCT ol.orderid) as order_count,
            SUM(ol.quantity * ol.unit_price) as daily_sales,
            SUM(CASE WHEN co.ordertype = 'Online' THEN ol.quantity * ol.productprice ELSE 0 END) as online_sales,
            SUM(CASE WHEN co.ordertype = 'Delivery' THEN ol.quantity * ol.productprice ELSE 0 END) as delivery_sales,
            SUM(CASE WHEN co.ordertype = 'Walk-in' THEN ol.quantity * ol.productprice ELSE 0 END) as walkin_sales
        FROM orderlog ol
        JOIN customerorder co ON ol.orderid = co.orderid
        WHERE ol.orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL $days DAY)
        GROUP BY DATE(ol.orderdate)
        ORDER BY date
    ")->fetch_all(MYSQLI_ASSOC),

    'category_performance' => $conn->query("
        SELECT 
            p.productcategory,
            COUNT(ol.orderid) as order_count,
            SUM(ol.quantity * ol.productprice) as revenue
        FROM products p
        LEFT JOIN orderlog ol ON p.productcode = ol.productcode
        WHERE ol.orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL $days DAY)
        GROUP BY p.productcategory
        ORDER BY revenue DESC
    ")->fetch_all(MYSQLI_ASSOC),

    'inventory_status' => $conn->query("
        SELECT 
            i.productcode,
            p.productname,
            p.productcategory,
            i.availablequantity,
            COUNT(ol.orderid) as monthly_demand,
            SUM(ol.quantity) as total_quantity_sold
        FROM inventory i
        JOIN products p ON i.productcode = p.productcode
        LEFT JOIN orderlog ol ON p.productcode = ol.productcode
        AND ol.orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
        GROUP BY i.productcode, p.productname, p.productcategory, i.availablequantity
    ")->fetch_all(MYSQLI_ASSOC),

    'recent_orders' => [
        'walk_in' => $conn->query("
            SELECT orderdate, customername, ordertotal, status, timeoforder, orderdescription, ordertype
            FROM customerorder 
            WHERE ordertype = 'Walk-in'
            AND orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)
            ORDER BY orderdate DESC, timeoforder DESC 
            LIMIT 5
        ")->fetch_all(MYSQLI_ASSOC),

        'online' => $conn->query("
            SELECT orderdate, customername, ordertotal, status, timeoforder, orderdescription, ordertype
            FROM customerorder 
            WHERE ordertype = 'Online'
            AND orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)
            ORDER BY orderdate DESC, timeoforder DESC 
            LIMIT 5
        ")->fetch_all(MYSQLI_ASSOC),

        'delivery' => $conn->query("
            SELECT orderdate, customername, ordertotal, status, timeoforder, orderdescription, ordertype
            FROM customerorder 
            WHERE ordertype = 'Delivery'
            AND orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)
            ORDER BY orderdate DESC, timeoforder DESC 
            LIMIT 5
        ")->fetch_all(MYSQLI_ASSOC)
    ]
];

header('Content-Type: application/json');
echo json_encode($dashboard_data);
