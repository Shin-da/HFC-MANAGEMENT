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
                SELECT 15 UNION SELECT 16 UNION SELECT 17 UNION SELECT 18 UNION SELECT 19 UNION
                SELECT 20 UNION SELECT 21 UNION SELECT 22 UNION SELECT 23 UNION SELECT 24 UNION
                SELECT 25 UNION SELECT 26 UNION SELECT 27 UNION SELECT 28 UNION SELECT 29
            ) sequence
            WHERE DATE_SUB(CURRENT_DATE, INTERVAL ? DAY) + INTERVAL sequence.seq DAY <= CURRENT_DATE
        ) date_range";

    $stmt_dates = $conn->prepare($dates_query);
    $stmt_dates->bind_param('ii', $days, $days);
    $stmt_dates->execute();
    $result_dates = $stmt_dates->get_result();

    $dates = [];
    while ($row = $result_dates->fetch_assoc()) {
        $dates[$row['date']] = [
            'date' => $row['date'],
            'in' => 0,
            'out' => 0
        ];
    }

    // Get movement data
    $movements_query = "
        SELECT 
            DATE(dateencoded) as date,
            movement_type,
            SUM(totalpieces) as total_pieces,
            COUNT(*) as count
        FROM stockmovement
        WHERE dateencoded >= DATE_SUB(CURRENT_DATE, INTERVAL ? DAY)
        GROUP BY DATE(dateencoded), movement_type
        ORDER BY date";

    $stmt_movements = $conn->prepare($movements_query);
    $stmt_movements->bind_param('i', $days);
    $stmt_movements->execute();
    $result_movements = $stmt_movements->get_result();

    while ($row = $result_movements->fetch_assoc()) {
        if (isset($dates[$row['date']])) {
            if ($row['movement_type'] == 'IN') {
                $dates[$row['date']]['in'] = intval($row['total_pieces']);
            } else {
                $dates[$row['date']]['out'] = intval($row['total_pieces']);
            }
        }
    }

    // Format data for chart
    $trends_data = [
        'stock_in' => [],
        'stock_out' => []
    ];

    foreach ($dates as $date_data) {
        $trends_data['stock_in'][] = [
            'x' => $date_data['date'],
            'y' => $date_data['in']
        ];
        $trends_data['stock_out'][] = [
            'x' => $date_data['date'],
            'y' => $date_data['out']
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($trends_data);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
