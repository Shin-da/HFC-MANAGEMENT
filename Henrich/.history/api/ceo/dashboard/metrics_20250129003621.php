<?php
require_once '../../../includes/config.php';
require_once '../../../includes/session.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized access']));
}

try {
    $response = [
        'summary' => getDashboardSummary($conn),
        'trends' => getPerformanceTrends($conn),
        'branches' => getBranchMetrics($conn),
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function getDashboardSummary($conn) {
    $query = "SELECT 
        (SELECT SUM(ordertotal) FROM customerorder 
         WHERE MONTH(orderdate) = MONTH(CURRENT_DATE)) as monthly_revenue,
        (SELECT COUNT(*) FROM customers) as total_customers,
        (SELECT COUNT(*) FROM branches WHERE status = 'active') as active_branches,
        (SELECT COUNT(DISTINCT productcode) FROM inventory) as total_products";
    
    $result = $conn->query($query);
    return $result->fetch_assoc();
}
