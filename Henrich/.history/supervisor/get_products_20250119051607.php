<?php
require '../database/dbconnect.php';

header('Content-Type: application/json');

try {
    $search = isset($_GET['search']) ? $_GET['search'] : '';

    // Enhanced search query with better product information
    $sql = "SELECT 
        productcode,
        productname,
        productweight,
        productcategory,
        productprice,
        piecesperbox
    FROM productlist 
    WHERE (
        productcode LIKE ? OR 
        productname LIKE ? OR 
        productcategory LIKE ?
    )
    AND productstatus != 'Inactive'
    ORDER BY 
        CASE WHEN productcode LIKE ? THEN 1
             WHEN productname LIKE ? THEN 2
             ELSE 3
        END,
        productcode ASC 
    LIMIT 15";
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = [
            'id' => str_pad($row['productcode'], 3, '0', STR_PAD_LEFT),
            'text' => sprintf("%03d - %s (%.2fkg)", 
                $row['productcode'],
                $row['productname'],
                $row['productweight']
            ),
            'productname' => $row['productname'],
            'productweight' => $row['productweight'],
            'productcategory' => $row['productcategory'],
            'productprice' => $row['productprice']
        ];
    }
    
    error_log("Products found: " . json_encode($products)); // Debug log
    echo json_encode(['results' => $products]);

} catch (Exception $e) {
    error_log("Error in get_products.php: " . $e->getMessage());
    echo json_encode([
        'error' => true,
        'message' => 'Failed to load products: ' . $e->getMessage()
    ]);
}
