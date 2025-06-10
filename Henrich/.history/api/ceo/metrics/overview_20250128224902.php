<?php
require_once '../../../includes/config.php';
require_once '../../../includes/session.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized access']));
}

try {
    $response = [
        'revenue' => getRevenueOverview($conn),
        'branches' => getBranchesOverview($conn),
        'inventory' => getInventoryOverview($conn),
        'employees' => getEmployeesOverview($conn)
    ];
    
    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function getRevenueOverview($conn) {
    $query = "SELECT 
        SUM(total_amount) as total_revenue,
        AVG(total_amount) as average_daily,
        COUNT(*) as total_orders
    FROM orders 
    WHERE MONTH(order_date) = MONTH(CURRENT_DATE())";
    
    $result = $conn->query($query);
    return $result->fetch_assoc();
}
