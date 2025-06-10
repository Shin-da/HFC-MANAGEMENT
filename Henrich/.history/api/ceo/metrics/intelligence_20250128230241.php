<?php
require_once '../../../includes/config.php';
require_once '../../../includes/session.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized access']));
}

$timeRange = $_GET['range'] ?? '1y';

try {
    $response = [
        'trends' => getMarketTrends($conn, $timeRange),
        'predictions' => generateGrowthPredictions($conn),
        'patterns' => analyzeConsumerPatterns($conn),
        'recommendations' => generateRecommendations($conn)
    ];
    
    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function getMarketTrends($conn, $timeRange) {
    $period = match($timeRange) {
        '1m' => 'INTERVAL 1 MONTH',
        '3m' => 'INTERVAL 3 MONTH',
        '1y' => 'INTERVAL 1 YEAR',
        default => 'INTERVAL 1 YEAR'
    };

    $query = "SELECT 
        DATE_FORMAT(date, '%Y-%m') as period,
        SUM(revenue) as revenue,
        COUNT(DISTINCT customer_id) as customer_count,
        AVG(order_value) as avg_order_value
    FROM market_analytics
    WHERE date >= DATE_SUB(CURRENT_DATE, $period)
    GROUP BY DATE_FORMAT(date, '%Y-%m')
    ORDER BY period ASC";

    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}
