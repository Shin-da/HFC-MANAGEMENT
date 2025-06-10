<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';
require_once './models/analytics.php';
require_once './models/prescriptive-engine.php';
require_once './models/predictive-model.php';
require_once './models/AnalyticsManager.php';

// Debug mode
$debug = true;
error_reporting(E_ALL);
ini_set('display_errors', 1);

$current_page = basename($_SERVER['PHP_SELF'], '.php');
$_SESSION['current_page'] = $current_page;

// Add these functions at the top after the require statements
function calculateStandardDeviation($numbers)
{
  if (empty($numbers))
    return 0;
  $count = count($numbers);
  if ($count === 1)
    return 0;

  $mean = array_sum($numbers) / $count;
  $variance = array_sum(array_map(function ($x) use ($mean) {
    return pow($x - $mean, 2);
  }, $numbers)) / ($count - 1);

  return sqrt($variance);
}

function safeDiv($numerator, $denominator, $default = 0)
{
  return $denominator != 0 ? $numerator / $denominator : $default;
}

// Format sales values with peso sign
function formatCurrency($amount)
{
  return '₱' . number_format($amount, 2);
}

// Add this safe division helper function at the top with other helper functions
function safeDivision($numerator, $denominator)
{
  return $denominator != 0 ? $numerator / $denominator : 0;
}

// Add this helper function near the top with other helper functions
function initializeMetrics($data)
{
  return array_merge([
    'ordertotal' => 0,
    'avg_product_revenue' => 0,
    'order_frequency' => 0,
    'current_sales' => 0,
    'avg_sales' => 0,
    'performance_category' => 'Moderate Performer',
    'trend_direction' => 'Stable'
  ], $data);
}

// Pagination parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$offset = ($page - 1) * $limit;

// Filter parameters
$dateFrom = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$dateTo = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Modify the query_sales query
$query_sales = "
  SELECT 
    p.productcode, 
    p.productname, 
    COALESCE(SUM(ol.quantity * ol.unit_price), 0) AS current_sales,
    COALESCE(AVG(ol.unit_price), 0) AS avg_sales
  FROM products p
  LEFT JOIN orderlog ol ON p.productcode = ol.productcode
  LEFT JOIN customerorder co ON ol.orderid = co.orderid
";

// Build WHERE clause
$where_conditions = [];
if ($dateFrom) $where_conditions[] = "co.orderdate >= '$dateFrom'";
if ($dateTo) $where_conditions[] = "co.orderdate <= '$dateTo'";
if ($category) $where_conditions[] = "p.productcategory = '$category'";
if ($searchTerm) $where_conditions[] = "(p.productname LIKE '%$searchTerm%' OR p.productcode LIKE '%$searchTerm%')";

// Always add the completed status condition
$where_conditions[] = "co.status = 'Completed'";

// Combine WHERE conditions if they exist
if (!empty($where_conditions)) {
    $query_sales .= " WHERE " . implode(' AND ', $where_conditions);
}

// Add GROUP BY and LIMIT
$query_sales .= " GROUP BY p.productcode, p.productname LIMIT $offset, $limit";

// Get total records for pagination - modify to match main query structure
$total_records_query = "
    SELECT COUNT(DISTINCT p.productcode) as total 
    FROM products p 
    LEFT JOIN orderlog ol ON p.productcode = ol.productcode 
    LEFT JOIN customerorder co ON ol.orderid = co.orderid
";

if (!empty($where_conditions)) {
    $total_records_query .= " WHERE " . implode(' AND ', $where_conditions);
}

$total_records = $conn->query($total_records_query)->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);

$result_sales = $conn->query($query_sales);
$sales_data = [];
if ($result_sales && $result_sales->num_rows > 0) {
  while ($row = $result_sales->fetch_assoc()) {
    $sales_data[] = $row;
  }
}

// Function to generate recommendations
function generateProductRecommendation($metrics)
{
  $recommendation = [];

  // Ensure we have valid numbers to work with
  $current_sales = floatval($metrics['current_sales']);
  $avg_sales = floatval($metrics['avg_sales']);

  // Price optimization with safety check
  if ($avg_sales > 0 && $current_sales < $avg_sales) {
    $optimal_price = $avg_sales * 1.1;
    $recommendation[] = "Consider price optimization: Test price point around " . number_format($optimal_price, 2);
  }

  // Sales trend analysis
  $trend = $metrics['trend_direction'] ?? 'Stable';
  $performance = $metrics['performance_category'] ?? 'Moderate Performer';

  if ($trend === 'Declining') {
    if ($performance === 'High Performer') {
      $recommendation[] = "Priority Alert: High-value product showing decline. Implement retention strategy.";
    } else {
      $recommendation[] = "Sales declining. Consider promotional campaign or bundle offers.";
    }
  }

  // Inventory suggestions
  if ($metrics['order_frequency'] > 20) {
    $recommendation[] = "High-demand product: Maintain optimal inventory levels";
  } elseif ($metrics['order_frequency'] < 5) {
    $recommendation[] = "Low-demand product: Review inventory levels and consider marketing push";
  }

  // Performance-based recommendations
  switch ($performance) {
    case 'High Performer':
      $recommendation[] = "Key product: Ensure stock availability and consider premium positioning";
      break;
    case 'Moderate Performer':
      $recommendation[] = "Growth potential: Target upselling and cross-selling opportunities";
      break;
    case 'Needs Attention':
      $recommendation[] = "Underperforming: Review pricing strategy and marketing approach";
      break;
    default:
      $recommendation[] = "Monitor performance and adjust strategy accordingly";
  }

  return implode(" | ", $recommendation);
}
// Function to calculate the monthly growth rate
function calculateMonthlyGrowthRate($conn)
{
  // Query to get the sales of the last two months
  $query = "
        SELECT 
            DATE_FORMAT(orderdate, '%Y-%m') as month,
            SUM(ordertotal) as monthly_sales
        FROM customerorder
        WHERE status = 'Completed'
        GROUP BY month
        ORDER BY month DESC
        LIMIT 2
    ";
  $stmt = $conn->prepare($query);
  $stmt->execute();
  $result = $stmt->get_result();
  $sales = [];
  while ($row = $result->fetch_assoc()) {
    $sales[] = $row['monthly_sales'];
  }

  // Check if there are at least two months of data
  if (count($sales) < 2) {
    return 0; // Not enough data to calculate growth rate
  }

  $latest_month_sales = $sales[0];
  $previous_month_sales = $sales[1];

  // Avoid division by zero
  if ($previous_month_sales == 0) {
    return 0;
  }

  // Calculate the growth rate
  $growth_rate = (($latest_month_sales - $previous_month_sales) / $previous_month_sales) * 100;
  return round($growth_rate, 2);
}

function getCurrentMonthSales($conn)
{
  $current_month = date('m');
  $current_year = date('Y');
  $query = "
        SELECT 
            DATE_FORMAT(orderdate, '%Y-%m') as month,
            SUM(ordertotal) as monthly_sales
        FROM customerorder
        WHERE status = 'Completed' AND YEAR(orderdate) = '$current_year' AND MONTH(orderdate) = '$current_month'
        GROUP BY month
        ORDER BY month DESC
    ";
  $result = $conn->query($query);
  $sales_data = [];
  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $sales_data[] = $row;
    }
  } else {
    echo "No results found.";
  }
  return $sales_data;
}

// Fetch the monthly growth rate
$monthly_growth_rate = calculateMonthlyGrowthRate($conn);

// Fetch order and inventory data
$ordertotal_values = [];
$orderdate_values = [];
$total_sum = 0;
try {
  $sql = "SELECT * FROM customerorder WHERE status = 'Completed'";
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $ordertotal_values[] = $row["ordertotal"];
      $orderdate_values[] = $row["orderdate"];
      $total_sum += $row["ordertotal"];
    }
  } else {
    echo "No records matching your query were found.";
  }
} catch (Exception $e) {
  die("ERROR: Could not able to execute $sql." . $e->getMessage());
}

// Populate chart data when page is first loaded
$chart_labels_chart = array();
$chart_data_chart = array();
$sql_polar = "
    SELECT 
        p.productname, 
        SUM(ol.quantity * ol.unit_price) AS total_sales
    FROM orderlog ol
    JOIN products p ON ol.productcode = p.productcode
    JOIN customerorder co ON ol.orderid = co.orderid
    WHERE co.status = 'Completed'
    GROUP BY p.productname
    ORDER BY total_sales DESC
    LIMIT 5
