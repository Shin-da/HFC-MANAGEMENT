<?php
require_once '../../../includes/config.php';
require_once '../../../includes/session.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized access']));
}

$reportType = $_POST['type'] ?? '';
$format = $_POST['format'] ?? 'pdf';

try {
    // Generate report data
    $stmt = $conn->prepare("CALL sp_generate_executive_report(?, ?)");
    $stmt->bind_param('si', $reportType, $_SESSION['user_id']);
    $stmt->execute();

    // Get the latest report
    $query = "SELECT * FROM executive_reports 
              WHERE report_type = ? 
              ORDER BY generated_at DESC LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $reportType);
    $stmt->execute();
    $report = $stmt->get_result()->fetch_assoc();

    // Generate PDF
    if ($format === 'pdf') {
        require_once '../../../includes/PDF_Generator.php';
        $pdfPath = generatePDFReport($report);
        echo json_encode(['success' => true, 'download_url' => $pdfPath]);
    } else {
        echo json_encode(['success' => true, 'data' => $report]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
