<?php
require_once '../../../includes/config.php';
require_once '../../../includes/session.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized access']));
}

$riskLevel = $_GET['risk'] ?? 'all';

try {
    $response = [
        'overview' => getComplianceOverview($conn),
        'risks' => getRiskAssessment($conn, $riskLevel),
        'audits' => getAuditHistory($conn),
        'regulations' => getRegulationStatus($conn)
    ];
    
    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function getComplianceOverview($conn) {
    $query = "SELECT 
        (SELECT COUNT(*) FROM compliance_issues WHERE status = 'open') as open_issues,
        (SELECT AVG(compliance_score) FROM compliance_assessments 
         WHERE assessment_date >= DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH)) as compliance_score,
        (SELECT risk_level FROM risk_assessments 
         ORDER BY assessment_date DESC LIMIT 1) as current_risk_level
    FROM dual";
    
    $result = $conn->query($query);
    return $result->fetch_assoc();
}