";
$result_polar = $conn->query($sql_polar);
if ($result_polar->num_rows > 0) {
  while ($row = $result_polar->fetch_assoc()) {
    $chart_labels_chart[] = $row["productname"];
    $chart_data_chart[] = $row["total_sales"];
  }
}
// SQL query for polar area chart
if (isset($_GET['updateChartData'])) {
  $filtered_year = $_GET['year'];
  $filtered_month = $_GET['month'];
  $filtered_day = $_GET['day'];

  $sql_polar = "
        SELECT 
            p.productname, 
            SUM(ol.quantity * ol.unit_price) AS total_sales
        FROM orderlog ol
        JOIN products p ON ol.productcode = p.productcode
        JOIN customerorder co ON ol.orderid = co.orderid
        WHERE co.status = 'Completed'
        AND YEAR(co.orderdate) = ?
        AND MONTH(co.orderdate) = ?
        AND DAY(co.orderdate) = ?
        GROUP BY p.productname
        ORDER BY total_sales DESC
        LIMIT 10
    ";

  $stmt = $conn->prepare($sql_polar);
  $stmt->bind_param('sss', $filtered_year, $filtered_month, $filtered_day);
  $stmt->execute();
  $result_polar = $stmt->get_result();

  $chart_labels_chart = array();
  $chart_data_chart = array();
  if ($result_polar->num_rows > 0) {
    while ($row = $result_polar->fetch_assoc()) {
      $chart_labels_chart[] = $row["productname"];
      $chart_data_chart[] = $row["total_sales"];
    }
  }
  $data = array(
    'labels' => $chart_labels_chart,
    'data' => $chart_data_chart
  );
  echo json_encode($data);
  exit;
}
// Get unique years, months, and days from orderdate
$years = array_unique(array_map(fn($date) => date('Y', strtotime($date)), $orderdate_values));
$months = array_unique(array_map(fn($date) => date('m', strtotime($date)), $orderdate_values));
$days = array_unique(array_map(fn($date) => date('d', strtotime($date)), $orderdate_values));

