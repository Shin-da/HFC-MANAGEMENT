<?php

if (isset($_SESSION['uid']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'supervisor') {
        $onhand = array();
        $productcode = array();

        $sql = "SELECT * FROM inventory";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $onhand[] = $row['onhand'];
                $productcode[] = $row['productcode'];
            }
        }

        $chart =<<<EOT
        <canvas id="myChart"></canvas>
        <script>
            var ctx = document.getElementById("myChart").getContext("2d");
            var myChart = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: <?php echo json_encode($productcode); ?>,
                    datasets: [{
                        label: "On Hand",
                        data: <?php echo json_encode($onhand); ?>,
                        backgroundColor: [
                            "rgba(255, 99, 132, 0.2)"
                        ],
                        borderColor: [
                            "rgba(255, 99, 132, 1)"
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            });
        </script>
EOT;
        echo $chart;

    } else {
        header("Location: ../index.php");
        exit();
    }
}

