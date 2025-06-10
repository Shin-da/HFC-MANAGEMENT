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
                backgroundColor: function(context) {
                var index = context.dataIndex;
                var value = context.dataset.data[index];
                var randomColor = getRandomColor(); // generate a random color
                var gradientColor = getGradientColor(randomColor, index); // apply gradient effect to the random color
                return gradientColor;
            },
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

    function getRandomColor() {
</script>

