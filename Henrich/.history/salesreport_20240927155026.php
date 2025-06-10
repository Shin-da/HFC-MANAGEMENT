/*************  ✨ Codeium Command 🌟  *************/
<?php
<?php 

require 'database/dbconnect.php';

$config = array(
    'type' => 'line',
    'data' => array(
        'labels' => array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'),
        'datasets' => array(
            array(
                'label' => 'Monthly Sales',
                'data' => array(10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 110, 120),
                'data' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
                'borderColor' => array('rgba(255, 99, 132, 1)'),
                'borderWidth' => 2,
                'backgroundColor' => array('rgba(255, 99, 132, 0.2)'),
                'fill' => false
            )
        )
    ),
    'options' => array(
        'responsive' => true,
        'maintainAspectRatio' => false,
        'scales' => array(
            'y' => array(
                'beginAtZero' => true
            )
        )
    )
);

$sql = "SELECT MONTH(DateTime) as Month, SUM(Quantity) as Sales FROM tblorders GROUP BY MONTH(DateTime) ORDER BY Month ASC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $config['data']['datasets'][0]['data'][$row['Month'] - 1] = $row['Sales'];
    }
}

$conn->close();

echo '<canvas id="sales-chart" style="width: 100%; height: 300px;"></canvas>';
echo '<script>var ctx = document.getElementById("sales-chart").getContext("2d");var salesChart = new Chart(ctx, ' . json_encode($config) . ');</script>';



/******  0f78e2b0-da33-4cc9-acd6-89fe47b27cb6  *******/