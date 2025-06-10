<?php
require_once '../includes/config.php';
header('Content-Type: application/json');

try {
    // Add error reporting for debugging
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Get trends data with proper date format
    $trends_query = "SELECT 
        DATE_FORMAT(dateencoded, '%Y-%m-%d') as date,
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
            'x' => strtotime($row['date']) * 1000, // Convert to milliseconds
            'y' => (int)$row['total']
        ];
    }

    // Get actual distribution data
    $distribution_query = "SELECT 
        movement_type,
        COUNT(*) as count
    FROM stockmovement 
    GROUP BY movement_type";

    $dist_result = $conn->query($distribution_query);
    $distribution_data = [];
    $labels = [];
    $series = [];
    
    while ($row = $dist_result->fetch_assoc()) {
        $labels[] = $row['movement_type'];
        $series[] = (int)$row['count'];
    }

    $response = [
        'status' => 'success',
        'debug' => true,
        'trends' => $trends,
        'distribution' => [
            'labels' => $labels,
            'series' => $series
        ]
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
