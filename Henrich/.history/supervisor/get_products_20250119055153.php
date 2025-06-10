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
    
    $sql = "SELECT 
        p.productcode,
        p.productname,
        p.productcategory,
        p.productweight,
        p.piecesperbox,
        COALESCE(SUM(s.quantity), 0) as availablequantity
    FROM products p
    LEFT JOIN stocks s ON p.productcode = s.productcode
    WHERE (p.productcode LIKE ? OR p.productname LIKE ?)
    AND p.productstatus = 'Active'
    GROUP BY p.productcode, p.productname
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
            'text' => $row['productcode'] . ' - ' . $row['productname'],
            'productname' => $row['productname'],
            'productcategory' => $row['productcategory'],
            'productweight' => $row['productweight'],
            'piecesperbox' => $row['piecesperbox'],
            'availablequantity' => (int)$row['availablequantity']
        ];
    }
    
    // Clean output buffer before sending response
    ob_clean();
    echo json_encode([
        'results' => $products,
        'pagination' => ['more' => false]
    ]);
    exit;

} catch (Exception $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
    exit;
}
