<?php 
include "./database/dbconnect.php";

$sql = "SELECT MONTH(DateTime) as Month, SUM(Quantity) as Sales FROM tblorders GROUP BY MONTH(DateTime) ORDER BY Month ASC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $salesData = array();
    while($row = $result->fetch_assoc()) {
        $salesData[] = array(
            'Month' => $row['Month'], 
            'Sales' => $row['Sales']
        );
    }
    $config = array(
        'type' => 'line',
        'data' => array(
            'labels' => array(),
            'datasets' => array(
                array(
                    'label' => 'Monthly Sales',
                    'data' => array(),
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
    foreach ($salesData as $data) {
        $config['data']['labels'][] = $data['Month'];
        $config['data']['datasets'][0]['data'][] = $data['Sales'];
    }
    echo json_encode($salesData);
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}
$conn->close();

