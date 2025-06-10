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
        p.piecesperbox
    FROM products p 
    WHERE p.productcode = ? 
    AND p.productstatus = 'Active'";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $productCode);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode([
            'success' => true,
            'productname' => $row['productname'],
            'productweight' => $row['productweight'],
            'productcategory' => $row['productcategory'],
            'piecesperbox' => $row['piecesperbox']
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
