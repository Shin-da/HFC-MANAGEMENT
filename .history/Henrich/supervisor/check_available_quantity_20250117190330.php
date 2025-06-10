<?php
/*************  ✨ Codeium Command ⭐  *************/
require '../database/dbconnect.php';
/******  90f55c03-6b96-4796-8e7c-3ebe7f073bf3  *******/

// Get product code from URL parameter
$productCode = $_GET['productcode'];

// Query database for available quantity
$sql = "SELECT availablequantity FROM productlist WHERE productcode = '$productCode'";
$result = mysqli_query($conn, $sql);

// Check if query was successful
if ($result) {
    // Get available quantity from query result
    $row = mysqli_fetch_assoc($result);
    $availableQuantity = $row['availablequantity'];

    // Return available quantity as JSON
    header('Content-Type: application/json');
    echo json_encode(array('availablequantity' => $availableQuantity));
    exit;
} else {
    // Return error message as JSON
    header('Content-Type: application/json');
    echo json_encode(array('error' => 'Failed to retrieve available quantity'));
    exit;
}

?>