<?php
require_once '../includes/config.php';
header('Content-Type: application/json');

try {
    // Monthly trends data
    $trends_query = "SELECT 
        DATE_FORMAT(dateencoded, '%Y-%m-%d') as date,
        COUNT(*) as total_movements,
        SUM(CASE WHEN movement_type = 'IN' THEN totalpacks ELSE 0 END) as stock_in,
        SUM(CASE WHEN movement_type = 'OUT' THEN totalpacks ELSE 0 END) as stock_out
    FROM stockmovement
    WHERE dateencoded >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
    GROUP BY DATE_FORMAT(dateencoded, '%Y-%m-%d')
    ORDER BY date ASC";

    $result = $conn->query($trends_query);
    $trends_data = [];
    
    while ($row = $result->fetch_assoc()) {
        $trends_data[] = [
            'x' => $row['date'],
            'in' => (int)$row['stock_in'],
            'out' => (int)$row['stock_out'],
            'total' => (int)$row['total_movements']
        ];
    }

    // Product distribution data
    $distribution_query = "SELECT 
        productname,
        COUNT(*) as movement_count,
        SUM(totalpacks) as total_packs
    FROM stockmovement
    GROUP BY productname
    ORDER BY movement_count DESC
    LIMIT 10";

    $result = $conn->query($distribution_query);
    $distribution_data = [];
    
    while ($row = $result->fetch_assoc()) {
        $distribution_data[] = [
            'product' => $row['productname'],
            'count' => (int)$row['movement_count'],
            'packs' => (int)$row['total_packs']
        ];
    }

    echo json_encode([
        'status' => 'success',
        'trends' => $trends_data,
        'distribution' => $distribution_data
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()