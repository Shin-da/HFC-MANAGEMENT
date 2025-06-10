<?php
require '../database/dbconnect.php';

header('Content-Type: application/json');

try {
    $search = isset($_GET['search']) ? $_GET['search'] : '';

    $sql = "SELECT productcode, productname, productweight, productcategory, productprice 
            FROM produc
            WHERE (productcode LIKE ? OR productname LIKE ?) 
            AND productstatus = 'Active'
            ORDER BY productcode ASC 
            LIMIT 10";

    $searchTerm = "%$search%";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = [
            'id' => $row['productcode'],
            'text' => sprintf("%s - %s (%.2fkg)", 
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
    
    echo json_encode(['results' => $products]);

} catch (Exception $e) {
    error_log("Error in get_products.php: " . $e->getMessage());
    echo json_encode([
        'error' => true,
        'message' => 'Failed to load products: ' . $e->getMessage()
    ]);
}
