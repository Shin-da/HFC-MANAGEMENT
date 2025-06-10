<?php
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$hostname = "localhost";
$username = "root";
$password = "";
$dbname = "dbHenrichFoodCorps";
$port = 3306;

try {
    // Create connection
    $conn = new mysqli($hostname, $username, $password, $dbname, $port);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Test if 'product' table exists
    $result = $conn->query("SHOW TABLES LIKE 'product'");
    $productTableExists = $result->num_rows > 0;
    
    // Test if 'inventory' table exists
    $result = $conn->query("SHOW TABLES LIKE 'inventory'");
    $inventoryTableExists = $result->num_rows > 0;
    
    // Test product table structure if it exists
    $productColumns = [];
    if ($productTableExists) {
        $result = $conn->query("DESCRIBE product");
        while ($row = $result->fetch_assoc()) {
            $productColumns[] = $row;
        }
    }
    
    // Test inventory table structure if it exists
    $inventoryColumns = [];
    if ($inventoryTableExists) {
        $result = $conn->query("DESCRIBE inventory");
        while ($row = $result->fetch_assoc()) {
            $inventoryColumns[] = $row;
        }
    }
    
    // Return success
    echo json_encode([
        'success' => true,
        'message' => 'Database connection established',
        'tables' => [
            'product' => [
                'exists' => $productTableExists,
                'columns' => $productColumns
            ],
            'inventory' => [
                'exists' => $inventoryTableExists,
                'columns' => $inventoryColumns
            ]
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 