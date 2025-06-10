<?php
/**
 * API Test File
 * This file is used to test API connectivity and verify database access
 */

// Initialize session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Allow errors to be displayed for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Test output
$response = [
    'success' => true,
    'timestamp' => date('Y-m-d H:i:s'),
    'message' => 'API Test Results',
    'session' => [],
    'database' => [
        'connected' => false,
        'message' => 'Not tested'
    ],
    'api_endpoints' => []
];

// Get session info
if (isset($_SESSION['user_id'])) {
    $response['session'] = [
        'user_id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'] ?? 'Unknown',
        'role' => $_SESSION['role'] ?? 'Unknown',
        'logged_in' => true
    ];
} else {
    $response['session'] = [
        'logged_in' => false,
        'message' => 'No active session found'
    ];
}

// Test database connection
try {
    require_once '../../../config/database.php';
    
    // Try to create database connection
    $db = new Database();
    $conn = $db->getConnection();
    
    if ($conn) {
        $response['database']['connected'] = true;
        $response['database']['message'] = 'Successfully connected to database';
        
        // Try a simple query to verify database is working
        $testQuery = "SELECT 1 AS test";
        $statement = $conn->prepare($testQuery);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        
        if ($result && isset($result['test']) && $result['test'] == 1) {
            $response['database']['query_result'] = 'Connection established and test query successful';
            $response['success'] = true;
        } else {
            $response['database']['query_result'] = 'Connection established but test query failed';
            $response['success'] = false;
        }
    }
} catch (Exception $e) {
    $response['database']['message'] = 'Database connection failed: ' . $e->getMessage();
    $response['success'] = false;
}

// Check API endpoints
$apiEndpoints = [
    'department-statistics.php' => __DIR__ . '/department-statistics.php',
    'request-trends.php' => __DIR__ . '/request-trends.php',
    'request-types.php' => __DIR__ . '/request-types.php',
    'recent-requests.php' => __DIR__ . '/recent-requests.php',
    'employee-performance.php' => __DIR__ . '/employee-performance.php'
];

foreach ($apiEndpoints as $endpoint => $path) {
    $endpoint_info = [
        'name' => $endpoint,
        'path' => $path,
        'exists' => file_exists($path),
        'readable' => is_readable($path),
        'size' => file_exists($path) ? filesize($path) . ' bytes' : 'N/A',
        'last_modified' => file_exists($path) ? date('Y-m-d H:i:s', filemtime($path)) : 'N/A'
    ];
    
    $response['api_endpoints'][] = $endpoint_info;
}

// Add server info
$response['server'] = [
    'php_version' => PHP_VERSION,
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown',
    'script_filename' => $_SERVER['SCRIPT_FILENAME'] ?? 'Unknown',
    'server_name' => $_SERVER['SERVER_NAME'] ?? 'Unknown',
    'request_uri' => $_SERVER['REQUEST_URI'] ?? 'Unknown',
    'api_base_directory' => __DIR__
];

// Set content type to JSON
header('Content-Type: application/json');

// Return information
echo json_encode($response, JSON_PRETTY_PRINT);
?> 