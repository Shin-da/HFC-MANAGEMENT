<!DOCTYPE html>
<html>

<head>
  <title></title>


  <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
  <style type="text/css">
    .card {
      
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
  <section >
    <div class="card" style="height: 420px;">
      <div class="card-content">
        <div class="card-header">
          <h2 class="card-title">Enhanced Inventory Analytics Dashboard</h2>
          <p class="card-description">Comprehensive inventory analysis with prescriptive recommendations</p>
        </div>

        <div class="tabs">
          <div class="tabs-list">
            <button class="tabs-trigger" value="inventory">Inventory Trends</button>
            <button class="tabs-trigger" value="products">Product Analysis</button>
          </div>
        </div>

        <div class="tabs-content" value="inventory">
          <div>
            <canvas id="inventory-chart"></canvas>
            <script>
              // Get the canvas element
              const ctx = document.getElementById("inventory-chart").getContext("2d");

              // Create some sample data for the chart
              const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun"];
              const sales = [100, 120, 150, 80, 110, 130];
              const stock = [500, 380, 230, 150, 340, 210];

              // Create the chart
              new Chart(ctx, {
                type: "line",
                data: {
                  labels: months,
                  datasets: [
                    {
                      label: "Sales",
                      data: sales,
                      borderColor: "rgb(255, 99, 132)",
                      backgroundColor: "rgba(255, 99, 132, 0.2)",
                      fill: false
                    },
                    {
                      label: "Stock",
                      data: stock,
                      borderColor: "rgb(54, 162, 235)",
                      backgroundColor: "rgba(54, 162, 235, 0.2)",
                      fill: false
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
                      text: "Inventory Trends"
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

        <div class="tabs-content" value="products">
          <div class="chart-container" style="height: 300px;">
            <div class="responsive-container" style="width: 100%; height: 100%;">
              <canvas id="product-chart"></canvas>
              <script>
                // Get the canvas element
                const productChartCtx = document.getElementById("product-chart").getContext("2d");

                // Create some sample data for the chart
                const productMonths = ["Jan", "Feb", "Mar", "Apr", "May", "Jun"];
                const productSales = [100, 120, 150, 80, 110, 130];
                const productStock = [500, 380, 230, 150, 340, 210];

                // Create the chart
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
      </div>
    </div>

  </section>

  <script src="prescriptive.js"></script>
</body>

</html>