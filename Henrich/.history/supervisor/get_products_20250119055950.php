<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../database/dbconnect.php';

header('Content-Type: application/json');

try {
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    
    // Simpler query that focuses on just required fields
    $sql = "SELECT 
        p.productcode,
        p.productname,
        p.productcategory,
        p.productprice,
        p.productweight,
        IFNULL((SELECT SUM(quantity) FROM inventory WHERE productcode = p.productcode), 0) as availablequantity
    FROM products p
    WHERE p.productstatus = 'Active'
    AND (p.productcode LIKE ? OR p.productname LIKE ?)
    LIMIT 10";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $searchTerm = "%{$search}%";
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $products = [];
    
    while ($row = $result->fetch_assoc()) {
        $availableQty = (int)$row['availablequantity'];
        $products[] = [
            'id' => $row['productcode'],
            'text' => $row['productcode'] . ' - ' . $row['productname'],
            'productname' => $row['productname'],
            'productweight' => $row['productweight'],
    $result = $stmt->get_result();
    $products = [];
    
    while ($row = $result->fetch_assoc()) {
        $availableQty = (int)$row['availablequantity'];
        $products[] = [
            'id' => $row['productcode'],
            'text' => $row['productcode'] . ' - ' . $row['productname'],
            'productname' => $row['productname'],
            'productcategory' => $row['productcategory'],
            'productweight' => $row['productweight'],
            'piecesperbox' => $row['piecesperbox'],
            'availablequantity' => $availableQty,
            'stockstatus' => getStockStatus($availableQty)
        ];
    }
    
    // Send JSON response
    echo json_encode([
        'results' => $products,
        'pagination' => ['more' => false]
    ]);

} catch (Exception $e) {
    error_log("Error in get_products.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => "Error loading products: " . $e->getMessage()
    ]);
}

function getStockStatus($qty) {
    if ($qty <= 0) return 'Out of Stock';
    if ($qty < 5) return 'Low Stock';
    return 'In Stock';
}
