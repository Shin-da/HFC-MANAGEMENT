<?php
require_once '../includes/session.php';
require_once '../includes/config.php';

// Ensure user is logged in and has appropriate permissions
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'supervisor') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

// Get number of days from request, default to 30
$days = isset($_GET['days']) ? intval($_GET['days']) : 30;

// Validate days parameter
if ($days <= 0 || $days > 365) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid days parameter']);
    exit;
}

try {
    // Get trends data
    $trends_query = "SELECT 
        DATE(dateupdated) as date,
        SUM(availablequantity * unit_price) as total_value
    FROM inventory
    WHERE dateupdated >= DATE_SUB(CURRENT_DATE, INTERVAL ? DAY)
    GROUP BY DATE(dateupdated)
    ORDER BY date ASC";
    
    $stmt = $conn->prepare($trends_query);
    $stmt->bind_param('i', $days);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $trends_data = [
        'dates' => [],
        'values' => []
    ];
    
    while ($row = $result->fetch_assoc()) {
        $trends_data['dates'][] = $row['date'];
        $trends_data['values'][] = (float)$row['total_value'];
    }
    
    // Set appropriate headers
    header('Content-Type: application/json');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
    
    echo json_encode($trends_data);
    
} catch (Exception $e) {
    error_log("Error fetching trends data: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch trends data']);
}
