<?php
// Database connection configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert test notifications
$sql = "INSERT INTO notifications (message, type, created_at, status) VALUES 
    ('Test notification 1', 'info', NOW(), 'unread'),
    ('Test notification 2', 'warning', NOW(), 'unread'),
    ('Test notification 3', 'success', NOW(), 'unread')";

if ($conn->query($sql) === TRUE) {
    echo "Test notifications inserted successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>