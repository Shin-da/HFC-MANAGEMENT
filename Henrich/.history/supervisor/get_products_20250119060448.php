<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../database/dbconnect.php';

header('Content-Type: application/json');

try {
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    
    // Modified query to use COALESCE and proper inventory sum
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
            'productprice' => $row['productprice'],
            'availablequantity' => $availableQty,
            'stockClass' => $availableQty <= 0 ? 'out-of-stock' : 
                          ($availableQty < 5 ? 'low-stock' : 'in-stock')
        ];
    }
    
    echo json_encode([
        'results' => $products,
        'pagination' => ['more' => false]
    ]);

} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}
