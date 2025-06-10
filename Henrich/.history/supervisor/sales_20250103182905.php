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
<html>

<head>
  <title>HOME</title>
  <?php require '../reusable/header.php'; ?>
  <link rel="stylesheet" type="text/css" href="../resources/css/table.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
  <style type="text/css">
    .chartBox, .polarAreaChart {
      width: 48%;
      margin: 1%;
      padding: 1rem;
      background-color: #ffffff;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      border: 1px solid #cccccc;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .chartBox canvas, .polarAreaChart canvas {
      height: 280px;
      width: 100%;
      background-color: #f5f5f5;
    }

    .chartBox {
      fill: #4caf50;
      stroke: #4caf50;
    }

    @media (max-width: 600px) {
      .chartBox, .polarAreaChart {
        width: 100%;
        margin-bottom: 1rem;
      }
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
              <?php foreach ($years as $year) : ?>
                <option value="<?= $year ?>" <?= isset($_GET['year']) && $_GET['year'] == $year ? 'selected' : '' ?>>
                  <?= $year ?>
                </option>
              <?php endforeach; ?>
            </select>
            <label for="month">Month:</label>
            <select id="month" name="month">
              <option value="">All</option>
              <?php foreach ($months as $month) : ?>
                <option value="<?= $month ?>" <?= isset($_GET['month']) && $_GET['month'] == $month ? 'selected' : '' ?>>
                  <?= date('F', mktime(0, 0, 0, $month, 1)) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <button type="submit">Filter</button>
          </form>
        </div>
        <div class="col-md-12">
          <div class="container" style="background-color: white; padding: 20px; border-radius: 5px; border: 1px solid var(--border-color);">
           
              <?php
              if (isset($_GET['year']) && isset($_GET['month'])) {
                $filtered_year = $_GET['year'];
                $filtered_month = $_GET['month'];
                $filtered_total_sum = 0;
                foreach ($orderdate_values as $key => $date) {
                  $year = date('Y', strtotime($date));
                  $month = date('m', strtotime($date));
                  if (($filtered_year == '' || $year == $filtered_year) && ($filtered_month == '' || $month == $filtered_month)) {
                    $filtered_total_sum += $ordertotal_values[$key];
                  }
                }
                echo '<p>Filtered Total Sum: ' . $filtered_total_sum . '</p>';
              }
              ?>
              <?php
              if (isset($_GET['year']) && isset($_GET['month'])) {
                $filtered_year = $_GET['year'];
                $filtered_month = $_GET['month'];

                if ($filtered_year == '' && $filtered_month == '') {
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
                  foreach ($orderdate_values as $key => $date) {
                    $year = date('Y', strtotime($date));
                    $month = date('m', strtotime($date));
                    if (($filtered_year == '' || $year == $filtered_year) && ($filtered_month == '' || $month == $filtered_month)) {
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
        </div>
      </div>
    </div>
  </section>




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
          label: 'Number of Orders',
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
   const chartPolar = new Chart(ctxPolar, {
     type: 'polarArea',
     data: {
       labels: <?= json_encode($chart_labels_chart) ?>,
       datasets: [{
         label: 'Product Sales',
         data: <?= json_encode($chart_data_chart) ?>,
         backgroundColor: [
           'rgba(255, 99, 132, 0.2)',
           'rgba(54, 162, 235, 0.2)',
           'rgba(255, 206, 86, 0.2)',
           'rgba(75, 192, 192, 0.2)',
           'rgba(153, 102, 255, 0.2)'
         ],
         borderColor: [
           'rgba(255, 99, 132, 1)',
           'rgba(54, 162, 235, 1)',
           'rgba(255, 206, 86, 1)',
           'rgba(75, 192, 192, 1)',
           'rgba(153, 102, 255, 1)'
         ],
         borderWidth: 1
       }]
     },
     options: {
       scales: {
         y: {
           beginAtZero: true
         }
       },
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
   // Function to update the chart data
   function updateChartData(year, month) {
     var xhr = new XMLHttpRequest();
     xhr.open('GET', 'supervisor/sales.php?updateChartData=true&year=' + year + '&month=' + month, true);
     xhr.onload = function() {
       if (xhr.status === 200) {
         var data = JSON.parse(xhr.responseText);
         polarChart.data.labels = data.labels;
         polarChart.data.datasets[0].data = data.data;
         polarChart.update();
       }
     };
     xhr.send();
   }
   
  </script>
  <?php include '../reusable/footer.php'; ?>
</body>
</html>


