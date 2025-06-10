<?php
header('Content-Type: application/json');
require_once '../../includes/config.php';

try {
    $period = $_GET['period'] ?? 'all';
    $dateRange = [
        'start' => $_GET['start_date'] ?? null,
        'end' => $_GET['end_date'] ?? null
    ];

    // Query to fetch sales data based on period
    $query = "SELECT 
        DATE(orderdate) as date,
        COUNT(*) as order_count,
        SUM(ordertotal) as total_sales,
        AVG(ordertotal) as avg_order_value
    FROM customerorder 
    WHERE status = 'Completed'";

    // Add date filters if provided
    if ($dateRange['start'] && $dateRange['end']) {
        $query .= " AND orderdate BETWEEN ? AND ?";
        $params = [$dateRange['start'], $dateRange['end']];
    } else {
        switch($period) {
            case 'week':
                $query .= " AND orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)";
                break;
            case 'month':
                $query .= " AND orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)";
                break;
            case 'year':
                $query .= " AND YEAR(orderdate) = YEAR(CURRENT_DATE)";
                break;
        }
    }

    $query .= " GROUP BY DATE(orderdate) ORDER BY date DESC";
    
    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param('ss', ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $response = [
        'status' => 'success',
        'data' => $result->fetch_all(MYSQLI_ASSOC),
        'metrics' => [
            'total_sales' => 0,
            'total_orders' => 0,
            'avg_order_value' => 0
        ]
    ];

    // Calculate metrics
    if (!empty($response['data'])) {
        $response['metrics'] = [
            'total_sales' => array_sum(array_column($response['data'], 'total_sales')),
            'total_orders' => array_sum(array_column($response['data'], 'order_count')),
            'avg_order_value' => array_sum(array_column($response['data'], 'total_sales')) / array_sum(array_column($response['data'], 'order_count'))
        ];
    }

    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
