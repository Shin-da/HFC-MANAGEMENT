<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once './access_control.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.php');
    exit;
}

// Set headers to prevent caching
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

// Get export parameters
$format = isset($_GET['format']) ? $_GET['format'] : 'excel';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'productcode';
$sortOrder = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// Validate sort parameters for security
$allowedSortFields = ['productcode', 'productname', 'productcategory', 'availablequantity', 'unit_price', 'dateupdated'];
$allowedSortOrders = ['ASC', 'DESC'];

if (!in_array($sortBy, $allowedSortFields)) {
    $sortBy = 'productcode';
}

if (!in_array($sortOrder, $allowedSortOrders)) {
    $sortOrder = 'ASC';
}

// Build query conditions based on filters
$conditions = [];
$params = [];

if (!empty($category)) {
    $conditions[] = "i.productcategory = ?";
    $params[] = $category;
}

if ($status === 'low') {
    $conditions[] = "i.availablequantity <= 10 AND i.availablequantity > 0";
} elseif ($status === 'out') {
    $conditions[] = "i.availablequantity = 0";
}

// Build the WHERE clause
$whereClause = '';
if (!empty($conditions)) {
    $whereClause = " WHERE " . implode(" AND ", $conditions);
}

// Get the current date for the filename
$today = date('Y-m-d');
$filename = "inventory_report_{$today}";

// Connect to the database and fetch data
try {
    $query = "SELECT 
        i.productcode,
        i.productname,
        i.productcategory,
        i.availablequantity,
        i.onhandquantity,
        i.unit_price,
        (i.availablequantity * i.unit_price) as total_value,
        i.dateupdated,
        CASE 
            WHEN i.availablequantity = 0 THEN 'Out of Stock'
            WHEN i.availablequantity <= 10 THEN 'Low Stock'
            ELSE 'In Stock'
        END as stock_status
    FROM inventory i
    $whereClause
    ORDER BY $sortBy $sortOrder";

    $stmt = $conn->prepare($query);
    
    // Bind parameters if any
    if (!empty($params)) {
        $types = str_repeat('s', count($params)); // Assuming all parameters are strings
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    
    $stmt->close();
    
    // If no data, show error message
    if (empty($data)) {
        echo "No inventory data found matching the selected criteria.";
        exit;
    }
    
    // Export based on requested format
    if ($format === 'excel') {
        exportToExcel($data, $filename);
    } else if ($format === 'pdf') {
        exportToPDF($data, $filename);
    } else {
        echo "Invalid export format.";
        exit;
    }
    
} catch (Exception $e) {
    error_log("Error exporting inventory data: " . $e->getMessage());
    echo "An error occurred while generating the export. Please try again later.";
    exit;
}

/**
 * Export data to Excel format
 */
function exportToExcel($data, $filename) {
    // Set headers for Excel download
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="' . $filename . '.xls"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Create a file pointer
    $output = fopen('php://output', 'w');
    
    // Add headers
    $headers = array_keys($data[0]);
    fputcsv($output, $headers, "\t");
    
    // Add rows
    foreach ($data as $row) {
        fputcsv($output, $row, "\t");
    }
    
    fclose($output);
    exit;
}

/**
 * Export data to PDF format
 */
function exportToPDF($data, $filename) {
    // Include mPDF library if using it, or another PDF library
    require_once '../includes/vendor/autoload.php'; // Adjust path as needed
    
    // Create new PDF document
    $mpdf = new \Mpdf\Mpdf([
        'margin_left' => 10,
        'margin_right' => 10,
        'margin_top' => 15,
        'margin_bottom' => 15,
    ]);
    
    // Add a title to the document
    $mpdf->SetTitle("Inventory Report");
    
    // Build the HTML for the table
    $html = '
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        h1 {
            text-align: center;
            font-size: 16px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .report-info {
            text-align: right;
            margin-bottom: 15px;
            font-size: 10px;
        }
        .out-of-stock {
            color: #e74a3b;
        }
        .low-stock {
            color: #f6c23e;
        }
    </style>
    
    <h1>Inventory Report</h1>
    <div class="report-info">Generated on: ' . date('Y-m-d H:i:s') . '</div>
    
    <table>
        <thead>
            <tr>
                <th>Product Code</th>
                <th>Product Name</th>
                <th>Category</th>
                <th>Available Qty</th>
                <th>On Hand Qty</th>
                <th>Unit Price</th>
                <th>Total Value</th>
                <th>Status</th>
                <th>Last Updated</th>
            </tr>
        </thead>
        <tbody>';
    
    // Add data rows
    foreach ($data as $row) {
        $statusClass = '';
        if ($row['stock_status'] == 'Out of Stock') {
            $statusClass = 'out-of-stock';
        } else if ($row['stock_status'] == 'Low Stock') {
            $statusClass = 'low-stock';
        }
        
        $html .= '<tr>
            <td>' . htmlspecialchars($row['productcode']) . '</td>
            <td>' . htmlspecialchars($row['productname']) . '</td>
            <td>' . htmlspecialchars($row['productcategory']) . '</td>
            <td>' . number_format($row['availablequantity']) . '</td>
            <td>' . number_format($row['onhandquantity']) . '</td>
            <td>₱' . number_format($row['unit_price'], 2) . '</td>
            <td>₱' . number_format($row['total_value'], 2) . '</td>
            <td class="' . $statusClass . '">' . $row['stock_status'] . '</td>
            <td>' . date('Y-m-d H:i', strtotime($row['dateupdated'])) . '</td>
        </tr>';
    }
    
    $html .= '</tbody></table>';
    
    // Add summary information
    $totalValue = array_sum(array_column($data, 'total_value'));
    $totalQuantity = array_sum(array_column($data, 'availablequantity'));
    $outOfStockCount = count(array_filter($data, function($item) {
        return $item['stock_status'] == 'Out of Stock';
    }));
    $lowStockCount = count(array_filter($data, function($item) {
        return $item['stock_status'] == 'Low Stock';
    }));
    
    $html .= '<div style="margin-top: 20px">
        <p><strong>Summary:</strong></p>
        <ul>
            <li>Total Products: ' . count($data) . '</li>
            <li>Total Quantity: ' . number_format($totalQuantity) . '</li>
            <li>Total Value: ₱' . number_format($totalValue, 2) . '</li>
            <li>Out of Stock Products: ' . $outOfStockCount . '</li>
            <li>Low Stock Products: ' . $lowStockCount . '</li>
        </ul>
    </div>';
    
    // Write the HTML to the PDF
    $mpdf->WriteHTML($html);
    
    // Output the PDF
    $mpdf->Output($filename . '.pdf', 'D');
    exit;
} 