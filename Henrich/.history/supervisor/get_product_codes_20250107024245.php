<?php
// Connect to the database
$conn = mysqli_connect("localhost", "username", "password", "database");

// Retrieve the list of product codes from the database
$productCodes = array();
$result = mysqli_query($conn, "SELECT productcode FROM productlist");
while ($row = mysqli_fetch_assoc($result)) {    
    $productCodes[] = $row['productcode'];
}

// Return the list of product codes as JSON
echo json_encode($productCodes);