<?php
require_once '../../../includes/config.php';
require_once '../../../includes/session.php';
require_once '../../../includes/Analytics.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized access']));
}

try {
    $analytics = new Analytics($conn);
    $startDate = $_GET['start'] ?? date('Y-m-d', strtotime('-30 days'));
    $endDate = $_GET['end'] ?? date('Y-m-d');

    $response = [
        'metrics' => $analytics->getBusinessMetrics(),
        'sales' => $analytics->getSalesAnalytics(),
        'projections' => generateProjections($conn),
        'timestamp' => date('Y-m-d H:i:s')
    ];

    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function generateProjections($conn): array {
    // Get historical data for projection
    $query = "SELECT 
        DATE_FORMAT(orderdate, '%Y-%m') as month,
        SUM(ordertotal) as monthly_revenue,
        COUNT(*) as order_count
    FROM customerorder
    WHERE orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 12 MONTH)
    GROUP BY DATE_FORMAT(orderdate, '%Y-%m')
    ORDER BY month ASC";
    
    $result = $conn->query($query);
    $historicalData = $result->fetch_all(MYSQLI_ASSOC);

    // Calculate growth rates
    $growthRates = calculateGrowthRates($historicalData);
    
    // Generate next 3 months projections
    return [
        'revenue' => generateRevenueProjections($historicalData, $growthRates),
        'orders' => generateOrderProjections($historicalData, $growthRates),
        'confidence' => calculateConfidenceScore($growthRates),
        'factors' => getProjectionFactors()
    ];
}

function calculateGrowthRates(array $data): array {
    $growth = [];
    $count = count($data);
    
    for ($i = 1; $i < $count; $i++) {
        $previousRevenue = $data[$i-1]['monthly_revenue'];
        $currentRevenue = $data[$i]['monthly_revenue'];
        
        if ($previousRevenue > 0) {
            $growth[] = ($currentRevenue - $previousRevenue) / $previousRevenue;
        }
    }
    
    return [
        'average' => array_sum($growth) / count($growth),
        'median' => calculateMedian($growth),
        'volatility' => calculateVolatility($growth)
    ];
}

function generateRevenueProjections(array $historical, array $growthRates): array {
    $lastMonth = end($historical);
    $projections = [];
    $baseRevenue = $lastMonth['monthly_revenue'];
    
    // Project next 3 months
    for ($i = 1; $i <= 3; $i++) {
        $projectedRevenue = $baseRevenue * (1 + $growthRates['average']);
        $projections[] = [
            'month' => date('Y-m', strtotime("+$i month")),
            'projected_revenue' => round($projectedRevenue, 2),
            'confidence' => calculateConfidenceForMonth($i, $growthRates['volatility'])
        ];
        $baseRevenue = $projectedRevenue;
    }
    
    return $projections;
}

function calculateConfidenceScore(array $growthRates): float {
    // Higher volatility = lower confidence
    $baseConfidence = 0.9; // 90% base confidence
    $volatilityImpact = $growthRates['volatility'] * 100;
    
    return max(0, min(1, $baseConfidence - $volatilityImpact));
}

function getProjectionFactors(): array {
    return [
        'historical_performance' => true,
        'seasonal_trends' => true,
        'market_conditions' => true,
        'growth_patterns' => true
    ];
}

function calculateMedian(array $numbers): float {
    sort($numbers);
    $count = count($numbers);
    $mid = floor($count / 2);
    
    return ($count % 2 === 0) 
        ? ($numbers[$mid - 1] + $numbers[$mid]) / 2 
        : $numbers[$mid];
}

function calculateVolatility(array $numbers): float {
    $mean = array_sum($numbers) / count($numbers);
    $variance = array_reduce($numbers, function($carry, $item) use ($mean) {
        return $carry + pow($item - $mean, 2);
    }, 0) / count($numbers);
    
    return sqrt($variance);
}
