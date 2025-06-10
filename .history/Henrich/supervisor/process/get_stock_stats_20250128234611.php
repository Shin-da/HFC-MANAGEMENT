<?php
require '../includes/config.php';

$statsQuery = $conn->query("SELECT 
    COUNT(*) as total_records,
    SUM(numberofbox) as total_boxes,
    SUM(totalpieces) as total_pieces,
    SUM(totalweight) as total_weight
    FROM stockmovement");

$stats = $statsQuery->fetch_assoc();

// Format numbers
$stats['total_records'] = number_format($stats['total_records']);
$stats['total_boxes'] = number_format($stats['total_boxes']);
$stats['total_pieces'] = number_format($stats['total_pieces']);
$stats['total_weight'] = number_format($stats['total_weight'], 2);

header('Content-Type: application/json');
echo json_encode($stats);
