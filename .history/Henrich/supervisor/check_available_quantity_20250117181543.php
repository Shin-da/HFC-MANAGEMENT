/*************  ✨ Codeium Command 🌟  *************/
<?php
// Connect to database
$conn = mysqli_connect("localhost", "root", "", "dbhenrichfoodcorps");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

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
    echo "Response: " . json_encode(array('availablequantity' => $availableQuantity));
    exit;
    echo json_encode(array('availablequantity' => $availableQuantity));
} else {
    // Return error message as JSON
    echo "Response: " . json_encode(array('error' => 'Failed to retrieve available quantity'));
    exit;
    echo json_encode(array('error' => 'Failed to retrieve available quantity'));
}

// Close database connection
mysqli_close($conn);
?>
/******  f7f7adc4-0fbe-4e71-8b2d-459c65d505c5  *******/