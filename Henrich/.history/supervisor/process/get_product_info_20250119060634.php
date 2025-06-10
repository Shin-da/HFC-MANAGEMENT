<?php
require_once '../../database/dbconnect.php';
header('Content-Type: application/json');

try {
    if (!isset($_GET['productcode'])) {
        throw new Exception('Product code is required');
    }

    $productcode = $_GET['productcode'];
    
    $sql = "SELECT 
        productname,
        productcategory,
        availablequantity,
        onhandquantity,
        unit_price as productprice
    FROM products 
    WHERE productcode = ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("s", $productcode);
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if ($product) {
        echo json_encode([
            'success' => true,
            'productname' => $product['productname'],
            'productcategory' => $product['productcategory'],
            'availablequantity' => (int)$product['availablequantity'],
            'productprice' => $product['productprice'],
            'stockstatus' => getStockStatus((int)$product['availablequantity'])
        ]);
    } else {
        throw new Exception("Product not found: " . $productcode);
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

function getStockStatus($qty) {
    if ($qty <= 0) return 'Out of Stock';
    if ($qty < 5) return 'Low Stock';
    return 'In Stock';
}