<?php
require_once '../../../includes/config.php';
require_once '../../../includes/session.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized access']));
}

$timeframe = $_GET['timeframe'] ?? 'monthly';

try {
    $response = [
        'overview' => getSupplyChainOverview($conn, $timeframe),
        'suppliers' => getSupplierMetrics($conn),
        'inventory' => getInventoryHealth($conn),
        'logistics' => getLogisticsPerformance($conn)
    ];
    
    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function getSupplyChainOverview($conn, $timeframe) {
    $query = "SELECT 
        AVG(CASE WHEN actual_delivery <= expected_delivery THEN 100 ELSE 0 END) as otd_rate,