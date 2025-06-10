<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start with a clean output buffer
ob_clean();
ob_start();

require_once '../database/dbconnect.php';

// Set proper headers
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

try {
    // Validate and sanitize input
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    
    // Debug log
    error_log("Search term received: " . $search);

    // Modified query to be more efficient and handle NULL values
    $sql = "SELECT 
        p.productcode,
        p.productname,
        p.productcategory,
        p.productweight,
        p.piecesperbox,
        COALESCE((
            SELECT SUM(quantity) 
            FROM stocks 
            WHERE productcode = p.productcode
        ), 0) as availablequantity
    FROM products p
    WHERE (p.productcode LIKE ? OR p.productname LIKE ?)
    AND p.productstatus = 'Active'
    LIMIT 10";

    // Debug log
    error_log("SQL Query: " . $sql);
            
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
    
    // Debug log
    error_log("Number of results: " . $result->num_rows);
    
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $availableQty = (int)$row['availablequantity'];
        $products[] = [
            'id' => $row['productcode'],
            'text' => $row['productcode'] . ' - ' . $row['productname'],
            'productname' => $row['productname'],
            'productcategory' => $row['productcategory'],
            'productweight' => $row['productweight'],
            'piecesperbox' => $row['piecesperbox'],
            'availablequantity' => $availableQty,
            'stockstatus' => $availableQty <= 0 ? 'Out of Stock' : 
                            ($availableQty < 5 ? 'Low Stock' : 'In Stock')
        ];
    }
    
    // Clean any previous output and send JSON response
    ob_clean();
    echo json_encode([
        'results' => $products,
        'pagination' => ['more' => false]
    ]);

} catch (Exception $e) {
    // Log the error
    error_log("Error in get_products.php: " . $e->getMessage());
    
    // Clean output buffer
    ob_clean();
    
    // Send error response
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => "Error loading products: " . $e->getMessage()
    ]);
}

// End output buffer and flush
ob_end_flush();
