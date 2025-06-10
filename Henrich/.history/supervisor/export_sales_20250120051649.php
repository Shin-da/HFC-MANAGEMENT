<?php
require '../database/dbconnect.php';
require '../includes/.php';
$days = isset($_GET['days']) ? intval($_GET['days']) : 30;

// Query sales data
$sql = "
    SELECT 
        DATE(ol.orderdate) as date,
        co.ordertype,
        COUNT(DISTINCT ol.orderid) as order_count,
        SUM(ol.quantity * ol.productprice) as daily_sales
    FROM orderlog ol
    JOIN customerorder co ON ol.orderid = co.orderid
    WHERE ol.orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL $days DAY)
    GROUP BY DATE(ol.orderdate), co.ordertype
    ORDER BY date
";

$result = $conn->query($sql);

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="sales_report.csv"');

// Create CSV file
$output = fopen('php://output', 'w');
fputcsv($output, array('Date', 'Order Type', 'Order Count', 'Daily Sales'));

while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
