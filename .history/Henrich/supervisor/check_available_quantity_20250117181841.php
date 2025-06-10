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
    echo json_encode(array('availablequantity' => $availableQuantity));
    exit;
} else {
    // Return error message as JSON
    echo  json_encode(array('error' => 'Failed to retrieve available quantity'));
    exit;
}

// Close database connection
mysqli_close($conn);
?>
