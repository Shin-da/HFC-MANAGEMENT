<?php
header('Content-Type: application/json');
require_once '../database/dbconnect.php';

try {
    if (!isset($_GET['search'])) {
        throw new Exception('Search parameter is required');
    }

    $search = trim($_GET['search']);
    
    $sql = "SELECT 
                p.productcode,
                p.productname,
                p.productweight,
                p.piecesperbox,
                p.productcategory
            FROM products p 
            WHERE (p.productcode LIKE ? OR p.productname LIKE ?) 
            AND p.productstatus = 'Active'
            LIMIT 10";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Query preparation failed: " . $conn->error);
    }

    $searchParam = "%" . $search . "%";
    $stmt->bind_param("ss", $searchParam, $searchParam);
    
    if (!$stmt->execute()) {
        throw new Exception("Query execution failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $products = [];
    
    while ($row = $result->fetch_assoc()) {
        $products[] = [
            'id' => $row['productcode'],
            'text' => $row['productcode'] . ' - ' . $row['productname'],
            'productname' => $row['productname'],
            'productweight' => $row['productweight'],
            'piecesperbox' => $row['piecesperbox'],
            'productcategory' => $row['productcategory']
        ];
    }
    
    echo json_encode([
        'results' => $products,
        'pagination' => ['more' => false]
    ]);

} catch (Exception $e) {
    error_log("Search error: " . $e->getMessage());
    http_response_code(200); // Keep 200 to prevent Select2 error
    echo json_encode([
        'results' => [],
        'pagination' => ['more' => false],
        'error' => $e->getMessage()
    ]);
}
