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