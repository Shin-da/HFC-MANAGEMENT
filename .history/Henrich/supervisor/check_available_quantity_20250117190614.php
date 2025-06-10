/*************  ✨ Codeium Command 🌟  *************/
<?php
require '../database/dbconnect.php';

// Get product code from URL parameter
$productCode = $_GET['productcode'];

// Query database for available quantity
$sql = "SELECT availablequantity FROM inventory WHERE productcode = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $productCode);
$stmt->execute();
$result = $stmt->get_result();
$sql = "SELECT availablequantity FROM inventory WHERE productcode = '$productCode'";
$result = mysqli_query($conn, $sql);

// Check if query was successful
if ($result->num_rows > 0) {
if ($result) {
    // Get available quantity from query result
    $row = $result->fetch_assoc();
    $row = mysqli_fetch_assoc($result);
    $availableQuantity = $row['availablequantity'];

    // Return available quantity as JSON
    header('Content-Type: application/json');
    echo json_encode(array('availablequantity' => $availableQuantity));
    exit;
} else {
    // Return error message as JSON
    header('Content-Type: application/json');
    echo json_encode(array('availablequantity' => 'Not available'));
    echo json_encode(array('error' => 'Failed to retrieve available quantity'));
    exit;
}

?>
/******  f8066df9-e66c-43b4-a71c-3b7fd554be77  *******/