<?php
// Error handling setup
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

header('Content-Type: application/json');

try {
    // Include the config file with database connection
    require_once '../../includes/config.php';
    
    if (!isset($_GET['search'])) {
        throw new Exception('Search parameter is required');
    }

    $search = trim($_GET['search']);
    
    // Simplified query for troubleshooting
    $sql = "SELECT 
                p.productcode,
                p.productname,
                p.productweight,
                p.unit_price as unit_price,
                COALESCE(i.onhandquantity, 0) as onhandquantity
            FROM products p 
            LEFT JOIN inventory i ON p.productcode = i.productcode 
            WHERE (p.productcode LIKE ? OR p.productname LIKE ?) 
            AND p.productstatus = 'Active'
            LIMIT 10";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("MySQL prepare error: " . $conn->error);
        throw new Exception("Query preparation failed");
    }

    $searchParam = "%{$search}%";
    if (!$stmt->bind_param("ss", $searchParam, $searchParam)) {
        error_log("MySQL bind error: " . $stmt->error);
        throw new Exception("Parameter binding failed");
    }
    
    if (!$stmt->execute()) {
        error_log("MySQL execute error: " . $stmt->error);
        throw new Exception("Query execution failed");
    }
    
    $result = $stmt->get_result();
    $products = [];
    
    while ($row = $result->fetch_assoc()) {
        $onhand = (int)$row['onhandquantity'];
        $products[] = [
            'id' => $row['productcode'],
            'text' => $row['productcode'] . ' - ' . $row['productname'],
            'productname' => $row['productname'],
            'productweight' => $row['productweight'],
            'unit_price' => $row['unit_price'],
            'onhandquantity' => $onhand,
            'stock_status' => $onhand <= 0 ? 'Out of Stock' : ($onhand < 5 ? 'Low Stock' : 'In Stock')
        ];
    }
    
    echo json_encode([
        'results' => $products,
        'pagination' => ['more' => false]
    ]);

} catch (Exception $e) {
    error_log("Search error: " . $e->getMessage());
    http_response_code(200); // Keep 200 for Select2
    echo json_encode([
        'results' => [],
        'pagination' => ['more' => false],
        'error' => $e->getMessage()
    ]);
}
