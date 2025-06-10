<?php
require_once '../../../includes/config.php';
require_once '../../../includes/session.php';

function getDashboardMetrics($conn) {
    // Get sales metrics
    $salesQuery = "SELECT 
        COUNT(DISTINCT co.orderid) as total_orders,
        SUM(ol.quantity * ol.unit_price) as total_revenue,
        COUNT(DISTINCT co.customername) as total_customers
    FROM customerorder co
    JOIN orderlog ol ON co.orderid = ol.orderid
    WHERE co.orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)";

    // Get inventory metrics
    $inventoryQuery = "SELECT 
        COUNT(DISTINCT productcode) as total_products,
        SUM(availablequantity * unit_price) as inventory_value,
        SUM(CASE WHEN availablequantity <= 10 THEN 1 ELSE 0 END) as low_stock_items
    FROM inventory";

    // Get performance trends
    $trendsQuery = "SELECT 
        DATE_FORMAT(orderdate, '%Y-%m-%d') as date,
        COUNT(*) as orders,
        SUM(ordertotal) as revenue
    FROM customerorder
    WHERE orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
    GROUP BY DATE_FORMAT(orderdate, '%Y-%m-%d')
    ORDER BY date ASC";

    try {