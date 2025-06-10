<?php
require_once '../../../includes/config.php';
require_once '../../../includes/session.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized access']));
}

$data = json_decode(file_get_contents('php://input'), true);
$startDate = $data['start'] ?? date('Y-m-d', strtotime('-30 days'));
$endDate = $data['end'] ?? date('Y-m-d');

try {
    $response = [
        'summary' => getSalesSummary($conn, $startDate, $endDate),
        'trends' => getSalesTrends($conn, $startDate, $endDate),
        'topProducts' => getTopProducts($conn, $startDate, $endDate),
        'forecast' => generateSalesForecast($conn)
    ];
    
    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function getSalesSummary($conn, $startDate, $endDate) {
    $query = "SELECT 
        SUM(total_amount) as total_sales,
        AVG(total_amount) as avg_order_value,
        COUNT(DISTINCT order_id) as total_orders,
        COUNT(DISTINCT customer_id) as unique_customers
    FROM orders 
    WHERE order_date BETWEEN ? AND ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $startDate, $endDate);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}
