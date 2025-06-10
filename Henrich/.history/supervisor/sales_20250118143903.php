<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Add these helper functions at the top after the require statements
function calculateStandardDeviation($numbers)
{
  if (empty($numbers)) return 0;
  $count = count($numbers);
  if ($count === 1) return 0;

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
$sql_polar = "SELECT pl.productname, SUM(co.ordertotal) AS total_sales
              FROM customerorder co
              JOIN productlist pl ON co.orderdescription LIKE CONCAT('%', pl.productname, '%')
              WHERE co.status = 'Completed'
              GROUP BY pl.productname
              ORDER BY total_sales DESC
              LIMIT 5";
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
  $sql_polar = "SELECT pl.productname, SUM(oh.ordertotal) AS total_sales
                FROM customerorder oh
                JOIN productlist pl ON oh.orderdescription LIKE CONCAT('%', pl.productname, '%')
                WHERE oh.status = 'Completed' 
                AND YEAR(oh.orderdate) = '$filtered_year' 
                AND MONTH(oh.orderdate) = '$filtered_month' 
                AND DAY(oh.orderdate) = '$filtered_day'
                GROUP BY pl.productname
                ORDER BY total_sales DESC
                LIMIT 10";
  $result_polar = $conn->query($sql_polar);
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
            customerid,
            COUNT(*) as visit_frequency,
            SUM(ordertotal) as total_spent,
            AVG(ordertotal) as avg_transaction,
            MAX(orderdate) as last_visit,
            DATEDIFF(NOW(), MAX(orderdate)) as days_since_last_visit
        FROM customerorder
        WHERE status = 'Completed'
        GROUP BY customerid
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
                COUNT(DISTINCT customerid) as customer_count
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
                COUNT(DISTINCT customerid) as unique_customers,
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
                customerid,
                COUNT(*) as visit_frequency,
                SUM(ordertotal) as total_spent,
                AVG(ordertotal) as avg_transaction,
                STDDEV(ordertotal) as spending_variance,
                DATEDIFF(MAX(orderdate), MIN(orderdate)) as customer_lifespan,
                DATEDIFF(CURDATE(), MAX(orderdate)) as days_since_last_order
            FROM customerorder
            WHERE status = 'Completed'
            GROUP BY customerid
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
                COUNT(DISTINCT customerid) as unique_customers
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
                COUNT(DISTINCT customerid) as daily_customers
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
            COUNT(DISTINCT customerid) as total_customers,
            SUM(ordertotal) / COUNT(DISTINCT customerid) as revenue_per_customer,
            AVG(ordertotal) as avg_order_value,
            COUNT(*) / COUNT(DISTINCT customerid) as orders_per_customer
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
                COUNT(DISTINCT customerid) as weekly_customers
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
        pl.productname,
        COUNT(co.orderid) as order_count,
        SUM(co.ordertotal) as total_revenue,
        AVG(co.ordertotal) as avg_order_value,
        STDDEV(co.ordertotal) as price_volatility,
        COUNT(DISTINCT co.customerid) as unique_customers,
        SUM(CASE WHEN MONTH(co.orderdate) = MONTH(CURRENT_DATE) THEN co.ordertotal ELSE 0 END) as current_month_sales,
        SUM(CASE WHEN MONTH(co.orderdate) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) THEN co.ordertotal ELSE 0 END) as last_month_sales,
        MAX(co.orderdate) as last_order_date
    FROM customerorder co
    JOIN productlist pl ON co.orderdescription LIKE CONCAT('%', pl.productname, '%')
    WHERE co.status = 'Completed'
    GROUP BY pl.productname
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
            orderdescription,
            orderdate,
            ordertotal,
            AVG(ordertotal) OVER (PARTITION BY orderdescription) as avg_product_revenue,
            COUNT(*) OVER (PARTITION BY orderdescription) as order_frequency,
            SUM(ordertotal) OVER (PARTITION BY orderdescription) as total_product_revenue,
            DENSE_RANK() OVER (ORDER BY ordertotal DESC) as revenue_rank,
            LAG(ordertotal) OVER (PARTITION BY orderdescription ORDER BY orderdate) as previous_sale,
            CASE
                WHEN DENSE_RANK() OVER (ORDER BY ordertotal DESC) <= 5 THEN 'High Performer'
                WHEN DENSE_RANK() OVER (ORDER BY ordertotal DESC) <= 15 THEN 'Moderate Performer'
                ELSE 'Needs Attention'
            END as performance_category,
            CASE
                WHEN ordertotal < LAG(ordertotal) OVER (PARTITION BY orderdescription ORDER BY orderdate) THEN 'Declining'
                WHEN ordertotal > LAG(ordertotal) OVER (PARTITION BY orderdescription ORDER BY orderdate) THEN 'Growing'
                ELSE 'Stable'
            END as trend_direction
        FROM customerorder
        WHERE status = 'Completed'
    )
    SELECT 
        orderdate,
        orderdescription,
        ordertotal,
        COALESCE(avg_product_revenue, 0) as avg_product_revenue,
        COALESCE(order_frequency, 0) as order_frequency,
        COALESCE(total_product_revenue, 0) as total_product_revenue,
        revenue_rank,
        COALESCE(previous_sale, ordertotal) as previous_sale,
        performance_category,
        COALESCE(trend_direction, 'Stable') as trend_direction
    FROM ProductMetrics
    ORDER BY orderdate DESC
