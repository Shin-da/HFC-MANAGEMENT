/*************  ✨ Codeium Command 🌟  *************/
<?php // stocklevel.chart.php

$sql = "SELECT * FROM inventory";
$result = $conn->query($sql);

$labels = array();
$data = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['productcode'];
        $data[] = $row['onhand'];
        $onhand[] = $row['onhand'];
        $productcode[] = $row['productcode'];
    }
}

?>

<!-- Create a canvas element to render the chart -->
<canvas id="stock-chart" style="width: 100%; height: 300px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);"></canvas>
<canvas id="stock-chart" style="width: 100%; height: 300px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);"></canvas>';

<!-- Include the Chart.js library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>';

<!-- Create the chart using Chart.js -->
<script>
    var ctx = document.getElementById("stock-chart").getContext("2d");
    var chart = new Chart(ctx, {
        type: "bar",
        data: {
            labels: <?php echo json_encode($labels); ?>,
            datasets: [{
                label: "Stock Levels",
                data: <?php echo json_encode($data); ?>,
                backgroundColor: <?php
                $colors = array();
                foreach($labels as $key => $value) {
                    $colors[] = "rgba(".rand(0, 255).",".rand(0, 255).",".rand(0, 255).", 0.5)";
                type: "bar",
                data: labels: '.json_encode($productcode)
                datasets: [{';
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
                echo json_encode($colors);
                ?>,
                borderColor: <?php
                $colors = array();
                foreach($labels as $key => $value) {
                    $colors[] = "#630A10";
                echo '      ],';
                echo '      borderColor: [';
                foreach($productcode as $key => $value) {
                    echo '"#630A10"';
                    if ($key < count($productcode) - 1) {
                        echo ',';
                    }
                }
                echo json_encode($colors);
                ?>,
                borderWidth: 2,
                borderRadius: 5,
                hoverBorderWidth: 3,
                hoverBorderColor: "rgba(0, 0, 0, 1)"
            }]
        },
        options: {
            legend: {
                display: false
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        fontColor: "rgba(0, 0, 0, 0.5)"
                    }
                },
                x: {
                    ticks: {
                        fontColor: "rgba(0, 0, 0, 0.5)"
                    }
                }
            },
            title: {
                display: true,
                text: "Stock Levels",
                fontSize: 20,
                fontColor: "rgba(0, 0, 0, 0.8)"
            },
            layout: {
                padding: {
                    left: 10,
                    right: 10,
                    top: 10,
                    bottom: 10
                }
            },
            plugins: {
                datalabels: {
                    display: true,
                    formatter: function(value, context) {
                        return context.chart.data.labels[context.dataIndex] + ": " + value + " items";
                    },
                    color: "#000",
                    font: {
                        weight: "bold",
                        size: 12
                    }
                }
            }
        }
    });
</script>
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
/******  647c0563-dce1-4926-842b-5f2583e1d4b4  *******/