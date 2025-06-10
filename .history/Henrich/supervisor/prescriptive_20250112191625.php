<?php
require '../database/dbconnect.php';

$sql = "SELECT 
  co.productname, 
  co.productstatus AS status,
  SUM(co.productprice) AS sales, 
  i.availablequantity AS stocks
FROM 
  customerorder co
  INNER JOIN inventory i ON co.productcode = i.productcode
GROUP BY 
  co.productname, co.productstatus, i.availablequantity";

$result = $conn->query($sql);

$sales_data = array();
$stocks_data = array();
$product_labels = array();

while ($row = $result->fetch_assoc()) {
  $product_labels[] = $row["productname"];
  $product_status[] = $row["status"];
  $sales_data[] = $row["sales"];
  $stocks_data[] = $row["stocks"];
}

$data = array(
  'product_labels' => $product_labels,
  'product_status' => $product_status,
  'sales_data' => $sales_data,
  'stocks_data' => $stocks_data
);
?>
fix the pagination
<!DOCTYPE html>
if admin is logged, show relevant actions that can be made (like edit delete) that other users can;t do
<html>emphasize the icons with functions
when all rows are shown, arranged then by what order should be prioritized
<head>reformat the design of the header
  <title></title>

  <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
  <style type="text/css">
    .card {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background-color: #f8f9fa;
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
      border: 1px solid #e9ecef;
      padding: 1rem;
      display: flex;
      height: auto;
      width: 1200px;
    }

    .card-content {
      flex: 1;
      padding: 1rem;
      justify-content: center;
      align-items: center;
      overflow-y: hidden;
      /* Remove scrollbar */
    }

    .card .tabs {
      margin-top: 2rem;
    }

    .tabs-list {
      background-color: #EFF3EA;
      width: 17em;
      height: 2em;
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
      align-items: center;
      border: none;
      background-color: #F8FAFC;
      transition: background-color 0.3s ease;
    }

    .tabs-trigger.inactive {
      background-color: #D8DADE;
    }

    .tabs-content {
      display: flex;
      border: 1px solid #e9ecef;
      overflow-y: hidden;
      /* Remove scrollbar */
    }

    .recomendations {
      border: 1px solid #e9ecef;
      border-radius: 3px;
      margin-top: 1rem;
      display: flex;
      overflow-y: hidden;
      /* Remove scrollbar */
    }

    .summary {
      border: 1px solid #e9ecef;
      border-radius: 3px;
      margin-top: 1rem;
      display: flex;
      overflow-y: hidden;
      /* Remove scrollbar */
    }

    .header {
      margin-left: 1rem;
    }

    .summary-header {
      margin-left: 1rem;
    }
  </style>
</head>

<body>
  <section class="container-section">
    <div class="card">
      <div class="card-content">
        <div class="card-header">
          <h2 class="card-title">Enhanced Inventory Analytics Dashboard</h2>
          <p class="card-description">Comprehensive inventory analysis with prescriptive recommendations</p>
        </div>

        <div class="tabs">
          <div class="tabs-list">
            <button class="tabs-trigger" value="products" onclick="toggleTab(this)">Products Overiew</button>
            <button class="tabs-trigger" value="inventory" onclick="toggleTab(this)">Inventory Analysis</button>
          </div>
        </div>

        <div class="tabs-content" value="products">
          <div>
            <canvas id="productsOverview"></canvas>
            <script>
              var data = <?php echo json_encode($data); ?>;
              var product_labels = data.product_labels;
              var sales_data = data.sales_data;
              var stocks_data = data.stocks_data;

              // Create a new chart for the stock data
              const productsOverview = new Chart(document.getElementById('productsOverview'), {
                type: 'bar',
                data: {
                  labels: product_labels, // Use the product labels as the x-axis labels
                  datasets: [{
                    label: 'Sales',
                    data: sales_data, // Use the sales data as the sales data
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                  }, {
                    label: 'Stocks',
                    data: stocks_data, // Use the stocks data as the stocks data
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                  }]
                },
                options: {
                  title: {
                    display: true,
                    text: 'Sales and Stocks'
                  }
                }
              });

            </script>
          </div>
        </div>

        <div class="tabs-content" value="inventory">
          <div class="chart-container" style="height: 300px;">
            <div class="responsive-container" style="width: 100%; height: 100%;">
              <canvas id="inventoryAnalysis"></canvas>
              <script>
                const productChartCtx = document.getElementById("inventoryAnalysis").getContext("2d");
                const productMonths = ["Jan", "Feb", "Mar", "Apr", "May", "Jun"];
                const productSales = [100, 120, 150, 80, 110, 130];
                const productStock = [500, 380, 230, 150, 340, 210];

                new Chart(productChartCtx, {
                  type: "bar",
                  data: {
                    labels: productMonths,
                    datasets: [
                      {
                        label: "Product Sales",
                        data: productSales,
                        backgroundColor: "rgba(255, 99, 132, 0.2)",
                        borderColor: "rgb(255, 99, 132)",
                        borderWidth: 1
                      },
                      {
                        label: "Product Stock",
                        data: productStock,
                        backgroundColor: "rgba(54, 162, 235, 0.2)",
                        borderColor: "rgb(54, 162, 235)",
                        borderWidth: 1
                      }
                    ]
                  },
                  options: {
                    scales: {
                      y: {
                        beginAtZero: true
                      }
                    },
                    plugins: {
                      title: {
                        display: true,
                        text: "Product Analysis"
                      },
                      legend: {
                        labels: {
                          color: "black"
                        }
                      }
                    },
                    responsive: true,
                    maintainAspectRatio: false
                  }
                });
              </script>
            </div>
          </div>
        </div>

        <div class="recomendations">
          <div class="header">
            <h2>Products Analysis and Recomendations</h2>
            <table>
              <thead>
                <tr>
                  <th>Product Name</th>
                  <th>Sales</th>
                  <th>Stocks</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($data['product_labels'] as $index => $label) { ?>
                  <tr>
                    <td><?php echo $label; ?></td>
                    <td><?php echo $data['sales_data'][$index]; ?></td>
                    <td><?php echo $data['stocks_data'][$index]; ?></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
        <div class="summary">
          <div class="summary-header">
            <h2>Summary</h2>
          </div>
        </div>
      </div>

    </div>
  </section>

  <script>
    function toggleTab(button) {
      const buttons = document.querySelectorAll('.tabs-trigger');
      buttons.forEach(btn => btn.classList.remove('inactive'));
      button.classList.add('inactive');
    }
  </script>
  <script src="prescriptive.js"></script>
</body>

</html>