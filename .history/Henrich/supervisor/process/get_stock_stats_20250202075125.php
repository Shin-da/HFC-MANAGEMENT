<?php
require '../includes/config.php';

$statsQuery = $conn->query("SELECT 
    COUNT(*) as total_records,
    SUM(numberofbox) as total_boxes,
    SUM(totalpacks) as total_packs,
    SUM(totalweight) as total_weight
    FROM stockmovement");

$stats = $statsQuery->fetch_assoc();

// Format numbers
$stats['total_records'] = number_format($stats['total_records']);
$stats['total_boxes'] = number_format($stats['total_boxes']);
$stats['total_packs'] = number_format($stats['total_packs']);
$stats['total_weight'] = number_format($stats['total_weight'], 2);

header('Content-Type: application/json');
echo json_encode($stats);
