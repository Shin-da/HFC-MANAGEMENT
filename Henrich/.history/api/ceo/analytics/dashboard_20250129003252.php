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
