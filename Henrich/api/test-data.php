<?php
header('Content-Type: application/json');

echo json_encode([
    'sales' => [
        'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
        'data' => [1000, 1500, 800, 2000, 1800]
    ]
]);
