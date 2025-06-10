<?php
require_once '../../database/dbconnect.php';
header('Content-Type: application/json');

try {
    if (!isset($_GET['productcode'])) {
        throw new Exception('Product code is required');
    }

    $productCode = $_GET['productcode'];
    
    $sql = "SELECT 
                p.productcode,
                p.productname,
                p.productweight,
                p.productcategory,
                p.unit_price as productprice,
                p.piecesperbox,
                COALESCE(i.onhandquantity, 0) as onhandquantity,
                CASE 
                    WHEN i.onhandquantity IS NULL THEN 0
                    WHEN i.availablequantity IS NULL THEN i.onhandquantity
                    ELSE i.availablequantity
                END as availablequantity,
                CASE 
                    WHEN i.onhandquantity = 0 OR i.onhandquantity IS NULL THEN 'Out of Stock'
                    WHEN i.availablequantity < 5 THEN 'Low Stock'
                    ELSE 'In Stock'
                END as stock_status
            FROM products p 

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
            'productprice' => $row['productprice'],
            'onhandquantity' => (int)$row['onhandquantity'],
            'availablequantity' => (int)$row['availablequantity']
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