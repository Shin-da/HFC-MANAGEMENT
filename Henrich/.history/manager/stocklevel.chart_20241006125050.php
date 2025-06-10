<?php // stocklevel.chart.php

$sql = "SELECT * FROM inventory";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $onhand[] = $row['onhand'];
        $productcode[] = $row['productcode'];
    }
}
?>

<!-- Create a canvas element to render the chart -->
<canvas id="stock-chart" style="width: 100%; height: 300px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);"></canvas>';

<!-- Include the Chart.js library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>';

<!-- Create the chart using Chart.js -->
<script>
    var ctx = document.getElementById("stock-chart").getContext("2d");
    var chart = new Chart(ctx, {
                type: "bar",
                ';
                echo '  data: {';
                echo '    labels: '.json_encode($productcode).
                ',';
                echo '    datasets: [{';
                echo '      label: "Stock Levels",';
                echo '      data: '.json_encode($onhand).
                ',';
                echo '      backgroundColor: [';
                foreach($productcode as $key => $value) {
                    echo '"rgba('.rand(0, 255).
                    ','.rand(0, 255).
                    ','.rand(0, 255).
                    ', 0.5)"';
                    if ($key < count($productcode) - 1) {
                        echo ',';
                    }
                }
                echo '      ],';
                echo '      borderColor: [';
                foreach($productcode as $key => $value) {
                    echo '"#630A10"';
                    if ($key < count($productcode) - 1) {
                        echo ',';
                    }
                }
                echo '      ],';
                echo '      borderWidth: 2,';
                echo '      borderRadius: 5,';
                echo '      hoverBorderWidth: 3,';
                echo '      hoverBorderColor: "rgba(0, 0, 0, 1)"';
                echo '    }]';
                echo '  },';
                echo '  options: {';
                echo '    legend: {';
                echo '      display: false';
                echo '    },';
                echo '    scales: {';
                echo '      y: {';
                echo '        beginAtZero: true,';
                echo '        ticks: {';
                echo '          fontColor: "rgba(0, 0, 0, 0.5)"';
                echo '        }';
                echo '      },';
                echo '      x: {';
                echo '        ticks: {';
                echo '          fontColor: "rgba(0, 0, 0, 0.5)"';
                echo '        }';
                echo '      }';
                echo '    },';
                echo '    title: {';
                echo '      display: true,';
                echo '      text: "Stock Levels",';
                echo '      fontSize: 20,';
                echo '      fontColor: "rgba(0, 0, 0, 0.8)"';
                echo '    },';
                echo '    layout: {';
                echo '      padding: {';
                echo '        left: 10,';
                echo '        right: 10,';
                echo '        top: 10,';
                echo '        bottom: 10';
                echo '      }';
                echo '    },';
                echo '    plugins: {';
                echo '      datalabels: {';
                echo '        display: true,';
                echo '        formatter: function(value, context) {';
                echo '          return context.chart.data.labels[context.dataIndex] + ": " + value + " items";';
                echo '        },';
                echo '        color: "#000",';
                echo '        font: {';
                echo '          weight: "bold",';
                echo '          size: 12';
                echo '        }';
                echo '      }';
                echo '    }';
                echo '  }';
                echo '});';
                echo '
</script>';