
<?php
require '../includes/session.php';
require '../includes/config.php';

header('Content-Type: application/json');

$start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
$end_date = $_GET['end_date'] ?? date('Y-m-d');

try {
    // Get sales summary
    $salesQuery = "
        SELECT 
            ordertype,
            COUNT(*) as order_count,
            SUM(ordertotal) as total_revenue,
            AVG(ordertotal) as avg_order_value,
            COUNT(DISTINCT customername) as unique_customers
        FROM customerorder 
        WHERE orderdate BETWEEN ? AND ?
        GROUP BY ordertype
    ";
    
    $stmt = $conn->prepare($salesQuery);
    $stmt->bind_param('ss', $start_date, $end_date);
    $stmt->execute();
    $salesResult = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Get daily trends
    $trendsQuery = "
        SELECT 
            DATE(orderdate) as date,
            COUNT(*) as orders,
            SUM(ordertotal) as revenue
        FROM customerorder 
        WHERE orderdate BETWEEN ? AND ?
        GROUP BY DATE(orderdate)
        ORDER BY date
    ";
    
    $stmt = $conn->prepare($trendsQuery);
    $stmt->bind_param('ss', $start_date, $end_date);
    $stmt->execute();
    $trendsResult = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => [
            'sales_summary' => $salesResult,
            'daily_trends' => $trendsResult
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}