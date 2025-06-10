<?php
require_once '../database/dbconnect.php';

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    error_log("Search term: " . $search);
    
    $sql = "SELECT productcode, productname 
            FROM products 
            WHERE (productcode LIKE ? OR productname LIKE ?)
            AND productstatus = 'Active'
            LIMIT 10";
            
    $stmt = $conn->prepare($sql);
    $searchTerm = "%{$search}%";
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    
    if (!$stmt->execute()) {
        throw new Exception("Query failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $products = [];
    
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
