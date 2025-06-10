<?php
header('Content-Type: application/json');
require_once '../database/dbconnect.php';

try {
    if (!isset($_GET['search'])) {
        throw new Exception('Search parameter is required');
    }

    $search = trim($_GET['search']);
    
    // Updated query to match your database structure
    $sql = "SELECT 
                p.productcode,
                p.productname,
                p.productweight,
                p.unit_price as productprice,
                COALESCE(i.onhandquantity, 0) as onhandquantity,
                i.availablequantity
            FROM products p 
            LEFT JOIN inventory i ON p.productcode = i.productcode 
            WHERE (p.productcode LIKE ? OR p.productname LIKE ?) 
            AND p.productstatus = 'Active'
            LIMIT 10";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Query preparation failed: " . $conn->error);
    }

    $searchParam = "%" . $search . "%";
    if (!$stmt->execute()) {
        throw new Exception("Query execution failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $products = [];
    
    while ($row = $result->fetch_assoc()) {
        $products[] = [
            'id' => $row['productcode'],
            'text' => $row['productcode'] . ' - ' . $row['productname'],
            'productname' => $row['productname'],
            'productprice' => number_format($row['productprice'], 2),
            'onhandquantity' => (int)$row['onhandquantity']
        ];
    }
    
    echo json_encode([
        'results' => $products,
        'pagination' => ['more' => false]
    ]);

} catch (Exception $e) {
    error_log("Search error: " . $e->getMessage());
    http_response_code(200); // Change to 200 to prevent Select2 error
    echo json_encode([
        'results' => [],
        'pagination' => ['more' => false],
        'error' => $e->getMessage()
    ]);
}
