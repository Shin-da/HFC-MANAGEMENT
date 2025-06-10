
<?php
require_once '../../../includes/config.php';
require_once '../../../includes/session.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized access']));
}

try {
    $period = $_GET['period'] ?? 'monthly';
    
    $inventoryMetrics = [
        'summary' => getInventorySummary($conn),
        'trends' => getInventoryTrends($conn, $period),
        'categories' => getCategoryDistribution($conn),
        'alerts' => getInventoryAlerts($conn)
    ];
    
    echo json_encode($inventoryMetrics);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function getInventorySummary($conn) {
    $query = "SELECT 
        COUNT(*) as total_items,
        SUM(CASE WHEN stock_level <= reorder_point THEN 1 ELSE 0 END) as low_stock,
        SUM(stock_level * unit_price) as total_value
    FROM products";
    
    $result = $conn->query($query);
    return $result->fetch_assoc();
}

function getInventoryTrends($conn, $period) {
    // Implement the logic to fetch inventory trends based on the period
}

function getCategoryDistribution($conn) {
    // Implement the logic to fetch category distribution
}

function getInventoryAlerts($conn) {
    // Implement the logic to fetch inventory alerts
}
?>