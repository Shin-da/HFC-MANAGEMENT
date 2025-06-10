<?php
require_once '../../includes/config.php';
header('Content-Type: application/json');

try {
    if (!isset($_GET['productcode'])) {
        throw new Exception('Product code is required');
    }

    $productCode = $conn->real_escape_string($_GET['productcode']);
    
    $sql = "SELECT 
                p.productcode,
                p.productname,
                p.productweight,
                p.unit_price,
                p.productcategory,
                COALESCE(i.availablequantity, 0) as availablequantity,
                CASE 
                    WHEN COALESCE(i.availablequantity, 0) = 0 THEN 'Out of Stock'
                    WHEN COALESCE(i.availablequantity, 0) < 5 THEN 'Low Stock'
                    ELSE 'In Stock'
                END as stock_status
            FROM products p
            LEFT JOIN inventory i ON p.productcode = i.productcode
            WHERE p.productcode = ? AND p.productstatus = 'Active'";

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
            'productcode' => $row['productcode'],
            'productname' => $row['productname'],
            'productweight' => $row['productweight'],
            'unit_price' => $row['unit_price'],
            'productcategory' => $row['productcategory'],
            'availablequantity' => (int)$row['availablequantity'],
            'stock_status' => $row['stock_status']
        ]);
    } else {
        throw new Exception("Product not found");
    }

} catch (Exception $e) {
    error_log("Product info fetch error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$stmt->close();
$conn->close();