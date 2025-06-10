/*************  ✨ Codeium Command ⭐  *************/
<?php
require '../database/dbconnect.php';

header('Content-Type: application/json');

$query = "SELECT id, username, message, time FROM notifications ORDER BY time DESC";
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
/******  1a3ee58c-d77c-45c2-85ab-d9ce446ff3b2  *******/