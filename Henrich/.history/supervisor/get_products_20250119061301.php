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
    http_response_code(500);
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
