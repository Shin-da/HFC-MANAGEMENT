<?php
require '../database/dbconnect.php';
header('Content-Type: application/json');
// Get product code from URL parameter
$productCode = $_GET['productcode'];

// Query database for available quantity
$sql = "SELECT availablequantity FROM inventory WHERE productcode = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $productCode);
$stmt->execute();
$result = $stmt->get_result();

// Check if query was successful
if ($result->num_rows > 0) {
    // Get available quantity from query result
    $row = $result->fetch_assoc();
    $availableQuantity = $row['availablequantity'];

    // Return available quantity as JSON
    header('Content-Type: application/json');
    echo json_encode(array('availablequantity' => $availableQuantity));
} else {
    // Return error message as JSON
    header('Content-Type: application/json');
    echo json_encode(array('availablequantity' => 'Not available'));
}
