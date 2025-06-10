<?php
// require '../reusable/redirect404.php';
// require '../session/session.php';
require './';
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Fetch order and inventory data
$ordertotal_values = [];
$orderdate_values = [];
$total_sum = 0;
$combined_data = array();

$sql = "SELECT oh.ordertotal, oh.orderdate, i.onhandquantity AS inventorylevel
        FROM dbhenrichfoodcorps.orderhistory oh
        JOIN dbhenrichfoodcorps.inventory i ON oh.productcode = i.productcode";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $combined_data[] = array(
            'label' => $row['orderdate'],
            'sales' => $row['ordertotal'],
            'inventory' => $row['inventorylevel']
        );
    }
} else {
    echo "No records found.";
}


// Populate chart data when page is first loaded
$chart_labels_chart = array();
$chart_data_chart = array();
$sql_polar = "SELECT pl.productname, SUM(oh.ordertotal) AS total_sales
              FROM orderhistory oh
              JOIN productlist pl ON oh.orderdescription LIKE CONCAT('%', pl.productname, '%')
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
                FROM orderhistory oh
                JOIN productlist pl ON oh.orderdescription LIKE CONCAT('%', pl.productname, '%')
                WHERE YEAR(oh.orderdate) = '$filtered_year' AND MONTH(oh.orderdate) = '$filtered_month' AND DAY(oh.orderdate) = '$filtered_day'
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

// Pagination for stock management table
$page = $_GET['page'] ?? 1;
$limit = $_GET['limit'] ?? 10;
$start = ($page - 1) * $limit;
$items = $conn->query("SELECT * FROM inventory LIMIT $start, $limit");
$totalRecords = $conn->query("SELECT COUNT(*) FROM inventory")->fetch_row()[0];
$totalPages = ceil($totalRecords / $limit);

?>
<!DOCTYPE html>
<html>

<head>
  <title>HOME</title>
  <?php require '../reusable/header.php'; ?>
  <link rel="stylesheet" type="text/css" href="../resources/css/table.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
  <style type="text/css">
    .chartBox,
    .polarAreaChart {
      width: 50%;
      float: left;
      padding: 0.5rem;
      background-color: #f8f9fa;
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
      border: 1px solid #e9ecef;
    }

    .chartBox canvas,
    .polarAreaChart canvas {
      background-color: #e9ecef;
      height: 300px;
      width: 100%;
    }

    .chartBox {
      fill: green;
      stroke: green;
    }

    @media (max-width: 600px) {

      .chartBox,
      .polarAreaChart {
        width: 100%;
        margin: 0.5rem;
      }
    }

    .card {
      position: absolute;
      left: 2rem;
      bottom: -3rem;
      background-color: #f8f9fa;
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
      border: 1px solid #e9ecef;
      padding: 1rem;
    }

    .card .tabs {
      margin-top: 2rem;
    }

    .tabs-list {
      background-color: #EFF3EA;
      width: 20.5em;
      height: 2.5em;
      border-radius: 5px;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .tabs-trigger {
      height: 2em;
      width: 10em;
      border-radius: 3px;
      display: flex;
      justify-content: center;
      border: none;
      background-color: #F8FAFC;
    }
  </style>
</head>

<body>
  <?php include '../reusable/sidebar.php'; ?>
  <section class="panel">
    <?php include '../reusable/navbarNoSearch.html'; ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="table-header">
            <div class="title">
              <h2>Sales Report</h2>
            </div>
          </div>
          <div class="table-header">
            <form>
              <label for="year">Year:</label>
              <select id="year" name="year">
                <option value="">All</option>
                <?php foreach ($years as $year): ?>
                  <option value="<?= $year ?>" <?= isset($_GET['year']) && $_GET['year'] == $year ? 'selected' : '' ?>>
                    <?= $year ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <label for="month">Month:</label>
              <select id="month" name="month">
                <option value="">All</option>
                <?php foreach ($months as $month): ?>
                  <option value="<?= $month ?>" <?= isset($_GET['month']) && $_GET['month'] == $month ? 'selected' : '' ?>>
                    <?= date('F', mktime(0, 0, 0, $month, 1)) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <label for="day">Day:</label>
              <select id="day" name="day">
                <option value="">All</option>
                <?php foreach ($days as $day): ?>
                  <option value="<?= $day ?>" <?= isset($_GET['day']) && $_GET['day'] == $day ? 'selected' : '' ?>>
                    <?= $day ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <button type="submit">Filter</button>
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
          <div class="chartBox">
            <canvas id="myChart"></canvas>
          </div>
          <div class="polarAreaChart">
            <canvas id="polarAreaChart"></canvas>
          </div>
          <div>
            <canvas id="combined-chart"></canvas>
          </div>

        </div>
      </div>

    </div>
    </div>

  </section>
  <script>
    var orderdate_values = <?= json_encode($orderdate_values) ?>;
    var ordertotal_values = <?= json_encode($ordertotal_values) ?>;
    var inventory_values = <?= json_encode($inventory_values) ?>;
  </script>
  <script>
    var chartData = <?= json_encode($filtered_ordertotal_values ?? $ordertotal_values) ?>;
    var chartLabels = <?= json_encode($filtered_orderdate_values ?? $orderdate_values) ?>;
    var chartLabel = '<?= $chartLabel ?? "All Orders" ?>';
    var chartLabelsChart = <?= json_encode($chart_labels_chart) ?>;
    var chartDataChart = <?= json_encode($chart_data_chart) ?>;
    console.log(chartData);
    console.log(chartLabels);
    console.log(chartLabel);
    console.log(chartLabelsChart);
    console.log(chartDataChart);
  </script>

  <script>
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
  </script>
  <script src="sales.js"></script>
  <?php include '../reusable/footer.php'; ?>
</body>

</html>