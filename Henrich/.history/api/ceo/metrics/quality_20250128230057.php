<?php
require_once '../../../includes/config.php';
require_once '../../../includes/session.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized access']));
}

$period = $_GET['period'] ?? 'monthly';

try {
    $response = [
        'metrics' => getQualityMetrics($conn, $period),
        'incidents' => getQualityIncidents($conn),
        'trends' => getQualityTrends($conn, $period),
        'actions' => getCorrectiveActions($conn)
    ];
    
    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function getQualityMetrics($conn, $period) {
    $query = "SELECT 
        AVG(quality_score) as quality_score,
        COUNT(CASE WHEN has_defect = 1 THEN 1 END) * 100.0 / COUNT(*) as defect_rate,
        (SELECT COUNT(*) FROM customer_complaints WHERE period = ?) as complaint_count
    FROM quality_inspections
    WHERE inspection_date >= DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH)";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $period);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}
