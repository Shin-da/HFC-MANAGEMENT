<?php
require '../database/dbconnect.php';

header('Content-Type: application/json');

$query = "SELECT nid, , message, time FROM notifications ORDER BY time DESC";
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
