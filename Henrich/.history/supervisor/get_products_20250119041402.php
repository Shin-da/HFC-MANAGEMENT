<?php
require '../database/dbconnect.php';

header('Content-Type: application/json');

try {
    $search = isset($_GET['search']) ? $_GET['search'] : '';

    $sql = "SELECT productcode, productname, productweight, productcategory, productprice 
            FROM productlist 
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
    
    
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = [
            'id' => $row['productcode'],
            'text' => $row['productcode'] . ' - ' . $row['productname'],
            'productname' => $row['productname'],
            'productcategory' => $row['productcategory']
        ];
    }
    
    echo json_encode(['results' => $products]);

} catch (Exception $e) {
    error_log("Error in get_products.php: " . $e->getMessage());
    echo json_encode([
        'error' => true,
        'message' => 'Failed to load products'
    ]);
}
