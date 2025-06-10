<?php
    require_once '../../includes/config.php';
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
                p.unit_price as unit_price,
                p.packsperbox,
                i.onhandquantity,
                COALESCE(i.availablequantity, i.onhandquantity) as availablequantity,
                CASE 
                    WHEN i.availablequantity = 0 OR i.availablequantity IS NULL THEN 'Out of Stock'
                    WHEN i.availablequantity < 5 THEN 'Low Stock'
                    ELSE 'In Stock'
                END as stock_status
            FROM products p 
            LEFT JOIN inventory i ON p.productcode = i.productcode 
            WHERE p.productcode = ? 
            AND p.productstatus = 'Active'";
    
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
        $onhand = (int)$row['onhandquantity'];
        $available = (int)$row['availablequantity'];
        $unavailable = $onhand - $available;

        echo json_encode([
            'success' => true,
            'productname' => $row['productname'],
            'productweight' => $row['productweight'],
            'productcategory' => $row['productcategory'],
            'packsperbox' => $row['packsperbox'],
            'unit_price' => $row['unit_price'],
            'onhandquantity' => $onhand,
            'availablequantity' => $available,
            'unavailable_quantity' => $unavailable,
            'stock_status' => $row['stock_status'],
            'stock_details' => [
                'total' => $onhand,
                'available' => $available,
                'unavailable' => $unavailable,
                'reason' => $unavailable > 0 ? 'Reserved, damaged, or pending return' : null
            ]
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

$conn->close();