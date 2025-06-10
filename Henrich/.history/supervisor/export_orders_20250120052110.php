<?php
require '../reusable/redirect404.php';
require_once '../includes/config.php';
require_once '../includes/session.php';
require '../vendor/autoload.php'; // Make sure you have PhpSpreadsheet installed

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Create new spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set headers
$headers = ['Order ID', 'Reference', 'Date', 'Products', 'Total Amount', 'Status'];
$sheet->fromArray([$headers], NULL, 'A1');

// Get orders data
$orders = $conn->query("
    SELECT o.*, GROUP_CONCAT(CONCAT(p.product_name, ' x', oi.quantity) SEPARATOR ', ') as products_list
    FROM mekeni_orders o
    JOIN mekeni_order_items oi ON o.order_id = oi.order_id
    JOIN products p ON oi.product_id = p.id
    GROUP BY o.order_id
    ORDER BY o.order_date DESC
");

$row = 2;
while($order = $orders->fetch_assoc()) {
    $sheet->fromArray([[
        $order['order_id'],
        $order['reference_number'],
        $order['order_date'],
        $order['products_list'],
        $order['total_amount'],
        $order['status']
    ]], NULL, "A$row");
    $row++;
}

// Auto-size columns
foreach(range('A','F') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Set filename and headers for download
$filename = 'mekeni_orders_' . date('Y-m-d') . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

// Save file
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
