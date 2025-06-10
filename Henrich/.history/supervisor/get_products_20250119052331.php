<?php
require_once '../database/dbconnect.php';

header('Content-Type: application/json');

try {
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    
    $sql = "SELECT 
        productcode as id,
        productname,
        productcategory,
        productweight,
        piecesperbox 
    FROM products 
    WHERE productcode LIKE ? OR productname LIKE ?
    LIMIT 10";
            
    $stmt = $conn->prepare($sql);
    $searchTerm = "%{$search}%";
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    
    if (!$stmt->execute()) {
        throw new Exception("Query failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $products = [];
    
    while ($row = $result->fetch_assoc()) {
        $products[] = [
            'id' => $row['id'],
            'text' => $row['id'] . ' - ' . $row['productname'],
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