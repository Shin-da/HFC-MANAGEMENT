<?php
require_once '../Henrich/database/dbconnect.php';
require_once '../Henrich/session/session.php';

header('Content-Type: application/json');

$result = mysqli_query($conn, 
    "SELECT COUNT(*) as count 
     FROM notifications 
     WHERE user_id = {$_SESSION['uid']} AND is_read = 0"
);

$count = mysqli_fetch_assoc($result)['count'];
echo json_encode(['count' => $count]);
