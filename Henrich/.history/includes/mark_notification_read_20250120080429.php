<?php
session_start();
include('../../includes/config.php');

if (isset($_POST['notification_id'])) {
    $notificationId = $_POST['notification_id'];
    
    // Update the notification status to 'read'
    $query = "UPDATE notifications SET status = 'read' WHERE notification_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $notificationId);
    
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
    
    $stmt->close();
    $conn->close();
} else {
    echo "No notification ID provided";
}
?>