<?php
require_once '../includes/config.php';
header('Content-Type: application/json');

try {
    $days = isset($_GET['days']) ? (int)$_GET['days'] : 30;

    // Activity trends
    $trends_query = "SELECT 
        DATE(dateencoded) as date,
        COUNT(*) as total,
        SUM(CASE WHEN movement_type = 'IN' THEN totalpacks ELSE 0 END) as ins,
        SUM(CASE WHEN movement_type = 'OUT' THEN totalpacks ELSE 0 END) as outs
    FROM stockmovement 
    WHERE dateencoded >= DATE_SUB(CURRENT_DATE, INTERVAL ? DAY)
    GROUP BY DATE(dateencoded)
    ORDER BY date ASC";

    $stmt = $conn->prepare($trends_query);
    $stmt->bind_param('i', $days);
    $stmt->execute();
    $result = $stmt->get_result();

    $trends = [];
    while ($row = $result->fetch_assoc()) {
        $trends[] = [
            'x' => $row['date'],
            'total' => (int)$row['total'],
            'ins' => (int)$row['ins'],
            'outs' => (int)$row['outs']
        ];
    }

    // Distribution data
    $distribution_query = "SELECT 
        movement_type as type,
        COUNT(*) as count
    FROM stockmovement
    GROUP BY movement_type";

    $result = $conn->query($distribution_query);
    $distribution = [];
    while ($row = $result->fetch_assoc()) {
        $distribution[] = [
            'type' => $row['type'],
            'count' => (int)$row['count']
        ];
    }

    echo json_encode([
        'status' => 'success',
        'trends' => $trends,
        'distribution' => $distribution
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
