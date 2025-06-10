<?php
require_once '../database/dbconnect.php';

header('Content-Type: application/json');

try {
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    
    // Updated query to match your table structure
    $sql = "SELECT 
        productcode,
        productname,
        productcategory,
        productweight,
        piecesperbox,
        productstatus 
    FROM products 
    WHERE (CONCAT(productcode, ' - ', productname) LIKE ? 
    OR productcode LIKE ? 
    OR productname LIKE ?)
    AND productstatus = 'Active'
    LIMIT 10";
            
    $stmt = $conn->prepare($sql);
    $searchTerm = "%{$search}%";
    if (!$stmt->execute()) {
        throw new Exception("Query failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $products = [];
    
    while ($row = $result->fetch_assoc()) {
        // Simplified format with combined ID and text
        $products[] = [
            'id' => $row['productcode'],
            'text' => $row['productcode'] . ' - ' . $row['productname'],
            'productname' => $row['productname'],
            'productcategory' => $row['productcategory'],
            'productweight' => $row['productweight'],
            'piecesperbox' => $row['piecesperbox']
        ];
    }
    
    echo json_encode([
        'results' => $products,
        'pagination' => ['more' => false]
    ]);

} catch (Exception $e) {
    error_log("Product search error: " . $e->getMessage());
    echo json_encode([
        'error' => true,
        'message' => "Error loading products: " . $e->getMessage()
    ]);
}
