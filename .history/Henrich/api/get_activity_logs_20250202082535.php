<?php
require_once '../includes/config.php';

$draw = $_POST['draw'];
$start = $_POST['start'];
$length = $_POST['length'];
$search = $_POST['search']['value'];

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

$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$query = "SELECT SQL_CALC_FOUND_ROWS 
            sm.*,
            p.productname
          FROM stockmovement sm
          LEFT JOIN inventory p ON sm.productcode = p.productcode
          $whereClause
          ORDER BY sm.dateencoded DESC
          LIMIT ?, ?";

$stmt = $conn->prepare($query);
$types = str_repeat('s', count($params)) . 'ii';
$params = array_merge($params, [$start, $length]);
$stmt->bind_param($types, ...$params);
$stmt->execute();

$result = $stmt->get_result();
$data = $result->fetch_all(MYSQLI_ASSOC);
$total = $conn->query("SELECT FOUND_ROWS()")->fetch_row()[0];

echo json_encode([
    'draw' => $draw,
    'recordsTotal' => $total,
    'recordsFiltered' => $total,
    'data' => $data
]);
