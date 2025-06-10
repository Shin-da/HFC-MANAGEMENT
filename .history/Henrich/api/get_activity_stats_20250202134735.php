<?php
require_once '../includes/config.php';
header('Content-Type: application/json');

try {
    // Get trends data
    $trends_query = "SELECT 
        DATE(dateencoded) as date,
        COUNT(*) as total,
        COUNT(CASE WHEN movement_type = 'IN' THEN 1 END) as ins,
        COUNT(CASE WHEN movement_type = 'OUT' THEN 1 END) as outs
    FROM stockmovement 
    WHERE dateencoded >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    GROUP BY DATE(dateencoded)
    ORDER BY date ASC";

    $result = $conn->query($trends_query);
    $trends = [];
    while ($row = $result->fetch_assoc()) {
        $trends[] = [
            'x' => strtotime($row['date']) * 1000, // Convert to milliseconds
            'y' => intval($row['total']),
            'in' => intval($row['ins']),
            'out' => intval($row['outs'])
        ];
    }

    // Get distribution data
    $distribution_query = "SELECT 
        movement_type,
        COUNT(*) as count
    FROM stockmovement 
    GROUP BY movement_type";

    $result = $conn->query($distribution_query);
    $distribution = [
        'labels' => [],
        'series' => []
    ];
    
    while ($row = $result->fetch_assoc()) {
        $distribution['labels'][] = $row['movement_type'];
        $distribution['series'][] = intval($row['count']);
    }

    // Recent activities
    $recent_query = "SELECT 
        sm.*,
        p.productname
    FROM stockmovement sm
    LEFT JOIN inventory p ON sm.productcode = p.productcode
    ORDER BY sm.dateencoded DESC
    LIMIT 10";

    $recent_result = $conn->query($recent_query);
    $recent_activities = [];
    while ($row = $recent_result->fetch_assoc()) {
        $recent_activities[] = $row;
    }

    echo json_encode([
        'status' => 'success',
        'trends' => $trends,
        'distribution' => $distribution,
        'recent' => $recent_activities
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
