<?php
require '../database/dbconnect.php';

header('Content-Type: application/json');

$page = $_GET['page'];
$limit = $_GET['limit'];

$offset = ($page - 1) * $limit;

$query = "SELECT nid, username, role, activity, description, date FROM notification ORDER BY time DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($query);

$notifications = array();

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
  }
}

echo json_encode($notifications);

$conn->close();
?>