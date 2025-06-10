<?php
// Prevent any output
ob_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbhenrichfoodcorps";
$port = 3306;
$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die(json_encode(['error' => true, 'message' => "Connection failed: " . $conn->connect_error]));
}

// Clean any output that might have been generated
ob_clean();
?>