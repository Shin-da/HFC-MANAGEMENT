<?php
require_once '../../database/dbconnect.php';

// Prevent any HTML output
ob_clean();
header('Content-Type: application/json');

try {
    if (!isset($_GET['productcode'])) {
        throw new Exception('Product code is required');
    }

    $productcode = $_GET['productcode'];
    
    // Add error logging
    error_log("Fetching product info for code: " . $productcode);

    // Changed from products to productlist
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
        throw new Exception("Product not found: " . $productcode);
    }

} catch (Exception $e) {
    error_log("Error in get_product_info.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}