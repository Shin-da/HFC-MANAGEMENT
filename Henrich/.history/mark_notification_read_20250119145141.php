<?php
require_once '../Henrich/database/dbconnect.php';
require_once '../Henrich/session/session.php';

header('Content-Type: application/json');

if (isset($_POST['id'])) {
    $notif_id = (int)$_POST['id'];
    $success = mysqli_query($conn, 
        "UPDATE notifications 
         SET is_read = 1 
         WHERE id = $notif_id AND user_id = {$_SESSION['uid']}"
    );
    
    echo json_encode(['success' => $success]);
} else {
    echo json_encode(['success' => false]);
}
