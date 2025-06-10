/*************  ✨ Codeium Command 🌟  *************/
<?php
ob_start();
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';
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
    $stockStatus = ($availableQuantity <= 5) ? 'Low stock!' : 'In stock!';
    // Return available quantity and stock status as JSON

    // Return available quantity as JSON
    header('Content-Type: application/json');
    echo json_encode(array(
        'availablequantity' => $availableQuantity,
        'stockstatus' => $stockStatus
    ));
    echo json_encode($availableQuantity);
} else {
    // Return error message as JSON
    header('Content-Type: application/json');
    echo json_encode(array(
        'availablequantity' => 'Not available',
        'stockstatus' => 'Not available'
    ));
    echo json_encode(array('availablequantity' => 'Not available'));
}
ob_end_flush();
/******  0b5335a5-e5f2-46ba-a9d9-ac6d4c371b0b  *******/