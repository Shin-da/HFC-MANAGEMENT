<?php
require_once '../includes/config.php';

$draw = $_POST['draw'];
$start = $_POST['start'];
$length = $_POST['length'];
$search = $_POST['search']['value'];

// Build query
$query = "SELECT 
    sm.dateencoded,
    sm.productcode,
    sm.productname,
    sm.movement_type,
    sm.totalpacks,
    sm.encoder
FROM stockmovement sm
WHERE 1=1";

$params = [];

if (!empty($search)) {
    $query .= " AND (sm.productcode LIKE ? OR sm.productname LIKE ? OR sm.encoder LIKE ?)";
    $params = array_merge($params, ["%$search%", "%$search%", "%$search%"]);
}

if (!empty($_POST['startDate'])) {
    $query .= " AND DATE(sm.dateencoded) >= ?";
    $params[] = $_POST['startDate'];
}

if (!empty($_POST['endDate'])) {
    $query .= " AND DATE(sm.dateencoded) <= ?";
    $params[] = $_POST['endDate'];
}

if (!empty($_POST['activityType'])) {
    $query .= " AND sm.movement_type = ?";
    $params[] = $_POST['activityType'];
}

// Get total records count
$countQuery = str_replace("SELECT sm.dateencoded, sm.productcode", "SELECT COUNT(*)", $query);
$stmt = $conn->prepare($countQuery);
if (!empty($params)) {
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
}
$stmt->execute();
$total = $stmt->get_result()->fetch_row()[0];

// Add sorting and limit
$query .= " ORDER BY sm.dateencoded DESC LIMIT ?, ?";
$params = array_merge($params, [$start, $length]);

// Execute main query
$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode([
    'draw' => $draw,
    'recordsTotal' => $total,
    'recordsFiltered' => $total,
    'data' => $data
]);
