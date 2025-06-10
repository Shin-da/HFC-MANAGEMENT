<?php
require '../includes/session.php';
require '../includes/config.php';
$start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
$end_date = $_GET['end_date'] ?? date('Y-m-d');
$type = $_GET['type'] ?? 'overview';

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="sales_report_' . date('Y-m-d') . '.xls"');
header('Cache-Control: max-age=0');

// Get data based on report type
switch ($type) {
    case 'overview':
        exportOverviewReport($conn, $start_date, $end_date);
        break;
    case 'sales':
        exportSalesReport($conn, $start_date, $end_date);
        break;
    case 'inventory':
        exportInventoryReport($conn, $start_date, $end_date);
        break;
    default:
        exportOverviewReport($conn, $start_date, $end_date);
}

function exportOverviewReport($conn, $start_date, $end_date) {
    // Get overview data
    $query = "
        SELECT 
            ordertype,
            COUNT(*) as order_count,
            SUM(ordertotal) as total_revenue,
            AVG(ordertotal) as avg_order_value
        FROM customerorder 
        WHERE orderdate BETWEEN ? AND ?
        GROUP BY ordertype
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Generate Excel content
    echo "<table border='1'>";
    echo "<tr><th colspan='4'>Sales Overview Report ($start_date to $end_date)</th></tr>";
    echo "<tr><th>Order Type</th><th>Order Count</th><th>Total Revenue</th><th>Average Order Value</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['ordertype']}</td>";
        echo "<td>{$row['order_count']}</td>";
        echo "<td>₱" . number_format($row['total_revenue'], 2) . "</td>";
        echo "<td>₱" . number_format($row['avg_order_value'], 2) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

function exportSalesReport($conn, $start_date, $end_date) {
    // Get detailed sales data
    $query = "
        SELECT 
            co.orderdate,
            co.ordertype,
            co.customername,
            co.ordertotal,
            co.status,
            ol.productname,
            ol.quantity,
            ol.unit_price
        FROM customerorder co
        JOIN orderlog ol ON co.orderid = ol.orderid
        WHERE co.orderdate BETWEEN ? AND ?
        ORDER BY co.orderdate DESC
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Generate Excel content
    echo "<table border='1'>";
    echo "<tr><th colspan='8'>Detailed Sales Report ($start_date to $end_date)</th></tr>";
    echo "<tr>
            <th>Date</th>
            <th>Order Type</th>
            <th>Customer</th>
            <th>Product</th>
            <th>Quantity</th>
            <th>Unit Price</th>
            <th>Total</th>
            <th>Status</th>
          </tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . date('Y-m-d', strtotime($row['orderdate'])) . "</td>";
        echo "<td>{$row['ordertype']}</td>";
        echo "<td>{$row['customername']}</td>";
        echo "<td>{$row['productname']}</td>";
        echo "<td>{$row['quantity']}</td>";
        echo "<td>₱" . number_format($row['unit_price'], 2) . "</td>";
        echo "<td>₱" . number_format($row['ordertotal'], 2) . "</td>";
        echo "<td>{$row['status']}</td>";
        echo "</tr>";
    }
    echo "</table>";
}

function exportInventoryReport($conn, $start_date, $end_date) {
    // Get inventory data with sales information
    $query = "
        SELECT 
            p.productcode,
            p.productname,
            i.availablequantity,
            i.onhandquantity,
            COUNT(ol.orderid) as times_ordered,
            COALESCE(SUM(ol.quantity), 0) as total_quantity_sold,
            COALESCE(AVG(ol.quantity), 0) as avg_order_quantity
        FROM products p
        LEFT JOIN inventory i ON p.productcode = i.productcode
        LEFT JOIN orderlog ol ON p.productcode = ol.productcode 
            AND ol.orderdate BETWEEN ? AND ?
        GROUP BY p.productcode, p.productname, i.availablequantity, i.onhandquantity
        ORDER BY total_quantity_sold DESC
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Generate Excel content
    echo "<table border='1'>";
    echo "<tr><th colspan='7'>Inventory Report with Sales Data ($start_date to $end_date)</th></tr>";
    echo "<tr>
            <th>Product Code</th>
            <th>Product Name</th>
            <th>Available Qty</th>
            <th>On Hand Qty</th>
            <th>Times Ordered</th>
            <th>Total Sold</th>
            <th>Avg Order Qty</th>
          </tr>";
    
    while ($row = $result->fetch_assoc()) {
        $total_sold = $row['total_quantity_sold'] ? $row['total_quantity_sold'] : 0;
        $avg_qty = $row['avg_order_quantity'] ? number_format($row['avg_order_quantity'], 2) : '0.00';
        
        echo "<tr>";
        echo "<td>{$row['productcode']}</td>";
        echo "<td>{$row['productname']}</td>";
        echo "<td>{$row['availablequantity']}</td>";
        echo "<td>{$row['onhandquantity']}</td>";
        echo "<td>{$row['times_ordered']}</td>";
        echo "<td>{$total_sold}</td>";
        echo "<td>{$avg_qty}</td>";
        echo "</tr>";
    }
    echo "</table>";
}
?>