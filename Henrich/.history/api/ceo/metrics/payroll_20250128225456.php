<?php
require_once '../../../includes/config.php';
require_once '../../../includes/session.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized access']));
}

$period = $_GET['period'] ?? 'current';

try {
    $response = [
        'summary' => getPayrollSummary($conn, $period),
        'distribution' => getSalaryDistribution($conn),
        'trends' => getPayrollTrends($conn),
        'departments' => getDepartmentBreakdown($conn)
    ];
    
    echo json_encode($response);