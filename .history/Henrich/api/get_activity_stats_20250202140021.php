<?php
require_once '../includes/config.php';
header('Content-Type: application/json');

try {
    // Add error reporting for debugging
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Get trends data
    $trends_query = "SELECT 
        DATE(dateencoded) as date,
        COUNT(*) as total
    FROM stockmovement 
    WHERE dateencoded >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    GROUP BY DATE(dateencoded)
    ORDER BY date ASC";

    $result = $conn->query($trends_query);
    if (!$result) {
        throw new Exception("Database error: " . $conn->error);
    }

    $trends = [];
    while ($row = $result->fetch_assoc()) {
        $trends[] = [
            'x' => $row['date'],
            'y' => intval($row['total'])
        ];
    }

    // Simplified distribution query for debugging
    $distribution = [
        'labels' => ['IN', 'OUT', 'ADJUST'],
        'series' => [10, 20, 5] // Sample static data for testing
    ];

    $response = [
        'status' => 'success',
        'trends' => $trends,
        'distribution' => $distribution
    ];

    // Log the response for debugging
    error_log("API Response: " . json_encode($response));
    
    echo json_encode($response);

} catch (Exception $e) {
    error_log("Error in get_activity_stats.php: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
