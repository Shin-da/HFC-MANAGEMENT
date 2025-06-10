/*************  ✨ Codeium Command 🌟  *************/
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
        echo '<canvas id="myChart"></canvas>';
        echo '<script> ';

        echo 'var ctx = document.getElementById("myChart").getContext("2d");';
        echo 'var myChart = new Chart(ctx, {';

        echo 'type: "bar",';
        echo 'data: {';
        echo 'labels: ' . json_encode($productcode) . ',';
        echo 'datasets: [{';
        echo 'label: "On Hand",';
        echo 'data: ' . json_encode($onhand) . ',';
        echo 'backgroundColor: [';
        echo '"rgba(255, 99, 132, 0.2)"';
        echo '],';
        echo 'borderColor: [';
        echo '"rgba(255, 99, 132, 1)"';
        echo '],';
        echo 'borderWidth: 1';
        echo '}' . ']';
        echo '},';

        echo 'options: {';
        echo 'scales: {';
        echo 'yAxes: [{';
        echo 'ticks: {';
        echo 'beginAtZero: true';
        echo '},';
        echo '}]';
        echo '},';
        echo '}';

        echo '});';

        echo '</script>';

    } else {
        header("Location: ../index.php");
        exit();
    }
}


/******  624b19ba-9541-4a3f-a19a-76fe28d32b3e  *******/