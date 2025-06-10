<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');

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
    // Sales Growth Rate
    'growth_rate' => $conn->query("
        WITH MonthlyStats AS (
            SELECT 
                DATE_FORMAT(orderdate, '%Y-%m') as month,
                SUM(ordertotal) as monthly_sales
            FROM customerorder 
            WHERE status = 'Completed'
            GROUP BY DATE_FORMAT(orderdate, '%Y-%m')
            ORDER BY month DESC
            LIMIT 2
        )
        SELECT 
            (((SELECT monthly_sales FROM MonthlyStats LIMIT 1) - 
              (SELECT monthly_sales FROM MonthlyStats LIMIT 1,1)) /
             (SELECT monthly_sales FROM MonthlyStats LIMIT 1,1) * 100) as growth_rate
    ")->fetch_assoc()['growth_rate'],

    // Product Category Performance
    'category_performance' => $conn->query("
        SELECT 
            SUBSTRING_INDEX(orderdescription, ' ', 1) as category,
            COUNT(*) as order_count,
            SUM(ordertotal) as total_revenue,
            AVG(ordertotal) as avg_order_value,
            COUNT(DISTINCT customerid) as unique_customers
        FROM customerorder
        WHERE status = 'Completed'
        GROUP BY category
        ORDER BY total_revenue DESC
    ")->fetch_all(MYSQLI_ASSOC),
    
    // Customer Retention Rate
    'retention_metrics' => $conn->query("
        SELECT 
            COUNT(DISTINCT CASE 
                WHEN orders >= 2 THEN customerid 
                END) * 100.0 / COUNT(DISTINCT customerid) as retention_rate,
            AVG(orders) as avg_orders_per_customer
        FROM (
            SELECT 
                customerid,
                COUNT(*) as orders
            FROM customerorder 
            WHERE status = 'Completed'
            GROUP BY customerid
        ) customer_orders
    ")->fetch_assoc(),
    
    // Peak Sales Periods
    'peak_periods' => $conn->query("
        SELECT 
            DATE_FORMAT(orderdate, '%H:00') as hour_of_day,
            COUNT(*) as order_count,
            SUM(ordertotal) as total_sales,
            AVG(ordertotal) as avg_order_value
        FROM customerorder
        WHERE status = 'Completed'
        GROUP BY DATE_FORMAT(orderdate, '%H')
        ORDER BY total_sales DESC
    ")->fetch_all(MYSQLI_ASSOC)
];

// Add new KPIs to the dashboard
$kpis = [
    'revenue_metrics' => $conn->query("
        SELECT 
            SUM(CASE 
                WHEN orderdate >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) 
                THEN ordertotal ELSE 0 END) as revenue_30_days,
            SUM(CASE 
                WHEN orderdate >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) 
                THEN ordertotal ELSE 0 END) as revenue_7_days,
            AVG(ordertotal) as avg_order_value,
            MAX(ordertotal) as highest_order_value
        FROM customerorder 
        WHERE status = 'Completed'
    ")->fetch_assoc(),
    
    'customer_metrics' => $conn->query("
        SELECT 
            COUNT(DISTINCT customerid) as total_customers,
            COUNT(*) / COUNT(DISTINCT customerid) as orders_per_customer,
            SUM(ordertotal) / COUNT(DISTINCT customerid) as revenue_per_customer
        FROM customerorder 
        WHERE status = 'Completed'
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
function generateRecommendation($metrics) {
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

?>
<!DOCTYPE html>
<html>

<head>
  <title>HOME</title>
  <?php require '../reusable/header.php'; ?>
  <link rel="stylesheet" type="text/css" href="../resources/css/table.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
  <style type="text/css">
    .chartscontainer {
      display: flex;
      flex-direction: row;
      gap: 20px;
      width: 100%;
      height: 100%;
      align-items: center;
      justify-content: center;
    }

    .chartscontainer>div {
      width: 50%;
    }

    .chartBox,
    .polarAreaChart {
      /* width: 48%; */
      float: left;
      padding: 0.5rem;
      height: 100%;
      width: 100%;
      background-color: #f8f9fa;
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
      border: 1px solid #e9ecef;
      margin-bottom: 1rem;
    }

    .chartBox canvas,
    .polarAreaChart canvas {
      background-color: #e9ecef;
      height: 100%;
      width: 100%;
      border-radius: 5px;
    }

    /* @media (max-width: 600px) {

      .chartBox,
      .polarAreaChart {
        width: 100%;
        margin: 0.5rem 0;
      }
    } */


    #polarAreaChartContainer {
      display: inline-block;
      vertical-align: top;
      height: 290px;
    }

    .card {
      position: relative;
      margin: 2rem;
      background-color: #f8f9fa;
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
      border: 1px solid #e9ecef;
      padding: 1rem;
      width: calc(100% - 4rem);
    }

    .card .tabs {
      margin-top: 2rem;
    }

    .tabs-list {
      background-color: #EFF3EA;
      width: 100%;
      max-width: 20.5em;
      height: 2.5em;
      border-radius: 5px;
      display: flex;
      justify-content: center;
      align-items: center;
      margin: 0 auto;
    }

    .tabs-trigger {
      height: 2em;
      width: 10em;
      border-radius: 3px;
      display: flex;
      justify-content: center;
      align-items: center;
      border: none;
      background-color: #F8FAFC;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .tabs-trigger:hover {
      background-color: #e2e6ea;
    }

    .recommendations {
      border: 1px solid #ddd;
      border-radius: 5px;
      padding: 1rem;
      margin: 1rem 0;
    }

    .heading {
      clear: both;
      margin-top: 1rem;
    }

    .header2 {
      padding-left: 1rem;
      font-weight: bold;
    }

    .dashboard-stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1rem;
      margin: 1rem 0;
    }

    .stat-card {
      background: white;
      padding: 1.5rem;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      transition: transform 0.2s;
    }

    .stat-card:hover {
      transform: translateY(-5px);
    }

    .stat-value {
      font-size: 1.8rem;
      font-weight: bold;
      color: #2c3e50;
    }

    .stat-label {
      color: #7f8c8d;
      font-size: 0.9rem;
    }

    .analytics-container {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 2rem;
      margin: 2rem 0;
    }

    .analysis-card {
      background: white;
      padding: 1.5rem;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .recommendations table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0 0.5rem;
      margin: 1rem 0;
    }

    .recommendations th,
    .recommendations td {
      padding: 1rem;
      background: white;
      border: none;
    }

    .recommendations tr {
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
      transition: transform 0.2s;
    }

    .recommendations tr:hover {
      transform: scale(1.01);
    }

    .trend-indicator {
      padding: 0.25rem 0.5rem;
      border-radius: 4px;
      font-size: 0.8rem;
    }

    .trend-up {
      background: #e3fcef;
      color: #0a8a3f;
    }

    .trend-down {
      background: #fee2e2;
      color: #dc2626;
    }
  </style>
  <style>
    :root {
      --primary-color: #2c3e50;
      --secondary-color: #34495e;
      --accent-color: #3498db;
      --success-color: #2ecc71;
      --warning-color: #f1c40f;
      --danger-color: #e74c3c;
      --light-bg: #f8f9fa;
      --border-color: #dee2e6;
      --text-dark: #2c3e50;
      --text-light: #7f8c8d;
    }

    .dashboard-stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1.5rem;
      padding: 1.5rem;
      background: var(--light-bg);
      border-radius: 10px;
      margin-bottom: 2rem;
    }

    .stat-card {
      background: white;
      padding: 1.5rem;
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      transition: all 0.3s ease;
      border: 1px solid var(--border-color);
    }

    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
    }

    .stat-value {
      font-size: 2rem;
      font-weight: 600;
      color: var(--primary-color);
      margin-bottom: 0.5rem;
    }

    .stat-label {
      color: var(--text-light);
      font-size: 0.9rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .filter-controls {
      background: white;
      padding: 1.5rem;
      border-radius: 10px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
      margin-bottom: 2rem;
      display: flex;
      gap: 1rem;
      align-items: center;
      flex-wrap: wrap;
    }

    .filter-controls select,
    .filter-controls button {
      padding: 0.5rem 1rem;
      border: 1px solid var(--border-color);
      border-radius: 5px;
      background: white;
      color: var(--text-dark);
      font-size: 0.9rem;
      transition: all 0.2s ease;
    }

    .filter-controls button {
      background: var(--accent-color);
      color: white;
      border: none;
      cursor: pointer;
    }

    .filter-controls button:hover {
      background: var(--secondary-color);
    }

    .charts-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
      gap: 2rem;
      padding: 1.5rem;
      margin-bottom: 2rem;
    }

    .chart-card {
      background: white;
      padding: 1.5rem;
      border-radius: 10px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .trend-indicator {
      padding: 0.4rem 0.8rem;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 500;
    }

    .trend-up {
      background: #e3fcef;
      color: #0a8a3f;
    }

    .trend-down {
      background: #fee2e2;
      color: #dc2626;
    }

    table {
      width: 100%;
      border-spacing: 0;
      border-collapse: separate;
      border-radius: 10px;
      overflow: hidden;
      margin: 2rem 0;
    }

    th {
      background: var(--light-bg);
      padding: 1rem;
      text-align: left;
      font-weight: 600;
      color: var(--text-dark);
    }

    td {
      padding: 1rem;
      border-bottom: 1px solid var(--border-color);
      color: var(--text-dark);
    }

    tr:last-child td {
      border-bottom: none;
    }

    tr:hover td {
      background: var(--light-bg);
    }
  </style>

</head>

<body>
  <?php include '../reusable/sidebar.php'; ?>
  
  <section class="panel">
    
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
        <div class="stat-value"><?= number_format($enhanced_analytics['growth_rate'], 1) ?>%</div>
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

  <script>
    const ctx = document.getElementById('myChart');
    const chartData = <?= json_encode($filtered_ordertotal_values ?? $ordertotal_values) ?>;
    const chartBackgroundColor = chartData.map(data => data > 1000 ? 'rgba(54, 162, 235, 0.7)' : 'rgba(255, 99, 132, 0.8)');
    const chartBorderColor = chartData.map(data => data > 1000 ? 'rgba(54, 162, 235, 1)' : 'rgba(255, 99, 132, 1)');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: <?= json_encode($filtered_orderdate_values ?? $orderdate_values) ?>,
        datasets: [{
          label: 'Total Sales',
          data: chartData,
          backgroundColor: chartBackgroundColor,
          borderColor: chartBorderColor,
          borderWidth: 1,
          borderRadius: 5, // Rounded edges
          borderSkipped: false // Apply rounded edges to all bars
        }]
      },
      options: {
        plugins: {
          title: {
            display: true,
            text: '<?= $chartLabel ?? "All Orders" ?>',
            color: 'black'
          },
          legend: {
            labels: {
              color: 'black'
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              color: 'black'
            }
          },
          x: {
            ticks: {
              color: 'black'
            }
          }
        },
        responsive: true,
        maintainAspectRatio: false
      }
    });
    const ctxPolar = document.getElementById('polarAreaChart').getContext('2d');
    const polarChart = new Chart(ctxPolar, {
      type: 'pie',
      data: {
        labels: <?= json_encode($chart_labels_chart) ?>,
        datasets: [{
          label: 'Product Sales',
          data: <?= json_encode($chart_data_chart) ?>,
          backgroundColor: [
            'rgba(34, 49, 63, 0.8)', // Darker and more solid color
            'rgba(54, 162, 235, 0.8)', // Darker and more solid color
            'rgba(255, 206, 86, 0.8)', // Darker and more solid color
            'rgba(75, 192, 192, 0.8)', // Darker and more solid color
            'rgba(153, 102, 255, 0.8)', // Darker and more solid color
            'rgba(255, 159, 64, 0.8)', // Darker and more solid color
            'rgba(199, 199, 199, 0.8)', // Darker and more solid color
            'rgba(83, 102, 255, 0.8)', // Darker and more solid color
            'rgba(255, 205, 86, 0.8)', // Darker and more solid color
            'rgba(75, 192, 192, 0.8)' // Darker and more solid color
          ],
          borderColor: [
            'rgba(34, 49, 63, 1)', // Darker and more solid color
            'rgba(54, 162, 235, 1)', // Darker and more solid color
            'rgba(255, 206, 86, 1)', // Darker and more solid color
            'rgba(75, 192, 192, 1)', // Darker and more solid color
            'rgba(153, 102, 255, 1)', // Darker and more solid color
            'rgba(255, 159, 64, 1)', // Darker and more solid color
            'rgba(199, 199, 199, 1)', // Darker and more solid color
            'rgba(83, 102, 255, 1)',
            'rgba(255, 205, 86, 1)', // Darker and more solid color
            'rgba(75, 192, 192, 1)' // Darker and more solid color
          ],
          borderWidth: 1
        }]
      },
      options: {
        plugins: {
          legend: {
            labels: {
              color: 'black'
            }
          }
        },
        responsive: true,
        maintainAspectRatio: false
      }
    });


    // Function to update the polar chart data
    function updateChartData(year, month, day) {
      var xhr = new XMLHttpRequest();
      xhr.open('GET', 'supervisor/sales.php?updateChartData=true&year=' + year + '&month=' + month + '&day=' + day, true);
      xhr.onload = function() {
        if (xhr.status === 200) {
          var data = JSON.parse(xhr.responseText);
          polarChart.data.labels = data.labels.slice(0, 10);
          polarChart.data.datasets[0].data = data.data.slice(0, 10);
          polarChart.update();
        }
      };
      xhr.send();
    }

    // Add peak hours chart
    const peakHoursCtx = document.getElementById('peakHoursChart').getContext('2d');
    new Chart(peakHoursCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode(array_column($enhanced_analytics['peak_periods'], 'hour_of_day')) ?>,
            datasets: [{
                label: 'Sales by Hour',
                data: <?= json_encode(array_column($enhanced_analytics['peak_periods'], 'total_sales')) ?>,
                borderColor: 'rgba(54, 162, 235, 1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Sales Distribution by Hour'
                }
            }
        }
    });

    // Add category performance chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($enhanced_analytics['category_performance'], 'category')) ?>,
            datasets: [{
                label: 'Revenue by Category',
                data: <?= json_encode(array_column($enhanced_analytics['category_performance'], 'total_revenue')) ?>,
                backgroundColor: 'rgba(75, 192, 192, 0.8)'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Category Performance'
                }
            }
        }
    });
  </script>

  <div class="analytics-insights">
    <div class="insight-card">
        <h3>Peak Sales Hours</h3>
        <div class="peak-hours-chart">
            <canvas id="peakHoursChart"></canvas>
        </div>
    </div>
    <div class="insight-card">
        <h3>Category Performance</h3>
        <div class="category-chart">
            <canvas id="categoryChart"></canvas>
        </div>
    </div>
  </div>

  <style>
    /* Add new styles */
    .analytics-insights {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 2rem;
        padding: 1.5rem;
        margin-top: 2rem;
    }

    .insight-card {
        background: white;
        padding: 1.5rem;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .insight-card h3 {
        margin-bottom: 1rem;
        color: var(--text-dark);
        font-size: 1.2rem;
    }

    .stat-card.highlight {
        background: linear-gradient(135deg, var(--accent-color), var(--primary-color));
        color: white;
    }

    .stat-card.highlight .stat-value,
    .stat-card.highlight .stat-label {
        color: white;
    }
  </style>

  <?php include '../reusable/footer.php'; ?>
</body>

</html>