<?php
require_once '../../../includes/config.php';
require_once '../../../includes/session.php';
require_once '../../../includes/LiveMonitoring.php';

header('Content-Type: application/json');
header('Cache-Control: no-cache');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized access']));
}

try {
    $monitor = new LiveMonitoring($conn);
    echo json_encode($monitor->getLiveMetrics());
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
