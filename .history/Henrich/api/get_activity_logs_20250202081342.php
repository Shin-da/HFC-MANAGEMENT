<?php
require_once '../includes/config.php';

$draw = $_POST['draw'];
$start = $_POST['start'];
$length = $_POST['length'];
$search = $_POST['search']['value'];
$order_column = $_POST['order'][0]['column'];
$order_dir = $_POST['order'][0]['dir'];

$columns = ['dateencoded', 'productcode', 'productname', 'movement_type', 'totalpacks', 'encoder'];
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
          $where_clause
          ORDER BY $order_by $order_dir
          LIMIT ?, ?";

$params = array_merge($params, [$start, $length]);

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_all(MYSQLI_ASSOC);

$total_query = "SELECT FOUND_ROWS()";
$total_result = $conn->query($total_query);
$total_records = $total_result->fetch_row()[0];

echo json_encode([
    'draw' => $draw,
    'recordsTotal' => $total_records,
    'recordsFiltered' => $total_records,
    'data' => $data
]);
