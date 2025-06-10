<?php
require_once '../includes/config.php';

$days = isset($_GET['days']) ? intval($_GET['days']) : 30;

try {
    // Get all dates in range for consistent data points
    $dates_query = "
        SELECT DATE(date_range.date) as date
        FROM (
            SELECT DATE_SUB(CURRENT_DATE, INTERVAL ? DAY) + INTERVAL sequence.seq DAY as date
            FROM (
                SELECT 0 seq UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION
                SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION
                SELECT 10 UNION SELECT 11 UNION SELECT 12 UNION SELECT 13 UNION SELECT 14 UNION

    // Query for OUT movements
    $out_query = "SELECT 
        DATE(dateencoded) as date,
        SUM(totalpieces) as pieces,
        COUNT(*) as count
    FROM stockmovement 
    WHERE movement_type = 'OUT'
    AND dateencoded >= DATE_SUB(CURRENT_DATE, INTERVAL ? DAY)
    GROUP BY DATE(dateencoded)";

    $stmt_in = $conn->prepare($in_query);
    $stmt_out = $conn->prepare($out_query);

    $stmt_in->bind_param('i', $days);
    $stmt_out->bind_param('i', $days);

    $stmt_in->execute();
    $result_in = $stmt_in->get_result();

    $stmt_out->execute();
    $result_out = $stmt_out->get_result();

    $trends_data = [
        'stock_in' => [],
        'stock_out' => []
    ];

    while ($row = $result_in->fetch_assoc()) {
        $trends_data['stock_in'][] = [
            'x' => $row['date'],
            'y' => intval($row['pieces'])
        ];
    }

    while ($row = $result_out->fetch_assoc()) {
        $trends_data['stock_out'][] = [
            'x' => $row['date'],
            'y' => intval($row['pieces'])
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($trends_data);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
