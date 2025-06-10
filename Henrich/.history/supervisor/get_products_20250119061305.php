<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once '../database/dbconnect.php';

    // Basic input validation
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    
    // Simple query without subqueries
    $sql = "SELECT 
        p.productcode,
        p.productname,
        p.productcategory,
        p.unit_price,
        p.onhandquantity
    FROM products p 
    WHERE p.productstatus = 'Active' 
    AND (p.productcode LIKE ? OR p.productname LIKE ?)";
    
    // Debug log
    error_log("Search: " . $search);
    error_log("SQL: " . $sql);
    
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
        $products[] = [
            'id' => $row['productcode'],
            'text' => $row['productcode'] . ' - ' . $row['productname'],
            'productname' => $row['productname'],
            'productprice' => $row['unit_price'],
            'onhandquantity' => (int)$row['onhandquantity']
        ];
    }
    
    // Debug log
    error_log("Found products: " . count($products));
    
    header('Content-Type: application/json');
    echo json_encode([
        'results' => $products,
        'pagination' => ['more' => false]
    ]);
    
} catch (Exception $e) {
    error_log("Error in get_products.php: " . $e->getMessage());
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}
