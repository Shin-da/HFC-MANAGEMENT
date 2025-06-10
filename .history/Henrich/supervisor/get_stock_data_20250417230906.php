<?php
require_once '../includes/session.php';
require_once '../includes/config.php';

// Ensure user is logged in and has appropriate permissions
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'supervisor') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

try {
    // Get basic statistics
    $stats_query = "SELECT 
        COUNT(*) as total_products,
        SUM(CASE WHEN availablequantity <= 10 AND availablequantity > 0 THEN 1 ELSE 0 END) as low_stock,
        SUM(CASE WHEN availablequantity = 0 THEN 1 ELSE 0 END) as out_of_stock,
        SUM(availablequantity * unit_price) as total_value
    FROM inventory";
    
    $stats_result = $conn->query($stats_query);
    $stats = $stats_result->fetch_assoc();
    
    // Get category data
    $category_query = "SELECT 
        productcategory, 
        COUNT(*) as count,
        SUM(availablequantity) as total_quantity,
        SUM(availablequantity * unit_price) as total_value
    FROM inventory
    GROUP BY productcategory
    ORDER BY total_value DESC";
    
    $category_result = $conn->query($category_query);
    $categories = [];
    while ($row = $category_result->fetch_assoc()) {
        $categories[] = $row;
    }
    
    // Get trends data (last 30 days)
    $trends_query = "SELECT 
        DATE(dateupdated) as date,
        SUM(availablequantity * unit_price) as total_value
    FROM inventory
    WHERE dateupdated >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
    GROUP BY DATE(dateupdated)
    ORDER BY date ASC";
    
    $trends_result = $conn->query($trends_query);
    $trends_data = ['dates' => [], 'values' => []];
    while ($row = $trends_result->fetch_assoc()) {
        $trends_data['dates'][] = $row['date'];
        $trends_data['values'][] = (float)$row['total_value'];
    }
    
    // Get alerts data
    $alerts_query = "SELECT 
        productcode,
        productname,
        availablequantity,
        onhandquantity,
        unit_price,
        dateupdated,
        CASE 
            WHEN availablequantity = 0 THEN 'out_of_stock'
            WHEN availablequantity <= 10 THEN 'low_stock'
            ELSE 'normal'
        END as alert_type
    FROM inventory 
    WHERE availablequantity <= 10
    ORDER BY availablequantity ASC, dateupdated DESC
    LIMIT 10";
    
    $alerts_result = $conn->query($alerts_query);
    $alerts = [];
    while ($row = $alerts_result->fetch_assoc()) {
        $alerts[] = $row;
    }
    
    // Compile all data
    $response = [
        'stats' => $stats,
        'categories' => $categories,
        'trends' => $trends_data,
        'alerts' => $alerts,
        'last_updated' => date('Y-m-d H:i:s')
    ];
    
    // Set appropriate headers
    header('Content-Type: application/json');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
    
    echo json_encode($response);
    
} catch (Exception $e) {
    error_log("Error fetching stock data: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch stock data']);
} 