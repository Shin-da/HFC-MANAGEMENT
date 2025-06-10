<?php
require_once '../database/dbconnect.php';

header('Content-Type: application/json');

try {
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    
    $sql = "SELECT productcode as id, productname, productcategory, productweight, piecesperbox 
            FROM products 
            WHERE productcode LIKE ? OR productname LIKE ?
            LIMIT 10";
            
    $stmt = $conn->prepare($sql);
    $searchTerm = "%$search%";
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = [
            'id' => $row['id'],
    ORDER BY 
        CASE WHEN productcode LIKE ? THEN 1
             WHEN productname LIKE ? THEN 2
             ELSE 3
        END,
        productcode ASC 
    LIMIT 15";

    $searchTerm = "%$search%";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $formattedCode = str_pad($row['productcode'], 3, '0', STR_PAD_LEFT);
        $products[] = [
            'id' => $formattedCode,
            'text' => sprintf("[%s] %s - %.2fkg (%d pcs/box)", 
                $formattedCode,
                $row['productname'],
                $row['productweight'],
                $row['piecesperbox']
            ),
            'productname' => $row['productname'],
            'productweight' => $row['productweight'],
            'productcategory' => $row['productcategory'],
            'productprice' => $row['productprice'],
            'piecesperbox' => $row['piecesperbox']
        ];
    }
    
    echo json_encode([
        'results' => $products,
        'pagination' => [
            'more' => false
        ]
    ]);

} catch (Exception $e) {
    error_log("Error in get_products.php: " . $e->getMessage());
    echo json_encode([
        'error' => true,
        'message' => 'Failed to load products: ' . $e->getMessage()
    ]);
}
