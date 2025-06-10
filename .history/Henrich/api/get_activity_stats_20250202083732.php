<?php
require_once '../includes/config.php';
header('Content-Type: application/json');

try {
    // Get trends data with proper date formatting
    $trends_query = "SELECT 
        DATE_FORMAT(dateencoded, '%Y-%m-%d') as date,
        COUNT(*) as total,
        SUM(CASE WHEN movement_type = 'IN' THEN 1 ELSE 0 END) as ins,
        SUM(CASE WHEN movement_type = 'OUT' THEN 1 ELSE 0 END) as outs
    FROM stockmovement 
    WHERE dateencoded >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
    GROUP BY DATE_FORMAT(dateencoded, '%Y-%m-%d')
    ORDER BY date ASC";

    $trends_result = $conn->query($trends_query);
    $trends_data = [];
    
    while ($row = $trends_result->fetch_assoc()) {
        $trends_data[] = [
            'x' => $row['date'],
            'y' => (int)$row['total'],
            'ins' => (int)$row['ins'],
            'outs' => (int)$row['outs']
        ];
    }

    // Get distribution data
    $distribution_query = "SELECT 
        movement_type as type,
        COUNT(*) as count
    FROM stockmovement
    GROUP BY movement_type";

    $distribution_result = $conn->query($distribution_query);
    $distribution_data = [];
    
    while ($row = $distribution_result->fetch_assoc()) {
        $distribution_data[] = [
            'type' => $row['type'],
            'count' => (int)$row['count']
        ];
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
        'trends' => $trends_data,
        'distribution' => $distribution_data,
        'recent' => $recent_activities
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
