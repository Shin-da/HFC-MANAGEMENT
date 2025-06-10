<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../database/dbconnect.php';
header('Content-Type: application/json');

try {
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    
    // Updated query to match your actual table structure
    $sql = "SELECT 
        productcode,
        productname,
        productcategory,
        unit_price,
        onhandquantity,
        dateupdated
    FROM products 
    WHERE (productcode LIKE ? OR productname LIKE ?)
    AND productstatus = 'Active'
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
        $qty = (int)$row['onhandquantity'];
        $products[] = [
            'id' => $row['productcode'],
            'text' => $row['productcode'] . ' - ' . $row['productname'],
            'productname' => $row['productname'],
            'productcategory' => $row['productcategory'],
            'productprice' => $row['unit_price'],
            'onhandquantity' => $qty, // Using onhandquantity instead of availablequantity
            'stockstatus' => getStockStatus($qty)
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

function getStockStatus($qty) {
    if ($qty <= 0) return 'Out of Stock';
    if ($qty < 5) return 'Low Stock';
    return 'In Stock';
}
