<?php
// Database credentials
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'dbhe';

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8mb4");
?>