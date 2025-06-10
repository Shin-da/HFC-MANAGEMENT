<?php
require '../database/dbconnect.php';
require '../session/session.php';

$days = isset($_GET['days']) ? intval($_GET['days']) : 30;

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
            SUM(ol.quantity * ol.productprice) as daily_sales,
            SUM(CASE WHEN co.ordertype = 'Delivery' THEN ol.quantity * ol.productprice ELSE 0 END) as online_sales
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