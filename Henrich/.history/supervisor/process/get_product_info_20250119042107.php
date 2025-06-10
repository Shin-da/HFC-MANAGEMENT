<?php
require '../../database/dbconnect.php';

header('Content-Type: application/json');

try {
    $productcode = isset($_GET['productcode']) ? $_GET['productcode'] : '';

    $sql = "SELECT productname, productweight, productcategory, productprice 
            FROM productlist 
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
            'productweight' => $product['productweight'],
            'productcategory' => $product['productcategory'],
            'productprice' => $product['productprice']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Product not found'
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}