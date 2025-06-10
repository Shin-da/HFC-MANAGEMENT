<?php
require_once '../../../includes/config.php';
require_once '../../../includes/session.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized access']));
}

$period = $_GET['period'] ?? 'monthly';

try {
    $branchPerformance = [
        'metrics' => getBranchMetrics($conn),
        'performance' => getBranchPerformance($conn, $period),
        'topBranches' => getTopPerformingBranches($conn)
    ];
    
    echo json_encode($branchPerformance);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
