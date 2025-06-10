<?php
require_once '../../../includes/config.php';
require_once '../../../includes/session.php';
require_once '../../../includes/ReportGenerator.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized access']));
}

$data = json_decode(file_get_contents('php://input'), true);
$reportType = $data['type'] ?? 'consolidated';

try {
    $generator = new ReportGenerator($conn);
    $reportData = [];
    
    switch ($reportType) {
        case 'consolidated':
            $reportData = $generator->generateConsolidatedReport();
            break;
        case 'financial':
            $reportData = $generator->generateFinancialReport();
            break;
        case 'operational':
            $reportData = $generator->generateOperationalReport();
            break;
        case 'hr':
            $reportData = $generator->generateHRReport();
            break;
    }

    $filename = generateReportFile($reportData, $reportType);
    
    echo json_encode([
        'success' => true,
        'downloadUrl' => "/reports/downloads/{$filename}",
        'reportId' => saveReportRecord($conn, $filename, $reportType)
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function generateReportFile($data, $type) {
    $timestamp = date('Y-m-d_His');
    $filename = "report_{$type}_{$timestamp}.pdf";
    // Implementation of PDF generation...
    return $filename;
}
