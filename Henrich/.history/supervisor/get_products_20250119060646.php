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
        availablequantity,
        onhandquantity,
        unit_price
    FROM products 
    WHERE (productcode LIKE ? OR productname LIKE ?)
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
        $availableQty = (int)$row['availablequantity'];
        $products[] = [
            'id' => $row['productcode'],
            'text' => $row['productcode'] . ' - ' . $row['productname'],
            'productname' => $row['productname'],
            'productcategory' => $row['productcategory'],
            'productprice' => $row['unit_price'],
            'availablequantity' => $availableQty,
            'stockstatus' => getStockStatus($availableQty)
        ];
    }
    
    echo json_encode([
        'results' => $products,
        'pagination' => ['more' => false]
    ]);

} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    // Log the response for debugging
    error_log("Products found: " . json_encode($products));
    
    echo json_encode([
        'results' => $products,
        'pagination' => ['more' => false]
    ]);

} catch (Exception $e) {
    error_log("Error in get_products.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}

function getStockClass($qty) {
    if ($qty <= 0) return 'out-of-stock';
    if ($qty < 5) return 'low-stock';
    return 'in-stock';
}
