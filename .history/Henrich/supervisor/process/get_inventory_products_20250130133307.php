<?php
require_once '../../includes/config.php';

header('Content-Type: application/json');

try {
    $search = $_GET['search'] ?? '';
    
    $sql = "SELECT 
        p.productcode as id,
        CONCAT(p.productcode, ' - ', p.productname) as text,
        p.productname,
        p.productweight,
        p.piecesperbox
    FROM products p
    WHERE (p.productcode LIKE ? OR p.productname LIKE ?)
    AND p.productstatus = 'Active'
    LIMIT 10";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Query preparation failed");
    }

    $searchParam = "%{$search}%";
    $stmt->bind_param("ss", $searchParam, $searchParam);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    
    echo json_encode([
        'results' => $products
    ]);

} catch (Exception $e) {
    error_log("Search error: " . $e->getMessage());
    echo json_encode([
        'results' => [],
        'error' => $e->getMessage()
    ]);
}
