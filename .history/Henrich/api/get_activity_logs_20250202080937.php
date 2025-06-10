<?php
require_once '../includes/config.php';

$draw = $_POST['draw'];
$start = $_POST['start'];
$length = $_POST['length'];
$search = $_POST['search']['value'];
$order_column = $_POST['order'][0]['column'];
$order_dir = $_POST['order'][0]['dir'];

$columns = ['dateencoded', 'productcode', 'productname', 'movement_type', 'totalpieces', 'encoder'];
$order_by = $columns[$order_column];

$where = [];
$params = [];

if (!empty($search)) {
    $where[] = "(productcode LIKE ? OR productname LIKE ? OR encoder LIKE ?)";
    $params = array_merge($params, ["%$search%", "%$search%", "%$search%"]);
}

if (!empty($_POST['startDate'])) {
    $where[] = "DATE(dateencoded) >= ?";
    $params[] = $_POST['startDate'];
}

if (!empty($_POST['endDate'])) {
    $where[] = "DATE(dateencoded) <= ?";
    $params[] = $_POST['endDate'];
}

if (!empty($_POST['activityType'])) {
    $where[] = "movement_type = ?";
    $params[] = $_POST['activityType'];
}

$where_clause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$query = "SELECT SQL_CALC_FOUND_ROWS *
          FROM stockmovement