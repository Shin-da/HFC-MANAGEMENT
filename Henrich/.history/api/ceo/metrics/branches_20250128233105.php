<?php
require_once '../../../includes/config.php';
require_once '../../../includes/session.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized access']));
}

try {
    $response = [
        'overview' => getBranchOverview($conn),
        'performance' => getBranchPerformance($conn),
        'inventory' => getBranchInventory($conn),
        'sales' => getBranchSales($conn)
    ];
    
    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function getBranchOverview($conn) {
    $query = "SELECT 
        COUNT(*) as total_branches,
        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_branches,
        (SELECT COUNT(DISTINCT branch_id) FROM customerorder 
         WHERE orderdate = CURRENT_DATE) as branches_with_sales
    FROM branches";
    
    $result = $conn->query($query);
    return $result->fetch_assoc();
}
