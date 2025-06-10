<?php

$labels = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');

$data = array();
foreach ($labels as $label) {
    $data[] = rand(1, 100);
}

$config = array(
    'type' => 'line',
    'data' => array(
        'labels' => $labels,
        'datasets' => array(
            array(
                'label' => 'Monthly Sales',
                'data' => $data,
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

