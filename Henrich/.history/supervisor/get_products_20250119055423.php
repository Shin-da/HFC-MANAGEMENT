<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start with a clean output buffer
ob_clean();
ob_start();

require_once '../database/dbconnect.php';

// Set proper headers
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

try {
    // Validate and sanitize input
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    
    // Debug log
    error_log("Search term received: " . $search);

    // Modified query to be more efficient and handle NULL values
    $sql = "SELECT 
        p.productcode,
        p.productname,
        p.productcategory,
        p.productweight,
        p.piecesperbox,
        COALESCE((
            SELECT SUM(quantity) 
            FROM stocks 
            WHERE productcode = p.productcode
        ), 0) as availablequantity
    FROM products p
    WHERE (p.productcode LIKE ? OR p.productname LIKE ?)
    AND p.productstatus = 'Active'
    LIMIT 10";

    // Debug log
    while ($row = $result->fetch_assoc()) {
        $products[] = [
            'id' => $row['productcode'],
            'text' => $row['productcode'] . ' - ' . $row['productname'],
            'productname' => $row['productname'],
            'productcategory' => $row['productcategory'],
            'productweight' => $row['productweight'],
            'piecesperbox' => $row['piecesperbox'],
            'availablequantity' => (int)$row['availablequantity']
        ];
    }
    
    // Clean output buffer before sending response
    ob_clean();
    echo json_encode([
        'results' => $products,
        'pagination' => ['more' => false]
    ]);
    exit;

} catch (Exception $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
    exit;
}
