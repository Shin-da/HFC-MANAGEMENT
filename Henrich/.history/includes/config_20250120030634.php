<?php
// Start output buffering
ob_start();

$host = 'localhost';
$username = "root";
$password = "";
$dbname = "dbhenrichfoodcorps";

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset to ensure proper encoding
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    error_log("Database connection error: " . $e->getMessage());
    die(json_encode([
        'error' => true,
        'message' => "Database connection failed"
    ]));
}

// Clear any output that might have been generated
ob_clean();
?>