// Add these analytical queries after existing queries
$advanced_analytics = [
  // Sales Forecasting with Trend Analysis
  'forecast' => $conn->query("
        SELECT 
            DATE_FORMAT(orderdate, '%Y-%m') as period,
            COUNT(*) as order_count,
            SUM(ordertotal) as actual_sales,
            AVG(ordertotal) as avg_order_value,
            STD(ordertotal) as sales_volatility,
            MAX(ordertotal) as peak_sale,
            MIN(ordertotal) as lowest_sale
        FROM customerorder
        WHERE status = 'Completed'
        GROUP BY DATE_FORMAT(orderdate, '%Y-%m')
        ORDER BY period DESC
        LIMIT 12
    ")->fetch_all(MYSQLI_ASSOC),

  // Product Performance Analysis
  'product_performance' => $conn->query("
        SELECT 
            orderdescription,
            COUNT(*) as order_frequency,
            SUM(ordertotal) as total_revenue,
            AVG(ordertotal) as avg_revenue,
            COUNT(*) / (
                SELECT COUNT(*) FROM customerorder WHERE status = 'Completed'
            ) * 100 as sales_percentage
        FROM customerorder
        WHERE status = 'Completed'
        GROUP BY orderdescription
        ORDER BY total_revenue DESC
        LIMIT 10
    ")->fetch_all(MYSQLI_ASSOC),

  // Customer Loyalty Analysis
  'customer_loyalty' => $conn->query("
        SELECT 
            customername,
            COUNT(*) as visit_frequency,
            SUM(ordertotal) as total_spent,
            AVG(ordertotal) as avg_transaction,
            MAX(orderdate) as last_visit,
            DATEDIFF(NOW(), MAX(orderdate)) as days_since_last_visit
        FROM customerorder
        WHERE status = 'Completed'
        GROUP BY customername
        HAVING COUNT(*) > 1
        ORDER BY total_spent DESC
        LIMIT 10
    ")->fetch_all(MYSQLI_ASSOC)
];

// Add new analytical queries for enhanced metrics
$enhanced_analytics = [
  // Sales Growth Rate with Moving Average
  'growth_metrics' => $conn->query("
        WITH MonthlyStats AS (
            SELECT 
                DATE_FORMAT(orderdate, '%Y-%m') as month,
                SUM(ordertotal) as monthly_sales,
                COUNT(DISTINCT orderid) as order_count,
                COUNT(DISTINCT customername) as customer_count
            FROM customerorder 
            WHERE status = 'Completed'
            GROUP BY DATE_FORMAT(orderdate, '%Y-%m')
            ORDER BY month DESC
            LIMIT 3
        ),
        GrowthCalc AS (
            SELECT 
                month,
                monthly_sales,
                LAG(monthly_sales) OVER (ORDER BY month) as prev_month_sales,
                AVG(monthly_sales) OVER (ORDER BY month ROWS BETWEEN 2 PRECEDING AND CURRENT ROW) as moving_avg
            FROM MonthlyStats
        )
        SELECT 
            ROUND(((monthly_sales - prev_month_sales) / prev_month_sales * 100), 2) as growth_rate,
            ROUND(((monthly_sales - moving_avg) / moving_avg * 100), 2) as trending_rate
        FROM GrowthCalc
        WHERE prev_month_sales IS NOT NULL
        LIMIT 1
    ")->fetch_assoc(),

  // Enhanced Product Performance Analysis
  'product_performance' => $conn->query("
        WITH ProductStats AS (
            SELECT 
                orderdescription,
                COUNT(*) as order_count,
                SUM(ordertotal) as total_revenue,
                AVG(ordertotal) as avg_revenue,
                STDDEV(ordertotal) as revenue_std,
                COUNT(DISTINCT customername) as unique_customers,
                MAX(orderdate) as last_order,
                MIN(orderdate) as first_order
            FROM customerorder
            WHERE status = 'Completed'
            GROUP BY orderdescription
        )
        SELECT 
            *,
            total_revenue / DATEDIFF(CURDATE(), first_order) as daily_revenue_rate,
            order_count / DATEDIFF(CURDATE(), first_order) * 30 as monthly_order_rate,
            total_revenue / unique_customers as revenue_per_customer
        FROM ProductStats
        ORDER BY total_revenue DESC
        LIMIT 10
    ")->fetch_all(MYSQLI_ASSOC),

  // Customer Segmentation and Value Analysis
  'customer_analysis' => $conn->query("
        WITH CustomerMetrics AS (
            SELECT 
                customername,
                COUNT(*) as visit_frequency,
                SUM(ordertotal) as total_spent,
                AVG(ordertotal) as avg_transaction,
                STDDEV(ordertotal) as spending_variance,
                DATEDIFF(MAX(orderdate), MIN(orderdate)) as customer_lifespan,
                DATEDIFF(CURDATE(), MAX(orderdate)) as days_since_last_order
            FROM customerorder
            WHERE status = 'Completed'
            GROUP BY customername
            HAVING COUNT(*) > 1
        )
        SELECT 
            CASE 
                WHEN total_spent > (SELECT AVG(total_spent) + STDDEV(total_spent) FROM CustomerMetrics) THEN 'High Value'
                WHEN total_spent > (SELECT AVG(total_spent) FROM CustomerMetrics) THEN 'Medium Value'
                ELSE 'Standard Value'
            END as customer_segment,
            COUNT(*) as segment_size,
            AVG(total_spent) as avg_segment_value,
            AVG(visit_frequency) as avg_visits,
            AVG(customer_lifespan) as avg_lifespan,
            SUM(total_spent) / SUM(customer_lifespan) as daily_revenue_rate
        FROM CustomerMetrics
        GROUP BY 
            CASE 
                WHEN total_spent > (SELECT AVG(total_spent) + STDDEV(total_spent) FROM CustomerMetrics) THEN 'High Value'
                WHEN total_spent > (SELECT AVG(total_spent) FROM CustomerMetrics) THEN 'Medium Value'
                ELSE 'Standard Value'
            END
    ")->fetch_all(MYSQLI_ASSOC),

  // Time-Based Performance Metrics
  'temporal_analysis' => $conn->query("
        WITH HourlyStats AS (
            SELECT 
                DATE_FORMAT(orderdate, '%H:00') as hour_of_day,
                COUNT(*) as order_count,
                COALESCE(SUM(ordertotal), 0) as total_sales,
                COALESCE(AVG(ordertotal), 0) as avg_order_value,
                COUNT(DISTINCT customername) as unique_customers
            FROM customerorder
            WHERE 
                status = 'Completed' AND
                orderdate >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY DATE_FORMAT(orderdate, '%H')
        )
        SELECT 
            hour_of_day,
            order_count,
            total_sales,
            avg_order_value,
            unique_customers,
            CASE 
                WHEN order_count > 0 THEN total_sales / order_count 
                ELSE 0 
            END as efficiency_score,
            CASE 
                WHEN unique_customers > 0 THEN order_count / unique_customers 
                ELSE 0 
            END as customer_frequency_ratio
        FROM HourlyStats
        ORDER BY total_sales DESC
    ")->fetch_all(MYSQLI_ASSOC)
];

// Add new KPIs to the dashboard
$kpis = [
  'revenue_metrics' => $conn->query("
        WITH DailyMetrics AS (
            SELECT 
                DATE(orderdate) as sale_date,
                SUM(ordertotal) as daily_total,
                COUNT(*) as daily_orders,
                COUNT(DISTINCT customername) as daily_customers
            FROM customerorder 
            WHERE 
                status = 'Completed' AND
                orderdate >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY DATE(orderdate)
        )
        SELECT 
            SUM(daily_total) as revenue_30_days,
            AVG(daily_total) as avg_daily_revenue,
            STDDEV(daily_total) as revenue_volatility,
            SUM(daily_orders) / 30 as avg_daily_orders,
            SUM(daily_customers) / COUNT(DISTINCT sale_date) as avg_daily_customers,
            MAX(daily_total) as best_day_revenue,
            MIN(daily_total) as worst_day_revenue
        FROM DailyMetrics
    ")->fetch_assoc(),

  'customer_metrics' => $conn->query("
        SELECT 
            COUNT(DISTINCT customername) as total_customers,
            SUM(ordertotal) / COUNT(DISTINCT customername) as revenue_per_customer,
            AVG(ordertotal) as avg_order_value,
            COUNT(*) / COUNT(DISTINCT customername) as orders_per_customer
        FROM customerorder 
        WHERE status = 'Completed'
    ")->fetch_assoc(),

  // Add trend analysis
  'trend_metrics' => $conn->query("
        WITH WeeklyTrends AS (
            SELECT 
                YEARWEEK(orderdate) as sale_week,
                SUM(ordertotal) as weekly_revenue,
                COUNT(*) as weekly_orders,
                COUNT(DISTINCT customername) as weekly_customers
            FROM customerorder
            WHERE 
                status = 'Completed' AND
                orderdate >= DATE_SUB(CURDATE(), INTERVAL 12 WEEK)
            GROUP BY YEARWEEK(orderdate)
            ORDER BY sale_week DESC
        )
        SELECT 
            AVG(weekly_revenue) as avg_weekly_revenue,
            STDDEV(weekly_revenue) as weekly_volatility,
            (
                SELECT (MAX(weekly_revenue) - MIN(weekly_revenue)) / MIN(weekly_revenue) * 100
                FROM (
                    SELECT weekly_revenue
                    FROM WeeklyTrends
                    ORDER BY sale_week DESC
                    LIMIT 4
                ) recent_weeks
            ) as recent_fluctuation_percentage
        FROM WeeklyTrends
    ")->fetch_assoc()
];

// Add after existing analytics queries
$product_insights = $conn->query("
    SELECT 
        p.productname,
        COUNT(DISTINCT co.orderid) as order_count,
        SUM(ol.quantity * ol.unit_price) as total_revenue,
        AVG(ol.unit_price) as avg_order_value,
        STDDEV(ol.unit_price) as price_volatility,
        COUNT(DISTINCT co.customername) as unique_customers,
        SUM(CASE 
            WHEN MONTH(co.orderdate) = MONTH(CURRENT_DATE) 
            THEN ol.quantity * ol.unit_price 
            ELSE 0 
        END) as current_month_sales,
        SUM(CASE 
            WHEN MONTH(co.orderdate) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) 
            THEN ol.quantity * ol.unit_price 
            ELSE 0 
        END) as last_month_sales,
        MAX(co.orderdate) as last_order_date
    FROM products p
    LEFT JOIN orderlog ol ON p.productcode = ol.productcode
    LEFT JOIN customerorder co ON ol.orderid = co.orderid
    WHERE co.status = 'Completed'
    GROUP BY p.productname
")->fetch_all(MYSQLI_ASSOC);

// Fetch data for recommendations table
$query = "SELECT * FROM customerorder";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
  $label = $row['orderdescription'];
  // retrieve other data here
}

// Replace the existing recommendations query with this enhanced version
$query_recommendations = "
    WITH ProductMetrics AS (
        SELECT 
            p.productname,
            co.orderdate,
            SUM(ol.quantity * ol.unit_price) as ordertotal,
            AVG(ol.unit_price) OVER (PARTITION BY p.productcode) as avg_product_revenue,
            COUNT(*) OVER (PARTITION BY p.productcode) as order_frequency,
            SUM(ol.quantity * ol.unit_price) OVER (PARTITION BY p.productcode) as total_product_revenue,
            DENSE_RANK() OVER (ORDER BY SUM(ol.quantity * ol.unit_price) DESC) as revenue_rank
        FROM products p
        JOIN orderlog ol ON p.productcode = ol.productcode
        JOIN customerorder co ON ol.orderid = co.orderid
        WHERE co.status = 'Completed'
        GROUP BY p.productname, co.orderdate, ol.orderid
    )
    SELECT 
        orderdate,
        productname,
        ordertotal,
        avg_product_revenue,
        order_frequency,
        total_product_revenue,
        revenue_rank,
        CASE
            WHEN revenue_rank <= 5 THEN 'High Performer'
            WHEN revenue_rank <= 15 THEN 'Moderate Performer'
            ELSE 'Needs Attention'
        END as performance_category,
        CASE
            WHEN ordertotal < LAG(ordertotal) OVER (PARTITION BY productname ORDER BY orderdate) 
            THEN 'Declining'
            WHEN ordertotal > LAG(ordertotal) OVER (PARTITION BY productname ORDER BY orderdate) 
            THEN 'Growing'
            ELSE 'Stable'
        END as trend_direction
    FROM ProductMetrics
    ORDER BY orderdate DESC
";

// Add this function to generate specific recommendations
function generateRecommendation($metrics)
{
  // Initialize metrics with default values
  $metrics = initializeMetrics($metrics);

  $recommendation = [];

  // Ensure we have valid numbers to work with
  $ordertotal = floatval($metrics['ordertotal']);
  $avg_product_revenue = floatval($metrics['avg_product_revenue']);
  $order_frequency = intval($metrics['order_frequency']);

  // Price optimization with safety check
  if ($avg_product_revenue > 0 && $ordertotal < $avg_product_revenue) {
    $optimal_price = $avg_product_revenue * 1.1;
    $recommendation[] = "Consider price optimization: Test price point around " . number_format($optimal_price, 2);
  }

  // Sales trend analysis
  $trend = $metrics['trend_direction'] ?? 'Stable';
  $performance = $metrics['performance_category'] ?? 'Moderate Performer';

  if ($trend === 'Declining') {
    if ($performance === 'High Performer') {
      $recommendation[] = "Priority Alert: High-value product showing decline. Implement retention strategy.";
    } else {
      $recommendation[] = "Sales declining. Consider promotional campaign or bundle offers.";
    }
  }

  // Inventory suggestions
  if ($order_frequency > 20) {
    $recommendation[] = "High-demand product: Maintain optimal inventory levels";
  } elseif ($order_frequency < 5) {
    $recommendation[] = "Low-demand product: Review inventory levels and consider marketing push";
  }

  // Performance-based recommendations
  switch ($performance) {
    case 'High Performer':
      $recommendation[] = "Key product: Ensure stock availability and consider premium positioning";
      break;
    case 'Moderate Performer':
      $recommendation[] = "Growth potential: Target upselling and cross-selling opportunities";
      break;
    case 'Needs Attention':
      $recommendation[] = "Underperforming: Review pricing strategy and marketing approach";
      break;
    default:
      $recommendation[] = "Monitor performance and adjust strategy accordingly";
  }

  return implode(" | ", $recommendation);
}

// Add a function to calculate more accurate sales predictions
function calculateSalesPrediction($historical_data, $current_value)
{
  if (empty($historical_data)) {
    return [
      'predicted_value' => $current_value,
      'confidence_score' => 0,
      'trend_indicator' => 'stable',
      'volatility' => 0
    ];
  }

  $count = count($historical_data);
  $weights = array_map(function ($i) use ($count) {
    return ($i + 1) / (($count * ($count + 1)) / 2);
  }, range(0, $count - 1));

  $weighted_sum = 0;
  foreach ($historical_data as $index => $value) {
    $weighted_sum += floatval($value) * $weights[$index];
  }

  $trend_factor = ($current_value > 0) ? ($current_value / $weighted_sum) : 1;
  $prediction = $weighted_sum * $trend_factor;

  // Calculate volatility using our custom standard deviation function
  $volatility = ($weighted_sum > 0) ?
    calculateStandardDeviation($historical_data) / $weighted_sum :
    0;

  return [
    'predicted_value' => round($prediction, 2),
    'confidence_score' => round(min(100, (1 - abs($prediction - $current_value) / max(1, $current_value)) * 100), 2),
    'trend_indicator' => $trend_factor > 1 ? 'upward' : 'downward',
    'volatility' => round($volatility * 100, 2)
  ];
}

$current_month_sales_data = getCurrentMonthSales($conn);

// Fetch the previous month's sales
$previous_month = date('Y-m', strtotime('-1 month'));
$previous_month_query = "SELECT SUM(ordertotal) as previous_month_value
                         FROM customerorder
                         WHERE status = 'Completed' AND DATE_FORMAT(orderdate, '%Y-%m') = '$previous_month'";
$previous_month_result = $conn->query($previous_month_query);
$previous_month_value = $previous_month_result->fetch_assoc()['previous_month_value'];

// Update template includes to use absolute paths
$header_path = dirname(__DIR__) . '/templates/header.php';
$sidebar_path = dirname(__DIR__) . '/includes/sidebar.php';
$navbar_path = dirname(__DIR__) . '/includes/navbar.php';
$footer_path = dirname(__DIR__) . '/templates/footer.php';

// Add new food-specific analytics queries
$food_analytics = [
  'product_performance' => $conn->query("
        SELECT 
            p.productname,
            p.productcategory,
            COUNT(DISTINCT co.orderid) as order_count,
            SUM(ol.quantity) as total_quantity_sold,
            SUM(ol.quantity * ol.unit_price) as total_revenue,
            AVG(ol.quantity) as avg_order_size,
            STDDEV(ol.quantity) as demand_volatility,
            COUNT(DISTINCT co.customername) as unique_customers,
            SUM(ol.quantity) / COUNT(DISTINCT co.orderid) as items_per_order
        FROM products p
        LEFT JOIN orderlog ol ON p.productcode = ol.productcode
        LEFT JOIN customerorder co ON ol.orderid = co.orderid
        WHERE co.status = 'Completed'
        GROUP BY p.productname, p.productcategory
    ")->fetch_all(MYSQLI_ASSOC),

  'category_trends' => $conn->query("
        SELECT 
            p.productcategory,
            COUNT(*) as total_orders,
            SUM(ol.quantity) as total_units,
            AVG(ol.unit_price) as avg_price,
            SUM(ol.quantity * ol.unit_price) / COUNT(DISTINCT co.orderid) as revenue_per_order
        FROM products p
        JOIN orderlog ol ON p.productcode = ol.productcode
        JOIN customerorder co ON ol.orderid = co.orderid
        WHERE co.status = 'Completed'
        GROUP BY p.productcategory
    ")->fetch_all(MYSQLI_ASSOC)
];

// Add seasonal analysis
$seasonal_trends = $conn->query("
    SELECT 
        MONTH(orderdate) as month,
        COUNT(*) as order_count,
        SUM(ordertotal) as total_sales,
        AVG(ordertotal) as avg_order_value
    FROM customerorder
    WHERE status = 'Completed'
    GROUP BY MONTH(orderdate)
    ORDER BY month
")->fetch_all(MYSQLI_ASSOC);

// Initialize Predictive Model
$predictive_model = new PredictiveModel($conn);
$prescriptive_engine = new PrescriptiveEngine($conn);

// Get all products
$products_query = "SELECT productcode, productname FROM products";
$products_result = $conn->query($products_query);
$products = [];
$sales_predictions = [];

// Generate predictions for each product
if ($products_result->num_rows > 0) {
  while ($product = $products_result->fetch_assoc()) {
    $products[$product['productcode']] = $product;
    $sales_predictions[$product['productcode']] = $predictive_model->forecastSales($product['productcode']);
  }
}

// Generate strategic recommendations
$strategic_recommendations = $prescriptive_engine->generateRecommendations();

// Add error handling
if (empty($sales_predictions)) {
  $sales_predictions = [];
  error_log("Warning: No sales predictions generated");
}

if (empty($strategic_recommendations)) {
  $strategic_recommendations = [];
  error_log("Warning: No strategic recommendations generated");
}

// Configure page - Move this before any HTML output
Page::setTitle('Sales Analytics | Supervisor');
Page::setBodyClass('supervisor-body');
Page::set('current_page', 'sales');

// Add sales master CSS
Page::addStyle('/assets/css/sales-master.css');

// Add any other non-sales CSS if needed

// Add theme toggle scripts
Page::addScript('https://code.jquery.com/jquery-3.7.0.min.js');
Page::addScript('../assets/js/theme-toggle.js', ['defer' => true]);

// Add scripts with defer attribute
Page::addScript('https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js');
Page::addScript('../assets/js/theme-toggle.js', ['defer' => true]);

// Add this after loading Chart.js
Page::addScript('../assets/js/sales-dashboard.js');

// Add critical CSS inline
?>
<style>
  /* Critical CSS for initial render */
  .dashboard-wrapper {
    opacity: 0;
    transition: opacity 0.3s;
  }
  .dashboard-wrapper.loaded {
    opacity: 1;
  }
  .charts-grid, .recommendations-section {
    min-height: 200px;
  }
  /* Add loading indicators */
  .loading {
    position: relative;
  }
  .loading::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255,255,255,0.8);
  }
  .loading::after {
    content: 'Loading...';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
  }
</style>

<script>
  // Add this at the top of the page
  document.addEventListener('DOMContentLoaded', () => {
    // Show loading state
    document.querySelectorAll('.chart-container').forEach(container => {
      container.classList.add('loading');
    });
    
    // Remove loading class after charts are initialized
    window.addEventListener('load', () => {
      document.querySelector('.dashboard-wrapper').classList.add('loaded');
      document.querySelectorAll('.loading').forEach(el => {
        el.classList.remove('loading');
      });
    });
  });
</script>

<?php
// Start output buffering
ob_start();
?>

<div class="sales-dashboard-wrapper sales-theme">

    <!-- Header -->
    <header class="dashboard-header sales-page-header">
        <h1>Sales Overview</h1>
        <p>Today's Date: <?= date('F d, Y') ?></p>
    </header>

    <!-- Stats Grid -->
    <section class="dashboard-stats sales-stats-grid">
        <div class="stat-card">
            <div class="stat-value"><?= number_format(array_sum($ordertotal_values)) ?></div>
            <div class="stat-label">Total Revenue</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?= count($ordertotal_values) ?></div>
            <div class="stat-label">Total Orders</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">
                <?php
                if (empty($ordertotal_values)) {
                    echo '0.00';
                } else {
                    echo number_format(safeDivision(array_sum($ordertotal_values), count($ordertotal_values)), 2);
                }
                ?>
            </div>
            <div class="stat-label">Average Order Value</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?= count($chart_labels_chart) ?></div>
            <div class="stat-label">Active Products</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">
                <?php
                $completion_query = "SELECT 
                        ROUND((COUNT(CASE WHEN status = 'Completed' THEN 1 END) * 100.0) / COUNT(*), 1) as completion_rate 
                        FROM customerorder";
                $completion_result = $conn->query($completion_query);
                $completion_rate = $completion_result->fetch_assoc()['completion_rate'];
                echo $completion_rate . '%';
                ?>
            </div>
            <div class="stat-label">Order Completion Rate</div>
        </div>
        <div class="stat-card highlight">
            <div class="stat-value">
                <?php
                $monthly_query = "SELECT 
                                    DATE_FORMAT(orderdate, '%Y-%m') as month,
                                    SUM(ordertotal) as monthly_sales
                                FROM customerorder
                                WHERE status = 'Completed'
                                GROUP BY month
                                ORDER BY month DESC
                                LIMIT 2";
                $monthly_result = $conn->query($monthly_query);
                $monthly_sales = [];
                while ($row = $monthly_result->fetch_assoc()) {
                    $monthly_sales[] = $row['monthly_sales'];
                }

                if (count($monthly_sales) < 2) {
                    echo '0%';
                } else {
                    $latest_month_sales = $monthly_sales[0];
                    $previous_month_sales = $monthly_sales[1];

                    if ($previous_month_sales == 0) {
                        echo '0%';
                    } else {
                        $growth_rate = (($latest_month_sales - $previous_month_sales) / $previous_month_sales) * 100;
                        echo number_format($growth_rate, 1) . '%';
                    }
                }
                ?>
            </div>
            <div class="stat-label">Monthly Growth Rate</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">
                <?php
                $current_month_query = "SELECT SUM(ordertotal) as current_month_value
                                                FROM customerorder
                                                WHERE status = 'Completed' AND MONTH(orderdate) = MONTH(CURRENT_DATE()) AND YEAR(orderdate) = YEAR(CURRENT_DATE())";
                $current_month_result = $conn->query($current_month_query);
                $current_month_value = $current_month_result->fetch_assoc()['current_month_value'] ?? 0;
                echo number_format((float)$current_month_value, 2);
                ?>
            </div>
            <div class="stat-label">Current Month Value</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">
                <?php
                $previous_month_query = "SELECT SUM(ordertotal) as previous_month_value
                                                 FROM customerorder
                                                 WHERE status = 'Completed' AND MONTH(orderdate) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH) AND YEAR(orderdate) = YEAR(CURRENT_DATE())";
                $previous_month_result = $conn->query($previous_month_query);
                $previous_month_value = $previous_month_result->fetch_assoc()['previous_month_value'] ?? 0;
                echo number_format((float)$previous_month_value, 2);
                ?>
            </div>
            <div class="stat-label">Previous Month Value</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">
                <?php
                if ($previous_month_value == 0) {
                    echo '0%';
                } else {
                    $monthly_growth_rate = (($current_month_value - $previous_month_value) / $previous_month_value) * 100;
                    echo number_format($monthly_growth_rate, 1) . '%';
                }
                ?>
            </div>
            <div class="stat-label">Monthly Growth Rate (Based on Current Month Value)</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?= number_format((float)($kpis['revenue_metrics']['revenue_30_days'] ?? 0)) ?></div>
            <div class="stat-label">30-Day Revenue</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?= number_format((float)($kpis['customer_metrics']['revenue_per_customer'] ?? 0), 2) ?></div>
            <div class="stat-label">Revenue Per Customer</div>
        </div>
    </section>

    <!-- Filters Section -->
    <div class="filters-section">
        <form class="filter-controls">
            <label for="year">Year:</label>
            <select id="year" name="year">
                <option value="">All Years</option>
                <?php foreach ($years as $year): ?>
                    <option value="<?= $year ?>" <?= isset($_GET['year']) && $_GET['year'] == $year ? 'selected' : '' ?>>
                        <?= $year ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="month">Month:</label>
            <select id="month" name="month">
                <option value="">All Months</option>
                <?php foreach ($months as $month): ?>
                    <option value="<?= $month ?>" <?= isset($_GET['month']) && $_GET['month'] == $month ? 'selected' : '' ?>>
                        <?= date('F', mktime(0, 0, 0, $month, 1)) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="day">Day:</label>
            <select id="day" name="day">
                <option value="">All Days</< /option>
                    <?php foreach ($days as $day): ?>
                <option value="<?= $day ?>" <?= isset($_GET['day']) && $_GET['day'] == $day ? 'selected' : '' ?>>
                    <?= $day ?>
                </option>
            <?php endforeach; ?>
            </select>

            <button type="submit"
                onclick="updateChartData(document.getElementById('year').value, document.getElementById('month').value, document.getElementById('day').value)">
                Apply Filter
            </button>
        </form>
    </div>

    <!-- Charts Section -->
    <div class="charts-grid">
        <div class="chart-card">
            <h3>Sales Trends</h3>
            <div class="chart-container">
                <canvas id="myChart"></canvas>
            </div>
        </div>
        <div class="chart-card">
            <h3>Product Distribution</h3>
            <div class="chart-container">
                <canvas id="polarAreaChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Category and Seasonal Charts -->
    <div class="charts-grid">
        <div class="chart-card">
            <h3>Category Performance</h3>
            <div class="chart-container">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
        <div class="chart-card">
            <h3>Seasonal Trends</h3>
            <div class="chart-container">
                <canvas id="seasonalChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recommendations Section -->
    <div class="recommendations-section">
        <h2>Product Analysis & Recommendations</h2>
        <div class="table-controls">
            <input type="text" id="searchInput" placeholder="Search products...">
            <button class="export-btn" onclick="exportTableToCSV('recommendations.csv')">
                <i class="bx bx-download"></i> Export
            </button>
        </div>
        <div class="table-responsive">
            <table class="recommendations-table">
                <thead>
                    <tr>
                        <th>Order Date</th>
                        <th>Product Ordered</th>
                        <th>Status</th>
                        <th>Sales</th>
                        <th>Recommendation</th>
                        <th>Expected Monthly Sales</th>
                        <th>Expected Annual Sales</th>
                        <th>Trend</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query_recommendations = "
                                SELECT orderdate, GROUP_CONCAT(orderdescription SEPARATOR ', ') as products, SUM(ordertotal) as total_sales
                                FROM customerorder
                                WHERE status = 'Completed'
                                GROUP BY orderdate
                                ORDER BY orderdate DESC
                            ";
                    $result_recommendations = mysqli_query($conn, $query_recommendations);

                    // Replace the existing recommendations table rendering with the updated formula for expected monthly sales
                    while ($row_recommendations = mysqli_fetch_assoc($result_recommendations)) {
                        $orderdate = $row_recommendations['orderdate'];
                        $products = explode(', ', $row_recommendations['products']);
                        $total_sales = $row_recommendations['total_sales'];

                        $year = date('Y', strtotime($orderdate));
                        $month = date('m', strtotime($orderdate));

                        $query_total_sales = "SELECT SUM(ordertotal) AS total_sales FROM customerorder WHERE YEAR(orderdate) = '$year' AND MONTH(orderdate) = '$month'";
                        $result_total_sales = mysqli_query($conn, $query_total_sales);
                        $row_total_sales = mysqli_fetch_assoc($result_total_sales);

                        $date = new DateTime($orderdate);
                        $daysPassed = $date->format('j'); // Day of the month
                        $daysInMonth = $date->format('t'); // Total days in the month
                        $remainingDays = $daysInMonth - $daysPassed;

                        $totalSales = $row_total_sales['total_sales'];
                        $expectedMonthlySales = ($totalSales / $daysPassed) * $daysInMonth;

                        $query_expected_annual_sales = "SELECT SUM(ordertotal) AS total_sales FROM customerorder WHERE YEAR(orderdate) = '$year'";
                        $result_expected_annual_sales = mysqli_query($conn, $query_expected_annual_sales);
                        $row_expected_annual_sales = mysqli_fetch_assoc($result_expected_annual_sales);

                        $endOfYear = $date->format('Y-12-31'); // Last day of the year
                        $remainingDaysInYear = (new DateTime($endOfYear))->diff($date)->days + 1; // Include today

                        $averageDailySalesAnnual = $row_expected_annual_sales['total_sales'] / date('z', strtotime($orderdate)) + 1;
                        $expectedAnnualSale = $averageDailySalesAnnual * $remainingDaysInYear;

                        $recommendation = "";
                        if ($expectedMonthlySales <= $total_sales) {
                            $recommendation = "Offer targeted promotions to boost sales.";
                        } else {
                            $recommendation = "Maintain current strategy; monitor for continued success.";
                        }
                        echo "<tr class='recommendation-row' data-orderdate='$orderdate' data-products='" . json_encode($products) . "' data-totalsales='$total_sales'>";
                        echo "<td>$orderdate</td>";
                        echo "<td>" . implode(', ', $products) . "</td>";
                        echo "<td>Completed</td>";
                        echo "<td>" . formatCurrency($total_sales) . "</td>";
                        echo "<td>$recommendation</td>";
                        echo "<td>" . formatCurrency($expectedMonthlySales) . "</td>";
                        echo "<td>" . formatCurrency($expectedAnnualSale) . "</td>";
                        echo "<td>";
                        $trend = $expectedMonthlySales > $total_sales ?
                            '<span class="trend-indicator trend-up">↑ Growing</span>' :
                            '<span class="trend-indicator trend-down">↓ Declining</span>';
                        echo $trend;
                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="recommendations-section">
        <div style="display: flex; justify-content: center; align-items: center;">
            <h2 class="header2">Advanced Product Analysis and Strategic Recommendations</h2>
        </div>
        <div class="table-controls">
            <input type="text" id="searchInput" placeholder="Search products..." onkeyup="filterTable()">
            <button onclick="exportTableToCSV('recommendations.csv')">Export to CSV</button>
        </div>
        <table class="recommendations-table" id="recommendationsTable">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Current Performance</th>
                    <th>Trend</th>
                    <th>Sales Impact</th>
                    <th>Strategic Recommendations</th>
                    <th>Priority Level</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($sales_data as $data) {
                    $metrics = initializeMetrics([
                        'current_sales' => $data['current_sales'] ?? 0,
                        'avg_sales' => $data['avg_sales'] ?? 0,
                        'order_frequency' => $data['order_frequency'] ?? 0,
                        'performance_category' => $data['performance_category'] ?? 'Moderate Performer',
                        'trend_direction' => $data['trend_direction'] ?? 'Stable',
                        'ordertotal' => $data['current_sales'] ?? 0, // Use current_sales as ordertotal
                        'avg_product_revenue' => $data['avg_sales'] ?? 0 // Use avg_sales as avg_product_revenue
                    ]);

                    $recommendation = generateRecommendation($metrics);
                    $priority_class = '';
                    $priority_level = '';

                    // Determine priority level with null checks
                    if ($metrics['performance_category'] === 'High Performer' && $metrics['trend_direction'] === 'Declining') {
                        $priority_class = 'high-priority';
                        $priority_level = 'High';
                    } elseif ($metrics['performance_category'] === 'Needs Attention') {
                        $priority_class = 'medium-priority';
                        $priority_level = 'Medium';
                    } else {
                        $priority_class = 'low-priority';
                        $priority_level = 'Low';
                    }

                    // Calculate percentage change with safety check
                    $percentage_change = 0;
                    if ($metrics['avg_sales'] > 0) {
                        $percentage_change = (($metrics['current_sales'] - $metrics['avg_sales']) / $metrics['avg_sales']) * 100;
                    }

                    echo "<tr class='$priority_class'>";
                    echo "<td>{$data['productname']}</td>";
                    echo "<td>{$metrics['performance_category']}</td>";
                    echo "<td class='trend-{$metrics['trend_direction']}'>{$metrics['trend_direction']}</td>";
                    echo "<td>" . number_format($metrics['current_sales'], 2) . " (" .
                        ($percentage_change >= 0 ? '+' : '') .
                        number_format($percentage_change, 1) .
                        "%)</td>";
                    echo "<td class='recommendations-cell'>$recommendation</td>";
                    echo "<td class='priority-$priority_level'>$priority_level</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Add new sections in the dashboard-container div -->
    <div class="analytics-section">
        <h2>Food Product Analytics</h2>

        <!-- Category Performance -->
        <div class="chart-card">
            <h3>Category Performance</h3>
            <canvas id="foodCategoryChart"></canvas>  <!-- Changed ID -->
            <div class="category-metrics">
                <?php foreach ($food_analytics['category_trends'] as $productcategory): ?>
                    <div class="metric-card">
                        <h4><?= htmlspecialchars($productcategory['productcategory']) ?></h4>
                        <p>Revenue per Order: <?= formatCurrency($productcategory['revenue_per_order']) ?></p>
                        <p>Total Units: <?= number_format($productcategory['total_units']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Seasonal Trends -->
        <div class="chart-card">
            <h3>Seasonal Sales Patterns</h3>
            <canvas id="foodSeasonalChart"></canvas>  <!-- Changed ID -->
            <div class="seasonal-insights">
                <?php
                $peak_month = array_reduce($seasonal_trends, function ($carry, $item) {
                    return ($item['total_sales'] > ($carry['total_sales'] ?? 0)) ? $item : $carry;
                });
                ?>
                <p>Peak Sales Month: <?= date('F', mktime(0, 0, 0, $peak_month['month'], 1)) ?></p>
            </div>
        </div>
    </div>

    <!-- Predictive Analytics Section -->
    <div class="analytics-section">
        <h2>Predictive Sales Analysis</h2>
        <div class="predictions-grid">
            <?php 
            if (!empty($sales_predictions)): 
                foreach ($sales_predictions as $product_code => $prediction): 
                    // Skip if product doesn't exist
                    if (!isset($products[$product_code])) continue;
                    
                    $product_name = htmlspecialchars($products[$product_code]['productname'] ?? 'Unknown Product');
                    
                    // Skip if no predictions available
                    if (empty($prediction)) continue;
                    
                    $last_prediction = end($prediction);
                    if (!$last_prediction) continue;

                    $trend = $last_prediction['trend_indicator'] ?? 'stable';
                    
                    // Safe calculation of confidence with null checks
                    $confidence = 0;
                    if (isset($last_prediction['predicted_sales']) && 
                        $last_prediction['predicted_sales'] > 0 && 
                        isset($last_prediction['confidence_interval']['upper']) && 
                        isset($last_prediction['confidence_interval']['lower'])) {
                        $confidence = (($last_prediction['confidence_interval']['upper'] - 
                                      $last_prediction['confidence_interval']['lower']) / 
                                      $last_prediction['predicted_sales']) * 100;
                    }
            ?>
                <div class="prediction-card">
                    <h3><?= $product_name ?></h3>
                    <div class="forecast-chart-container">
                        <canvas id="forecast_<?= htmlspecialchars($product_code) ?>"></canvas>
                    </div>
                    <div class="forecast-metrics">
                        <div class="metric">
                            <label>30-Day Forecast:</label>
                            <span class="value">
                                <?= isset($last_prediction['predicted_sales']) ? 
                                    formatCurrency($last_prediction['predicted_sales']) : 
                                    'N/A' ?>
                            </span>
                        </div>
                        <div class="metric">
                            <label>Confidence Range:</label>
                            <span class="value">
                                <?php if (isset($last_prediction['confidence_interval'])): ?>
                                    <?= formatCurrency($last_prediction['confidence_interval']['lower'] ?? 0) ?> - 
                                    <?= formatCurrency($last_prediction['confidence_interval']['upper'] ?? 0) ?>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="metric">
                            <label>Trend:</label>
                            <span class="trend-indicator <?= htmlspecialchars($trend) ?>">
                                <?= ucfirst(str_replace('_', ' ', $trend)) ?>
                            </span>
                        </div>
                        <div class="metric">
                            <label>Seasonality Impact:</label>
                            <span class="value">
                                <?php
                                if (isset($last_prediction['seasonality_factor'])) {
                                    echo round(($last_prediction['seasonality_factor'] - 1) * 100, 1) . '%';
                                } else {
                                    echo 'N/A';
                                }
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php 
                endforeach;
            else:
            ?>
                <div class="no-predictions-message">
                    <p>No sales predictions available at this time.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Prescriptive Recommendations -->
    <div class="analytics-section">
        <h2>Strategic Recommendations</h2>
        <div class="recommendations-grid">
            <?php foreach ($strategic_recommendations as $recommendation): ?>
                <div class="recommendation-card priority-<?= strtolower($recommendation['priority']) ?>">
                    <h3><?= htmlspecialchars($recommendation['product']) ?></h3>
                    <div class="recommendation-content">
                        <p class="action"><?= htmlspecialchars($recommendation['action']) ?></p>
                        <p class="reason"><?= htmlspecialchars($recommendation['reason']) ?></p>
                        <?php if (isset($recommendation['quantity'])): ?>
                            <p class="quantity">Suggested Quantity: <?= $recommendation['quantity'] ?></p>
                        <?php endif; ?>
                        <?php if (isset($recommendation['suggested_price'])): ?>
                            <p class="price">Suggested Price: <?= formatCurrency($recommendation['suggested_price']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script src="../resources/js/sales.js"></script>
<script>
    // Initialize charts with PHP data
    const chartData = <?= json_encode($filtered_ordertotal_values ?? $ordertotal_values) ?>;
    const chartDates = <?= json_encode($filtered_orderdate_values ?? $orderdate_values) ?>;
    const chartLabels = <?= json_encode($chart_labels_chart) ?>;
    const chartValues = <?= json_encode($chart_data_chart) ?>;
    const chartLabel = <?= json_encode($chartLabel ?? "All Orders") ?>;

    const polarChart = initializeCharts(chartData, chartDates, chartLabels, chartValues);

    const currentMonthSalesData = <?= json_encode($current_month_sales_data) ?>;
    const previousMonthValue = <?= json_encode($previous_month_value) ?>;

    function filterTable() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toLowerCase();
        const table = document.getElementById('recommendationsTable');
        const tr = table.getElementsByTagName('tr');

        for (let i = 1; i < tr.length; i++) {
            const td = tr[i].getElementsByTagName('td')[0];
            if (td) {
                const txtValue = td.textContent || td.innerText;
                tr[i].style.display = txtValue.toLowerCase().indexOf(filter) > -1 ? '' : 'none';
            }
        }
    }

    function exportTableToCSV(filename) {
        const csv = [];
        const rows = document.querySelectorAll('table tr');

        for (const row of rows) {
            const cols = row.querySelectorAll('td, th');
            const rowData = [];
            for (const col of cols) {
                rowData.push(col.innerText);
            }
            csv.push(rowData.join(','));
        }

        const csvFile = new Blob([csv.join('\n')], {
            type: 'text/csv'
        });
        const downloadLink = document.createElement('a');
        downloadLink.download = filename;
        downloadLink.href = window.URL.createObjectURL(csvFile);
        downloadLink.style.display = 'none';
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
    }

    // Add new chart initializations
    function initializeFoodAnalytics() {
        const categoryCtx = document.getElementById('foodCategoryChart').getContext('2d');
        const seasonalCtx = document.getElementById('foodSeasonalChart').getContext('2d');

        // Category performance chart
        new Chart(categoryCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($food_analytics['category_trends'], 'productcategory')) ?>,
                datasets: [{
                    label: 'Revenue per Order',
                    data: <?= json_encode(array_column($food_analytics['category_trends'], 'revenue_per_order')) ?>,
                    backgroundColor: 'rgba(52, 152, 219, 0.5)'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Category Performance Analysis'
                    }
                }
            }
        });

        // Seasonal trends chart
        new Chart(seasonalCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode(array_map(function ($month) {
                    return date('F', mktime(0, 0, 0, $month['month'], 1));
                }, $seasonal_trends)) ?>,
                datasets: [{
                    label: 'Monthly Sales',
                    data: <?= json_encode(array_column($seasonal_trends, 'total_sales')) ?>,
                    fill: true,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Seasonal Sales Trends'
                    }
                }
            }
        });
    }

    // Initialize all charts
    document.addEventListener('DOMContentLoaded', function() {
        initializeCharts(chartData, chartDates, chartLabels, chartValues);
        initializeFoodAnalytics();
    });
</script>
<script>
    // Data initialization
    const chartData = <?= json_encode($ordertotal_values) ?>;
    const chartDates = <?= json_encode($orderdate_values) ?>;
    const chartLabels = <?= json_encode($chart_labels_chart) ?>;
    const chartValues = <?= json_encode($chart_data_chart) ?>;

    // Category and seasonal data
    window.categoryData = {
        labels: <?= json_encode(array_column($food_analytics['category_trends'], 'productcategory')) ?>,
        datasets: [{
            label: 'Revenue per Category',
            data: <?= json_encode(array_column($food_analytics['category_trends'], 'revenue_per_order')) ?>,
            backgroundColor: 'rgba(75, 192, 192, 0.5)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        }]
    };

    window.seasonalData = {
        labels: <?= json_encode(array_map(function ($month) {
            return date('F', mktime(0, 0, 0, $month['month'], 1));
        }, $seasonal_trends)) ?>,
        datasets: [{
            label: 'Monthly Sales',
            data: <?= json_encode(array_column($seasonal_trends, 'total_sales')) ?>,
            fill: true,
            borderColor: 'rgb(75, 192, 192)',
            tension: 0.1
        }]
    };
</script>

<!-- Replace all existing script tags with this single section before closing body tag -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Single source of chart data
    const chartData = {
        salesData: <?= json_encode($ordertotal_values) ?>,
        dates: <?= json_encode($orderdate_values) ?>,
        productLabels: <?= json_encode($chart_labels_chart) ?>,
        productValues: <?= json_encode($chart_data_chart) ?>,
        categoryData: {
            labels: <?= json_encode(array_column($food_analytics['category_trends'], 'productcategory')) ?>,
            datasets: [{
                label: 'Revenue per Category',
                data: <?= json_encode(array_column($food_analytics['category_trends'], 'revenue_per_order')) ?>,
                backgroundColor: 'rgba(75, 192, 192, 0.5)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        seasonalData: {
            labels: <?= json_encode(array_map(function ($month) {
                return date('F', mktime(0, 0, 0, $month['month'], 1));
            }, $seasonal_trends)) ?>,
            datasets: [{
                label: 'Monthly Sales',
                data: <?= json_encode(array_column($seasonal_trends, 'total_sales')) ?>,
                fill: true,
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        }
    };

    // Make data available globally
    window.categoryData = chartData.categoryData;
    window.seasonalData = chartData.seasonalData;

    // Initialize all charts
    initializeCharts(
        chartData.salesData,
        chartData.dates,
        chartData.productLabels,
        chartData.productValues
    );
});

// Table filter function
function filterTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const table = document.getElementById('recommendationsTable');
    const tr = table.getElementsByTagName('tr');

    for (let i = 1; i < tr.length; i++) {
        const td = tr[i].getElementsByTagName('td')[0];
        if (td) {
            const txtValue = td.textContent || td.innerText;
            tr[i].style.display = txtValue.toLowerCase().indexOf(filter) > -1 ? '' : 'none';
        }
    }
}

// Export table function
function exportTableToCSV(filename) {
    const csv = [];
    const rows = document.querySelectorAll('table tr');

    for (const row of rows) {
        const cols = row.querySelectorAll('td, th');
        const rowData = [];
        for (const col of cols) {
            rowData.push(col.innerText);
        }
        csv.push(rowData.join(','));
    }

    const csvFile = new Blob([csv.join('\n')], { type: 'text/csv' });
    const downloadLink = document.createElement('a');
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = 'none';
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}
</script>

<!-- Add required script files -->
<script src="../assets/js/sales-charts.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../assets/js/forecast-charts.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (!empty($sales_predictions)): ?>
        <?php foreach ($sales_predictions as $product_code => $prediction): ?>
            <?php if (!empty($prediction) && isset($products[$product_code])): ?>
            try {
                initializeForecastChart(
                    'forecast_<?= htmlspecialchars($product_code) ?>', 
                    <?= json_encode($prediction) ?>
                );
            } catch (error) {
                console.error('Error initializing forecast chart for product ' + 
                    '<?= htmlspecialchars($product_code) ?>:', error);
            }
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
});
</script>

<?php

// Check if it's an API request
if (isset($_GET['action'])) {
    require_once './api/sales-api.php';
    exit;
}

// Otherwise, load the view
require_once './views/sales-view.php';

$content = ob_get_clean();
Page::render($content);
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Prepare chart data
    const chartData = {
        salesData: <?= json_encode($ordertotal_values) ?>,
        dates: <?= json_encode($orderdate_values) ?>,
        productLabels: <?= json_encode($chart_labels_chart) ?>,
        productValues: <?= json_encode($chart_data_chart) ?>,
        categoryData: {
            labels: <?= json_encode(array_column($food_analytics['category_trends'], 'productcategory')) ?>,
            datasets: [{
                label: 'Revenue per Category',
                data: <?= json_encode(array_column($food_analytics['category_trends'], 'revenue_per_order')) ?>,
                backgroundColor: 'rgba(75, 192, 192, 0.5)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        seasonalData: {
            labels: <?= json_encode(array_map(function ($month) {
                return date('F', mktime(0, 0, 0, $month['month'], 1));
            }, $seasonal_trends)) ?>,
            datasets: [{
                label: 'Monthly Sales',
                data: <?= json_encode(array_column($seasonal_trends, 'total_sales')) ?>,
                fill: true,
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        }
    };

    // Make data available globally
    window.categoryData = chartData.categoryData;
    window.seasonalData = chartData.seasonalData;

    // Initialize all charts
    initializeCharts(
        chartData.salesData,
        chartData.dates,
        chartData.productLabels,
        chartData.productValues
    );
});

// Table filter function
function filterTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const table = document.getElementById('recommendationsTable');
    const tr = table.getElementsByTagName('tr');

    for (let i = 1; i < tr.length; i++) {
        const td = tr[i].getElementsByTagName('td')[0];
        if (td) {
            const txtValue = td.textContent || td.innerText;
            tr[i].style.display = txtValue.toLowerCase().indexOf(filter) > -1 ? '' : 'none';
        }
    }
}

// Export table function
function exportTableToCSV(filename) {
    const csv = [];
    const rows = document.querySelectorAll('table tr');

    for (const row of rows) {
        const cols = row.querySelectorAll('td, th');
        const rowData = [];
        for (const col of cols) {
            rowData.push(col.innerText);
        }
        csv.push(rowData.join(','));
    }

    const csvFile = new Blob([csv.join('\n')], { type: 'text/csv' });
    const downloadLink = document.createElement('a');
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = 'none';
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}
</script>

<!-- Add required libraries in correct order -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Debug Information -->
<div id="debug-container" style="margin: 20px; padding: 15px; border: 1px solid #f00; background-color: #fff; display: block;">
    <h3>Debug Information</h3>
    <pre id="debug-output" style="overflow: auto; max-height: 200px; background: #f5f5f5; padding: 10px;">
PHP Variables:
<?php 
// Debug check for required variables
$debug_vars = [
    'ordertotal_values' => isset($ordertotal_values) ? count($ordertotal_values) . ' items' : 'NOT SET',
    'orderdate_values' => isset($orderdate_values) ? count($orderdate_values) . ' items' : 'NOT SET',
    'chart_labels_chart' => isset($chart_labels_chart) ? count($chart_labels_chart) . ' items' : 'NOT SET',
    'chart_data_chart' => isset($chart_data_chart) ? count($chart_data_chart) . ' items' : 'NOT SET',
    'food_analytics' => isset($food_analytics) ? 'Set with ' . (isset($food_analytics['category_trends']) ? count($food_analytics['category_trends']) : '0') . ' category trends' : 'NOT SET',
    'seasonal_trends' => isset($seasonal_trends) ? count($seasonal_trends) . ' items' : 'NOT SET',
    'sales_predictions' => isset($sales_predictions) ? 'Set' : 'NOT SET'
];

echo "Debug Variables:\n";
foreach ($debug_vars as $key => $value) {
    echo "$key: $value\n";
}

// Sample of actual data (limit to avoid too much output)
if (isset($ordertotal_values) && count($ordertotal_values) > 0) {
    echo "\nSample ordertotal_values: " . json_encode(array_slice($ordertotal_values, 0, 3)) . "\n";
}
if (isset($orderdate_values) && count($orderdate_values) > 0) {
    echo "Sample orderdate_values: " . json_encode(array_slice($orderdate_values, 0, 3)) . "\n";
}
?>
    </pre>
</div>

<script>
console.log('Initializing dashboardData...');
// Single source of chart data
window.dashboardData = {
    salesData: <?= json_encode($ordertotal_values) ?>,
    dates: <?= json_encode($orderdate_values) ?>,
    productLabels: <?= json_encode($chart_labels_chart) ?>,
    productValues: <?= json_encode($chart_data_chart) ?>,
    categoryData: {
        labels: <?= json_encode(array_column($food_analytics['category_trends'], 'productcategory')) ?>,
        datasets: [{
            label: 'Revenue per Category',
            data: <?= json_encode(array_column($food_analytics['category_trends'], 'revenue_per_order')) ?>,
            backgroundColor: 'rgba(75, 192, 192, 0.5)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        }]
    },
    seasonalData: {
        labels: <?= json_encode(array_map(function ($month) {
            return date('F', mktime(0, 0, 0, $month['month'], 1));
        }, $seasonal_trends)) ?>,
        datasets: [{
            label: 'Monthly Sales',
            data: <?= json_encode(array_column($seasonal_trends, 'total_sales')) ?>,
            fill: true,
            borderColor: 'rgb(75, 192, 192)',
            tension: 0.1
        }]
    }
};
console.log('dashboardData initialized:', window.dashboardData);

// Make predictions data available globally
window.salesPredictions = <?= json_encode($sales_predictions) ?>;
</script>
<script src="../assets/js/sales-dashboard.js"></script>
<!-- Right before closing </body> tag -->
<script>
// Format data for food analytics charts
const foodAnalyticsData = {
    categories: {
        labels: <?= json_encode(array_column($food_analytics['category_trends'], 'productcategory')) ?>,
        data: <?= json_encode(array_column($food_analytics['category_trends'], 'revenue_per_order')) ?>
    },
    seasonal: {
        labels: <?= json_encode(array_map(function($month) {
            return date('F', mktime(0, 0, 0, $month['month'], 1));
        }, $seasonal_trends)) ?>,
        data: <?= json_encode(array_column($seasonal_trends, 'total_sales')) ?>
    }
};

// Initialize food analytics charts
document.addEventListener('DOMContentLoaded', function() {
    const foodCategoryCtx = document.getElementById('foodCategoryChart');
    const foodSeasonalCtx = document.getElementById('foodSeasonalChart');
    
    console.log('Food analytics contexts:', { foodCategoryCtx, foodSeasonalCtx });
    console.log('Food analytics data:', foodAnalyticsData);

    if (foodCategoryCtx) {
        new Chart(foodCategoryCtx, {
            type: 'bar',
            data: {
                labels: foodAnalyticsData.categories.labels,
                datasets: [{
                    label: 'Revenue per Category',
                    data: foodAnalyticsData.categories.data,
                    backgroundColor: 'rgba(52, 152, 219, 0.7)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    if (foodSeasonalCtx) {
        new Chart(foodSeasonalCtx, {
            type: 'line',
            data: {
                labels: foodAnalyticsData.seasonal.labels,
                datasets: [{
                    label: 'Monthly Sales',
                    data: foodAnalyticsData.seasonal.data,
                    fill: true,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }
});
</script>
<!-- Before the closing </body> tag -->
<script>
// Debug logging for predictions data
console.log('Sales predictions data:', <?= json_encode($sales_predictions) ?>);

// Update additional chart data
// dashboardData was already defined earlier in the file
console.log('Using existing dashboardData:', window.dashboardData);

// Make predictions data available globally
window.salesPredictions = <?= json_encode($sales_predictions) ?>;

// Test chart to verify Chart.js is working
document.addEventListener('DOMContentLoaded', function() {
    try {
        console.log('Creating test chart...');
        const testCanvas = document.createElement('canvas');
        testCanvas.id = 'test-chart';
        testCanvas.width = 400;
        testCanvas.height = 200;
        document.getElementById('debug-container').appendChild(testCanvas);
        
        const ctx = testCanvas.getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
                datasets: [{
                    label: 'Test Data',
                    data: [12, 19, 3, 5, 2, 3],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        console.log('Test chart created successfully');
    } catch (error) {
        console.error('Error creating test chart:', error);
    }
});
</script>
</body>
