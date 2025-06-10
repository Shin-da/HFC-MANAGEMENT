<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database credentials
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'dbhenrichfoodcorps';

// Create connection with error handling
try {
    $conn = new mysqli($host, $username, $password, $database);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Set charset
    $conn->set_charset("utf8mb4");

} catch (Exception $e) {
    error_log("Database connection error: " . $e->getMessage());
    // Initialize $conn as null so we can check for it
    $conn = null;
}

// Define base path
define('BASE_PATH', dirname(__DIR__));
?>