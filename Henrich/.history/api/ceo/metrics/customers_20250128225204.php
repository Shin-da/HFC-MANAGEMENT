<?php
require_once '../../../includes/config.php';
require_once '../../../includes/session.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized access']));
}

$days = $_GET['days'] ?? 30;

try {
    $metrics = [
        'overview' => getCustomerOverview($conn, $days),
        'segments' => getCustomerSegments($conn),
        'loyalty' => getLoyaltyMetrics($conn, $days),
        'regions' => getRegionalDistribution($conn)
    ];
    
    echo json_encode($metrics);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function getCustomerOverview($conn, $days) {
    $query = "SELECT 
        COUNT(DISTINCT customer_id) as total_customers,
        AVG(total_amount) as avg_value,
        COUNT(CASE WHEN order_date >= DATE_SUB(NOW(), INTERVAL ? DAY) THEN 1 END) / 
        COUNT(DISTINCT customer_id) * 100 as retention_rate
    FROM orders";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $days);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}
