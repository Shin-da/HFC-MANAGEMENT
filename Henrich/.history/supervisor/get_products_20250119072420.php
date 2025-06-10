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
                COALESCE(i.onhandquantity, 0) as onhandquantity,
                CASE 
                    WHEN i.onhandquantity IS NULL THEN 0
                    WHEN i.availablequantity IS NULL THEN i.onhandquantity
                    ELSE i.availablequantity
                END as availablequantity,
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
        'error' => $e->getMessage()
    ]);
}
