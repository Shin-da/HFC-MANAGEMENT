<?php
require_once '../../../includes/config.php';
require_once '../../../includes/session.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized access']));
}

$campaignType = $_GET['type'] ?? 'all';

try {
    $response = [
        'overview' => getMarketingOverview($conn),
        'campaigns' => getCampaignPerformance($conn, $campaignType),
        'channels' => getChannelMetrics($conn),
        'budget' => getBudgetAllocation($conn)
    ];
    
    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function getMarketingOverview($conn) {
    $query = "SELECT 
        COUNT(*) as active_campaigns,
        AVG(roi_percentage) as avg_roi,
        SUM(leads_converted) / SUM(total_leads) * 100 as conversion_rate
    FROM marketing_campaigns
    WHERE status = 'active'";
    
    $result = $conn->query($query);
    return $result->fetch_assoc();
}
