<?php
require_once '../../../includes/config.php';
require_once '../../../includes/session.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized access']));
}

try {
    $days = $_GET['days'] ?? 30;
    
    $response = [
        'overview' => getBranchOverview($conn),
        'performance' => getBranchPerformance($conn, $days),
        'locations' => getBranchLocations($conn),
        'directory' => getBranchDirectory($conn)
    ];
    
    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function getBranchOverview($conn) {
    $query = "SELECT 
        COUNT(*) as total_branches,
        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_branches,
        (SELECT SUM(ordertotal) FROM customerorder 
         WHERE branch_id IS NOT NULL 
         AND orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)) as total_revenue
    FROM branches";
    
    $result = $conn->query($query);
    return $result->fetch_assoc();
}
