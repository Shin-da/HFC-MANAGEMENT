<?php

$config = array(
    'type' => 'line',
    'data' => array(
        'labels' => array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'),
        'datasets' => array(
            array(
                'label' => 'Monthly Sales',
                'data' => array(10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 110, 120),
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

echo '<canvas id="sales-chart" style="width: 100%; height: 300px;"></canvas>';
echo '<script>var ctx = document.getElementById("sales-chart").getContext("2d");var salesChart = new Chart(ctx, ' . json_encode($config) . ');</script>';



