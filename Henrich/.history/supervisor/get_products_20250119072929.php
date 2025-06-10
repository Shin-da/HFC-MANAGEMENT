<?php
header('Content-Type: application/json');
require_once '../database/dbconnect.php';

try {
    if (!isset($_GET['search'])) {
        throw new Exception('Search parameter is required');
    }

    $search = trim($_GET['search']);
    
    $sql = "SELECT 
                p.productcode,
                p.productname,
                p.productweight,
                p.piecesperbox,
                p.productcategory,
                p.unit_price as productprice,
                i.onhandquantity,
                COALESCE(i.availablequantity, i.onhandquantity) as availablequantity,
                CASE 
                    WHEN i.availablequantity = 0 OR i.availablequantity IS NULL THEN 'Out of Stock'
                    WHEN i.availablequantity < 5 THEN 'Low Stock'
                    ELSE 'In Stock'
                CASE 
                    WHEN i.onhandquantity = 0 OR i.onhandquantity IS NULL THEN 'Out of Stock'
                    WHEN i.onhandquantity < 5 THEN 'Low Stock'
                    ELSE 'In Stock'
                END as stock_status
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
    $stmt->bind_param("ss", $searchParam, $searchParam);
    
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
            'productcode' => $row['productcode'],
            'productweight' => $row['productweight'],
            'productprice' => $row['productprice'],
            'piecesperbox' => $row['piecesperbox'],
            'productcategory' => $row['productcategory'],
            'onhandquantity' => (int)$row['onhandquantity'],
            'availablequantity' => (int)$row['availablequantity'],
            'stock_status' => $row['stock_status']
        ];
    }
    
    echo json_encode([
        'results' => $products,
        'pagination' => ['more' => false]
    ]);

} catch (Exception $e) {
    error_log("Search error: " . $e->getMessage());
    http_response_code(200); // Keep 200 to prevent Select2 error
    echo json_encode([
        'results' => [],
        'pagination' => ['more' => false],
        'error' => $e->getMessage()
    ]);
}
