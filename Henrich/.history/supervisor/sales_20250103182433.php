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
  $sql = "SELECT * FROM dbhenrichfoodcorps.orderhistory";
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
$sql_polar = "SELECT pl.productname, SUM(oh.ordertotal) AS total_sales
              FROM orderhistory oh
              JOIN productlist pl ON oh.orderdescription LIKE CONCAT('%', pl.productname, '%')
              GROUP BY pl.productname
              ORDER BY total_sales DESC
              LIMIT 10";
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
  $sql_polar = "SELECT pl.productname, SUM(oh.ordertotal) AS total_sales
                FROM orderhistory oh
                JOIN productlist pl ON oh.orderdescription LIKE CONCAT('%', pl.productname, '%')
                WHERE YEAR(oh.orderdate) = '$filtered_year' AND MONTH(oh.orderdate) = '$filtered_month'
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


// Get unique years and months from orderdate
$years = array_unique(array_map(fn($date) => date('Y', strtotime($date)), $orderdate_values));
$months = array_unique(array_map(fn($date) => date('m', strtotime($date)), $orderdate_values));

// Pagination for stock management table
$page = $_GET['page'] ?? 1;
$limit = $_GET['limit'] ?? 10;
$start = ($page - 1) * $limit;
$items = $conn->query("SELECT * FROM inventory LIMIT $start, $limit");
$totalRecords = $conn->query("SELECT COUNT(*) FROM inventory")->fetch_row()[0];
$totalPages = ceil($totalRecords / $limit);

?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sales Report</title>
  <link rel="stylesheet" href="../resources/css/main.css">
  <link rel="stylesheet" href="../resources/css/table.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
  <style>
    .chartBox, .polarAreaChart {
      width: 50%;
      float: left;
      padding: 0.5rem;
      background-color: white;
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
      border: 1px solid #e9ecef;
    }

    .chartBox canvas, .polarAreaChart canvas {
      background-color: #e9ecef;
      height: 300px;
      width: 100%;
    }

    .chartBox  {
      fill: green;
      stroke: green;
    }

    @media (max-width: 600px) {
      .chartBox, .polarAreaChart {
        width: 100%;
        margin: 0.5rem;
      }
    }
  </style>
</head>

<body>
  <?php include '../reusable/sidebar.php'; ?>
  <section class="panel">
    <?php include '../reusable/navbarNoSearch.html'; ?>

