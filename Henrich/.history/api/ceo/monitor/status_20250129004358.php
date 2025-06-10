<?php
require_once '../../../includes/config.php';
require_once '../../../includes/session.php';
require_once '../../../includes/SystemMonitor.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized access']));
}

try {
    $monitor = new SystemMonitor($conn);
    
    $response = [
        'server' => $monitor->getServerStatus(),
        'database' => $monitor->getDatabaseStatus(),
        'users' => $monitor->getActiveUsers(),
        'performance' => $monitor->getPerformanceMetrics(),
        'activity' => $monitor->getRecentActivity(),
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
