<?php
require_once '../includes/config.php';

$draw = $_POST['draw'];
$start = $_POST['start'];
$length = $_POST['length'];
$search = $_POST['search']['value'];
$dateFilter = $_POST['dateFilter'] ?? '';
$typeFilter = $_POST['typeFilter'] ?? '';

$where = [];
$params = [];

if (!empty($search)) {
    $where[] = "(productcode LIKE ? OR productname LIKE ? OR batchid LIKE ?)";
    $params = array_merge($params, ["%$search%", "%$search%", "%$search%"]);
}

if (!empty($dateFilter)) {
    $where[] = "DATE(dateencoded) = ?";
    $params[] = $dateFilter;
}

if (!empty($typeFilter)) {
    $where[] = "movement_type = ?";
    $params[] = $typeFilter;
}

$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$query = "SELECT SQL_CALC_FOUND_ROWS *
          FROM stockmovement
          $whereClause
          ORDER BY dateencoded DESC
          LIMIT ?, ?";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $types = str_repeat('s', count($params) + 2);
    $params = array_merge($params, [$start, $length]);
    $stmt->bind_param($types, ...$params);
} else {
    $stmt->bind_param('ii', $start, $length);
}

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
