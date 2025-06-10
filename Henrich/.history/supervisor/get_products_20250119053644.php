<?php
// Clean any output buffers
ob_clean();
ob_start();

require_once '../database/dbconnect.php';

// Set proper JSON headers
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

try {
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    
    $sql = "SELECT productcode, productname 
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
        $products[] = [
            'id' => $row['productcode'],
            'text' => $row['productcode'] . ' - ' . $row['productname']
        ];
    }
    
    // Clean output buffer before sending response
    ob_clean();
    echo json_encode([
        'results' => $products,
        'pagination' => ['more' => false]
            'text' => $row['productcode'] . ' - ' . $row['productname']
        ];
    }
    
    // Log the final response
    error_log("Response data: " . json_encode($products));
    
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
