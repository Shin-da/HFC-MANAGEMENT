<?php
require_once '../../database/dbconnect.php';
header('Content-Type: application/json');

try {
    if (!isset($_GET['productcode'])) {
        throw new Exception('Product code is required');
    }

    $productCode = $_GET['productcode'];
    
    // Updated query to match your database structure
    $sql = "SELECT 
                p.productcode,
                p.productname,
                p.productweight,
                p.productcategory,
                p.unit_price as productprice,
                p.piecesperbox,
                COALESCE(i.onhandquantity, 0) as onhandquantity,
                COALESCE(i.availablequantity, 0) as availablequantity
            FROM products p 
            LEFT JOIN inventory i ON p.productcode = i.productcode 
            WHERE p.productcode = ? 
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Query preparation failed: " . $conn->error);
    }

    $stmt->bind_param("s", $productCode);
    
    if (!$stmt->execute()) {
        throw new Exception("Query execution failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode([
            'success' => true,
            'productname' => $row['productname'],
            'productweight' => $row['productweight'],
            'productcategory' => $row['productcategory'],
            'piecesperbox' => $row['piecesperbox'],
            'quantity' => (int)$row['quantity']
        ]);
    } else {
        throw new Exception("Product not found");
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}