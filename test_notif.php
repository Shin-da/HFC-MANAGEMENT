<?php
require_once 'includes/config.php';
require_once 'includes/session.php';

// 1. Check session
echo "<h2>Session Data:</h2>";
var_dump($_SESSION);

// 2. Check database connection
echo "<h2>Database Connection:</h2>";
var_dump($conn);

// 3. Create a test notification using user_id
$user_id = $_SESSION['user_id']; // Use user_id consistently
$message = "Test notification " . date('Y-m-d H:i:s');
$sql = "INSERT INTO notifications (user_id, message, is_read, created_at) 
        VALUES (?, ?, 0, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $user_id, $message);

if ($stmt->execute()) {
    echo "Test notification created successfully!<br>";
    // Show current notifications
    $query = "SELECT * FROM notifications WHERE user_id = ? AND is_read = 0";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo "<h3>Current Notifications:</h3>";
    while ($row = $result->fetch_assoc()) {
        echo "<pre>";
        print_r($row);
        echo "</pre>";
    }
} else {
    echo "Error creating notification: " . $stmt->error;
}
?>
