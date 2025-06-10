<?php
header('Content-Type: application/json');
require_once '../../includes/config.php';

try {
    $action = $_GET['action'] ?? 'sales';
    $period = $_GET['period'] ?? 'all';
    $dateRange = [
        'start' => $_GET['start_date'] ?? null,
        'end' => $_GET['end_date'] ?? null
    ];

    switch($action) {
        case 'sales':
            getSalesData($conn, $period, $dateRange);
            break;
        case 'inventory':
            getInventoryData($conn);
            break;
        case 'analytics':
            getSalesAnalytics($conn, $period, $dateRange);
            break;
        case 'recommendations':
            getRecommendations($conn);
            break;
        default:
            throw new Exception('Invalid action specified');
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

function getSalesData($conn, $period, $dateRange) {
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
}

function getInventoryData($conn) {
    // Get inventory status
    $query = "SELECT 
        productname,
        availablequantity,
        onhandquantity,
        productcategory,
        reorderlevel,
        (availablequantity <= reorderlevel) as needs_restock
    FROM inventory
    ORDER BY needs_restock DESC, availablequantity ASC";
    
    $result = $conn->query($query);
    
    $response = [
        'status' => 'success',
        'data' => $result->fetch_all(MYSQLI_ASSOC),
        'summary' => [
            'total_items' => 0,
            'low_stock_items' => 0,
            'out_of_stock_items' => 0
        ]
    ];
    
    // Calculate summary metrics
    if (!empty($response['data'])) {
        $response['summary'] = [
            'total_items' => count($response['data']),
            'low_stock_items' => count(array_filter($response['data'], function($item) {
                return $item['availablequantity'] <= $item['reorderlevel'] && $item['availablequantity'] > 0;
            })),
            'out_of_stock_items' => count(array_filter($response['data'], function($item) {
                return $item['availablequantity'] <= 0;
            }))
        ];
    }
    
    echo json_encode($response);
}

function getSalesAnalytics($conn, $period, $dateRange) {
    // Get category distribution
    $categoryQuery = "SELECT 
        i.productcategory,
        COUNT(ol.orderid) as order_count,
        SUM(ol.quantity * ol.productprice) as total_sales
    FROM inventory i
    JOIN orderlog ol ON i.productcode = ol.productcode
    WHERE ol.orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
    GROUP BY i.productcategory
    ORDER BY total_sales DESC";
    
    $categoryResult = $conn->query($categoryQuery);
    $categoryData = $categoryResult->fetch_all(MYSQLI_ASSOC);
    
    // Get top selling products
    $productQuery = "SELECT 
        i.productname,
        SUM(ol.quantity) as total_quantity,
        SUM(ol.quantity * ol.productprice) as total_sales
    FROM inventory i
    JOIN orderlog ol ON i.productcode = ol.productcode
    WHERE ol.orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
    GROUP BY i.productname
    ORDER BY total_quantity DESC
    LIMIT 10";
    
    $productResult = $conn->query($productQuery);
    $productData = $productResult->fetch_all(MYSQLI_ASSOC);
    
    // Get monthly trends
    $monthlyQuery = "SELECT 
        MONTH(orderdate) as month,
        YEAR(orderdate) as year,
        SUM(ordertotal) as total_sales
    FROM customerorder
    WHERE status = 'Completed'
    AND orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 12 MONTH)
    GROUP BY YEAR(orderdate), MONTH(orderdate)
    ORDER BY year, month";
    
    $monthlyResult = $conn->query($monthlyQuery);
    $monthlyData = $monthlyResult->fetch_all(MYSQLI_ASSOC);
    
    $response = [
        'status' => 'success',
        'category_trends' => $categoryData,
        'top_products' => $productData,
        'monthly_trends' => $monthlyData
    ];
    
    echo json_encode($response);
}

function getRecommendations($conn) {
    // Get sales growth data
    $growthQuery = "SELECT 
        MONTH(curr.orderdate) as month,
        YEAR(curr.orderdate) as year,
        SUM(curr.ordertotal) as current_sales,
        prev.prev_sales
    FROM customerorder curr
    LEFT JOIN (
        SELECT 
            MONTH(orderdate) as month,
            YEAR(orderdate) as year,
            SUM(ordertotal) as prev_sales
        FROM customerorder
        WHERE status = 'Completed'
        AND orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 24 MONTH)
        AND orderdate < DATE_SUB(CURRENT_DATE, INTERVAL 12 MONTH)
        GROUP BY YEAR(orderdate), MONTH(orderdate)
    ) prev ON MONTH(curr.orderdate) = prev.month
    WHERE curr.status = 'Completed'
    AND curr.orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 12 MONTH)
    GROUP BY YEAR(curr.orderdate), MONTH(curr.orderdate)
    ORDER BY year, month";
    
    $growthResult = $conn->query($growthQuery);
    $growthData = $growthResult->fetch_all(MYSQLI_ASSOC);
    
    // Calculate growth rates and detect seasonal patterns
    $growthRates = [];
    $seasonalityDetected = false;
    $peakSalesMonths = [];
    
    if (!empty($growthData)) {
        foreach ($growthData as $data) {
            if ($data['prev_sales'] > 0) {
                $growthRate = (($data['current_sales'] - $data['prev_sales']) / $data['prev_sales']) * 100;
                $growthRates[] = [
                    'month' => $data['month'],
                    'year' => $data['year'],
                    'growth_rate' => $growthRate
                ];
                
                // Identify peak sales months (25% higher than average)
                if ($data['current_sales'] > (array_sum(array_column($growthData, 'current_sales')) / count($growthData)) * 1.25) {
                    $peakSalesMonths[] = $data['month'];
                    $seasonalityDetected = true;
                }
            }
        }
    }
    
    // Generate actionable recommendations
    $recommendations = [];
    
    // 1. Stock recommendations
    $recommendations[] = [
        'type' => 'inventory',
        'title' => 'Inventory Management',
        'action' => 'Increase stock levels for peak months: ' . implode(', ', array_map(function($m) {
            return date('F', mktime(0, 0, 0, $m, 1));
        }, array_unique($peakSalesMonths)))
    ];
    
    // 2. Growth strategy
    $avgGrowthRate = !empty($growthRates) ? array_sum(array_column($growthRates, 'growth_rate')) / count($growthRates) : 0;
    $growthAction = $avgGrowthRate > 10 ? 'Capitalize on growth with expanded offerings' : 
                   ($avgGrowthRate < 0 ? 'Review pricing and marketing strategies' : 'Maintain current strategy with targeted promotions');
    
    $recommendations[] = [
        'type' => 'growth',
        'title' => 'Growth Strategy',
        'action' => $growthAction,
        'metrics' => [
            'avg_growth_rate' => round($avgGrowthRate, 2) . '%'
        ]
    ];
    
    // Get underperforming categories
    $underperformingQuery = "SELECT 
        i.productcategory,
        COUNT(ol.orderid) as order_count,
        SUM(ol.quantity * ol.productprice) as total_sales
    FROM inventory i
    LEFT JOIN orderlog ol ON i.productcode = ol.productcode
    WHERE ol.orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
    GROUP BY i.productcategory
    ORDER BY total_sales ASC
    LIMIT 3";
    
    $underperformingResult = $conn->query($underperformingQuery);
    $underperformingData = $underperformingResult->fetch_all(MYSQLI_ASSOC);
    
    if (!empty($underperformingData)) {
        $recommendations[] = [
            'type' => 'product_mix',
            'title' => 'Product Mix Optimization',
            'action' => 'Consider revamping or promoting these underperforming categories',
            'categories' => array_column($underperformingData, 'productcategory')
        ];
    }
    
    $response = [
        'status' => 'success',
        'recommendations' => $recommendations,
        'growth_data' => $growthRates,
        'seasonality' => [
            'detected' => $seasonalityDetected,
            'peak_months' => array_unique($peakSalesMonths)
        ]
    ];
    
    echo json_encode($response);
}
