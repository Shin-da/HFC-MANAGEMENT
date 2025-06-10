<?php
require_once '../includes/config.php';

$days = isset($_GET['days']) ? intval($_GET['days']) : 30;

try {
    // Query for IN movements
    $in_query = "SELECT 
        DATE(dateencoded) as date,
        SUM(totalpieces) as pieces,
        COUNT(*) as count
    FROM stockmovement 
    WHERE movement_type = 'IN'
    AND dateencoded >= DATE_SUB(CURRENT_DATE, INTERVAL ? DAY)
    GROUP BY DATE(dateencoded)";

    // Query for OUT movements
    $out_query = "SELECT 
        DATE(dateencoded) as date,
        SUM(totalpieces) as pieces,
        COUNT(*) as count
    FROM stockmovement 
    WHERE movement_type = 'OUT'
    AND dateencoded >= DATE_SUB(CURRENT_DATE, INTERVAL ? DAY)
    GROUP BY DATE(dateencoded)";

    $stmt_in = $conn->prepare($in_query);
    $stmt_out = $conn->prepare($out_query);

    $stmt_in->bind_param('i', $days);
    $stmt_out->bind_param('i', $days);

    $stmt_in->execute();
    $result_in = $stmt_in->get_result();

    $stmt_out->execute();