";

// Add this function to generate specific recommendations
function generateRecommendation($metrics)
{
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

?>
<!DOCTYPE html>
<html>

<head>
  <title>HOME</title>
  <?php require '../reusable/header.php'; ?>
  <link rel="stylesheet" type="text/css" href="../resources/css/table.css">
  <link rel="stylesheet" type="text/css" href="../resources/css/sales.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
</head>

<body>
  <?php include '../reusable/sidebar.php'; ?>
<div class="panel">
  
  <?php include '../reusable/navbarNoSearch.html'; ?>
  <div class="dashboard-stats">
    <div class="stat-card">
      <div class="stat-value"><?= number_format(array_sum($ordertotal_values)) ?></div>
      <div class="stat-label">Total Revenue</div>
    </div>
    <div class="stat-card">
      <div class="stat-value"><?= count($ordertotal_values) ?></div>
      <div class="stat-label">Total Orders</div>
    </div>
    <div class="stat-card">
      <div class="stat-value"><?= number_format(array_sum($ordertotal_values) / count($ordertotal_values), 2) ?></div>
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
        $growth_rate = $enhanced_analytics['growth_metrics']['growth_rate'] ?? 0;
        echo number_format($growth_rate, 1) . '%';
        ?>
      </div>
      <div class="stat-label">Monthly Growth Rate</div>
    </div>
    <div class="stat-card">
      <div class="stat-value"><?= number_format($kpis['revenue_metrics']['revenue_30_days']) ?></div>
      <div class="stat-label">30-Day Revenue</div>
    </div>
    <div class="stat-card">
      <div class="stat-value"><?= number_format($kpis['customer_metrics']['revenue_per_customer'], 2) ?></div>
      <div class="stat-label">Revenue Per Customer</div>
    </div>
  </div>
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="table-header">
          <div class="title">
            <h2>Sales and Inventory Report</h2>
          </div>
        </div>
        <div class="table-header">
          <form>
            <div class="filter-controls">
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
                <option value="">All Days</option>
                <?php foreach ($days as $day): ?>
                  <option value="<?= $day ?>" <?= isset($_GET['day']) && $_GET['day'] == $day ? 'selected' : '' ?>>
                    <?= $day ?>
                  </option>
                <?php endforeach; ?>
              </select>

              <button type="submit" onclick="updateChartData(document.getElementById('year').value, document.getElementById('month').value, document.getElementById('day').value)">
                Apply Filter
              </button>
            </div>
          </form>
        </div>
        <div class="col-md-12">
          <div class="container"
            style="background-color: white; padding: 20px; border-radius: 5px; border: 1px solid var(--border-color);">

            <?php
            if (isset($_GET['year']) && isset($_GET['month']) && isset($_GET['day'])) {
              $filtered_year = $_GET['year'];
              $filtered_month = $_GET['month'];
              $filtered_day = $_GET['day'];
              $filtered_total_sum = 0;
              foreach ($orderdate_values as $key => $date) {
                $year = date('Y', strtotime($date));
                $month = date('m', strtotime($date));
                $day = date('d', strtotime($date));
                if (($filtered_year == '' || $year == $filtered_year) && ($filtered_month == '' || $month == $filtered_month) && ($filtered_day == '' || $day == $filtered_day)) {
                  $filtered_total_sum += $ordertotal_values[$key];
                }
              }
              echo '<p>Filtered Total Sum: ' . $filtered_total_sum . '</p>';
            }
            ?>
            <?php
            if (isset($_GET['year']) && isset($_GET['month']) && isset($_GET['day'])) {
              $filtered_year = $_GET['year'];
              $filtered_month = $_GET['month'];
              $filtered_day = $_GET['day'];

              if ($filtered_year == '' && $filtered_month == '' && $filtered_day == '') {
                $filtered_ordertotal_values = $ordertotal_values;
                $filtered_orderdate_values = $orderdate_values;
                $chartLabel = "All Orders";
              } else {
                $filtered_ordertotal_values = [];
                $filtered_orderdate_values = [];
                $chartLabel = "Orders";
                if ($filtered_year != '') {
                  $chartLabel .= " in " . $filtered_year;
                }
                if ($filtered_month != '') {
                  $chartLabel .= " " . date('F', mktime(0, 0, 0, $filtered_month, 1));
                }
                if ($filtered_day != '') {
                  $chartLabel .= " on " . $filtered_day;
                }
                foreach ($orderdate_values as $key => $date) {
                  $year = date('Y', strtotime($date));
                  $month = date('m', strtotime($date));
                  $day = date('d', strtotime($date));
                  if (($filtered_year == '' || $year == $filtered_year) && ($filtered_month == '' || $month == $filtered_month) && ($filtered_day == '' || $day == $filtered_day)) {
                    $filtered_ordertotal_values[] = $ordertotal_values[$key];
                    $filtered_orderdate_values[] = $date;
                  }
                }
              }
            }
            ?>
          </div>
        </div>
        <div class="chartscontainer">
          <div class="chartBox">
            <canvas id="myChart"></canvas>
          </div>

          <div class="rightchart">
            <div id="polarAreaChartContainer"
              style="background-color: #f9f9f9; padding: 20px; border: 1px solid #ddd; border-radius: 10px;overflow: visible; display: flex; flex-direction: column;">
              <canvas id="polarAreaChart"></canvas>
            </div>
            <div id="productSalesList"
              style="background-color: #f9f9f9; padding: 20px; border: 1px solid #ddd; border-radius: 10px;overflow: visible; display: flex; flex-direction: column;">
              <h4>Product Sales</h4>
              <ul>
                <?php foreach ($chart_labels_chart as $index => $label) { ?>
                  <li>
                    <?= $label ?>
                    (<?= number_format($chart_data_chart[$index] / array_sum($chart_data_chart) * 100, 2) ?>%)
                  </li>
                <?php } ?>
              </ul>
            </div>
          </div>
        </div>


      </div>
    </div>

    <section style="margin-left: 0.1rem; margin-top: 1rem;" class="prescriptive">
      <div class="recomendations">
        <div class="heading">
          <h2 style="text-align: center;" class="header2">Products Analysis and Recommendations</h2>
        </div>
        <?php
        $query = "SELECT * FROM customerorder";
        $result = mysqli_query($conn, $query);
        while ($row = mysqli_fetch_assoc($result)) {
          $label = $row['orderdescription'];
          // retrieve other data here
        }
        ?>
        <table style="width:100%; border-collapse: collapse;">
          <thead>
            <tr>
              <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Order Date</th>
              <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Product Ordered</th>
              <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Status</th>
              <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Sales</th>
              <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Recommendation</th>
              <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Expected Monthly Sales</th>
              <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Expected Annual Sales</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $query_recommendations = "SELECT orderdate, orderdescription, status, ordertotal FROM customerorder WHERE status = 'Completed'";
            $result_recommendations = mysqli_query($conn, $query_recommendations);

            while ($row_recommendations = mysqli_fetch_assoc($result_recommendations)) {
              $orderdate = $row_recommendations['orderdate'];
              $productname = $row_recommendations['orderdescription'];
              $status = $row_recommendations['status'];
              $ordertotal = $row_recommendations['ordertotal'];

              $year = date('Y', strtotime($orderdate));
              $month = date('m', strtotime($orderdate));

              $query_expected_sales = "SELECT SUM(ordertotal) AS total_sales FROM customerorder WHERE YEAR(orderdate) = '$year' AND MONTH(orderdate) = '$month'";
              $result_expected_sales = mysqli_query($conn, $query_expected_sales);
              $row_expected_sales = mysqli_fetch_assoc($result_expected_sales);

              $date = new DateTime($orderdate);
              $endOfMonth = $date->format('Y-m-t'); // Last day of the month
              $remainingDaysInMonth = (new DateTime($endOfMonth))->diff($date)->days + 1; // Include today

              $averageDailySales = $row_expected_sales['total_sales'] / date('t', strtotime($orderdate));
              $expectedMonthlySales = $averageDailySales * $remainingDaysInMonth;

              $query_expected_annual_sales = "SELECT SUM(ordertotal) AS total_sales FROM customerorder WHERE YEAR(orderdate) = '$year'";
              $result_expected_annual_sales = mysqli_query($conn, $query_expected_annual_sales);
              $row_expected_annual_sales = mysqli_fetch_assoc($result_expected_annual_sales);

              $endOfYear = $date->format('Y-12-31'); // Last day of the year
              $remainingDaysInYear = (new DateTime($endOfYear))->diff($date)->days + 1; // Include today

              $averageDailySalesAnnual = $row_expected_annual_sales['total_sales'] / date('z', strtotime($orderdate)) + 1;
              $expectedAnnualSale = $averageDailySalesAnnual * $remainingDaysInYear;

              $recommendation = "";
              if ($expectedMonthlySales <= $ordertotal) {
                $recommendation = "Offer targeted promotions to boost sales.";
              } else {
                $recommendation = "Maintain current strategy; monitor for continued success.";
              }
              echo "<tr style='border-bottom: 1px solid #ddd;'>";
              echo "<td style='border-right: 1px solid #ddd; padding: 8px; text-align: center;'>$orderdate</td>";
              echo "<td style='border-right: 1px solid #ddd; padding: 8px; text-align: center;'>$productname</td>";
              echo "<td style='border-right: 1px solid #ddd; padding: 8px; text-align: center;'>$status</td>";
              echo "<td style='border-right: 1px solid #ddd; padding: 8px; text-align: center;'>$ordertotal</td>";
              echo "<td style='border-right: 1px solid #ddd; padding: 8px; text-align: center;'>$recommendation</td>";
              echo "<td style='border-right: 1px solid #ddd; padding: 8px; text-align: center;'>$expectedMonthlySales</td>";
              echo "<td style='padding: 8px; text-align: center;'>$expectedAnnualSale</td>";
              echo "<td style='text-align: center;'>";
              $trend = $expectedMonthlySales > $ordertotal ?
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
    </section>

    <div class="recomendations">
      <div class="heading">
        <h2 class="header2">Advanced Product Analysis and Strategic Recommendations</h2>
      </div>
      <table>
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
          $result_recommendations = mysqli_query($conn, $query_recommendations);
          if ($result_recommendations) {
            while ($row = mysqli_fetch_assoc($result_recommendations)) {
              $metrics = [
                'ordertotal' => $row['ordertotal'] ?? 0,
                'avg_product_revenue' => $row['avg_product_revenue'] ?? 0,
                'order_frequency' => $row['order_frequency'] ?? 0,
                'performance_category' => $row['performance_category'] ?? 'Moderate Performer',
                'trend_direction' => $row['trend_direction'] ?? 'Stable'
              ];

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
              if ($metrics['avg_product_revenue'] > 0) {
                $percentage_change = (($metrics['ordertotal'] - $metrics['avg_product_revenue']) / $metrics['avg_product_revenue']) * 100;
              }

              echo "<tr class='$priority_class'>";
              echo "<td>{$row['orderdescription']}</td>";
              echo "<td>{$metrics['performance_category']}</td>";
              echo "<td class='trend-{$metrics['trend_direction']}'>{$metrics['trend_direction']}</td>";
              echo "<td>" . number_format($metrics['ordertotal'], 2) . " (" .
                ($percentage_change >= 0 ? '+' : '') .
                number_format($percentage_change, 1) .
                "%)</td>";
              echo "<td class='recommendations-cell'>$recommendation</td>";
              echo "<td class='priority-$priority_level'>$priority_level</td>";
              echo "</tr>";
            }
          }
          ?>
        </tbody>
      </table>
    </div>

    </section>



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
  </script>

  <?php include '../reusable/footer.php'; ?>
</body>

</html>