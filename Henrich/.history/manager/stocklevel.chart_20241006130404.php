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
    }
}

// Create a gradient of colors for the bars
$colors = array();
$colorStart = array(255, 0, 0); // red
$colorEnd = array(0, 0, 255); // blue
$step = array(
    ($colorEnd[0] - $colorStart[0]) / (count($labels) - 1),
    ($colorEnd[1] - $colorStart[1]) / (count($labels) - 1),
    ($colorEnd[2] - $colorStart[2]) / (count($labels) - 1)
);

for ($i = 0; $i < count($labels); $i++) {
    $colors[] = "linear-gradient(to right, rgba(" .
    $colors[] = "rgba(" .
        intval($colorStart[0] + $step[0] * $i) . "," .
        intval($colorStart[1] + $step[1] * $i) . "," .
        intval($colorStart[2] + $step[2] * $i) . ", 0.5)" .
        ", rgba(" .
        intval($colorStart[0] + $step[0] * ($i + 1)) . "," .
        intval($colorStart[1] + $step[1] * ($i + 1)) . "," .
        intval($colorStart[2] + $step[2] * ($i + 1)) . ", 0.5)";
        intval($colorStart[2] + $step[2] * $i) . ", 0.5)";
}

?>
<!-- Create a canvas element to render the chart -->
<canvas id="stock-chart" style="width: 100%; height: 300px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);"></canvas>

<!-- Include the Chart.js library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>

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
                backgroundColor: <?php echo json_encode($colors); ?>,
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



/******  cdb88b6e-3135-412d-b8b0-d6bc560a6f57  *******/