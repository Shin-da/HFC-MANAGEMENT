<?php
require_once '../../../includes/config.php';
require_once '../../../includes/session.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized access']));
}

$days = $_GET['days'] ?? 30;
$category = $_GET['category'] ?? '';

try {
    $response = [
        'topProducts' => getTopProducts($conn, $days, $category),
        'categoryPerformance' => getCategoryPerformance($conn, $days),
        'stockLevels' => getStockLevels($conn),
        'trends' => getProductTrends($conn, $days, $category)
    ];
    
    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function getTopProducts($conn, $days, $category) {
    $query = "SELECT 
        p.product_id,
        p.product_name,
        COUNT(o.order_id) as order_count,
        SUM(o.quantity) as units_sold,
        SUM(o.quantity * o.unit_price) as revenue
    FROM products p
    JOIN order_items o ON p.product_id = o.product_id
    WHERE o.order_date >= DATE_SUB(CURRENT_DATE, INTERVAL ? DAY)
    " . ($category ? "AND p.category = ?" : "") . "
    GROUP BY p.product_id
    ORDER BY revenue DESC
    LIMIT 10";

    $stmt = $conn->prepare($query);
    if ($category) {
        $stmt->bind_param('is', $days, $category);
    } else {
        $stmt->bind_param('i', $days);
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
