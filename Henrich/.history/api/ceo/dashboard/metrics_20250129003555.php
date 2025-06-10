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
    