<?php
require_once '../includes/config.php';

$query = "SELECT 
    DATE_FORMAT(dateencoded, '%Y-%m') as month,
    COUNT(*) as count
FROM stockactivitylog
GROUP BY month
ORDER BY month ASC
LIMIT 12";

$result = $conn->query($query);
$data = [
    'labels' => [],
    'values' => []
];

while ($row = $result->fetch_assoc()) {
    $data['labels'][] = date('M Y', strtotime($row['month']));
    $data['values'][] = (int)$row['count'];
}

header('Content-Type: application/json');
echo json_encode($data);
