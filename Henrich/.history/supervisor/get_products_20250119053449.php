<?php
require_once '../database/dbconnect.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log the incoming request
error_log("Search request received: " . $_GET['search']);

header('Content-Type: application/json');

try {
    // Clean and validate input
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    
    // Simple query to test
    $sql = "SELECT * FROM products WHERE productcode LIKE ? OR productname LIKE ? LIMIT 10";
    $searchTerm = "%{$search}%";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    
    // Log the query for debugging
    while ($row = $result->fetch_assoc()) {
        $products[] = [
            'id' => $row['productcode'],
            'text' => $row['productcode'] . ' - ' . $row['productname']
        ];
    }
    
    error_log("Found products: " . json_encode($products));
    
    echo json_encode([
        'results' => $products,
        'pagination' => ['more' => false]
    ]);

} catch (Exception $e) {
    error_log("Product search error: " . $e->getMessage());
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}
