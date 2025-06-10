<?php
require_once '../../../includes/config.php';
require_once '../../../includes/session.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized access']));
}

try {
    $startDate = $_GET['start'] ?? date('Y-m-d', strtotime('-30 days'));
    $endDate = $_GET['end'] ?? date('Y-m-d');

    $response = [
        'summary' => getFinancialSummary($conn, $startDate, $endDate),
        'cashFlow' => getCashFlowData($conn, $startDate, $endDate),
        'revenue' => getRevenueStreams($conn, $startDate, $endDate),
        'expenses' => getExpenseAnalysis($conn, $startDate, $endDate)
    ];
    
    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function getFinancialSummary($conn, $startDate, $endDate) {
    $query = "SELECT 
        SUM(ordertotal) as gross_revenue,
        SUM(ordertotal) * 0.2 as net_profit,
        SUM(ordertotal) * 0.8 as operating_costs
    FROM customerorder 
    WHERE orderdate BETWEEN ? AND ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $startDate, $endDate);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}
