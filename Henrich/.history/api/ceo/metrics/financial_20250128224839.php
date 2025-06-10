<?php
require_once '../../../includes/config.php';
require_once '../../../includes/session.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized access']));
}

$period = $_GET['period'] ?? 'monthly';
$response = [];

try {
    $metrics = [
        'summary' => getFinancialSummary($conn),
        'trends' => getFinancialTrends($conn, $period),
        'performance' => getBranchPerformance($conn)
    ];
    
    echo json_encode($metrics);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function getFinancialSummary($conn) {
    $query = "SELECT 
        COALESCE(SUM(total_amount), 0) as total_revenue,
        COUNT(DISTINCT branch_id) as active_branches,
        COUNT(DISTINCT customer_id) as total_customers
    FROM orders 
    WHERE YEAR(order_date) = YEAR(CURRENT_DATE)";
    
    $result = $conn->query($query);
    return $result->fetch_assoc();
}
