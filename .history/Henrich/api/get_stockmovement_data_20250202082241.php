<?php
require_once '../includes/config.php';
header('Content-Type: application/json');

try {
    // Monthly trends data
    $trends_query = "SELECT 
        DATE_FORMAT(dateencoded, '%Y-%m-%d') as date,
        COUNT(*) as total_movements,
        SUM(CASE WHEN movement_type = 'IN' THEN totalpacks ELSE 0 END) as stock_in,