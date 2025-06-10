<?php
$productCode = $_GET["productcode"];

// Assuming you have a database connection and a function to retrieve product data
function retrieveProductData($productCode) {
    global $conn;
    $sql = "SELECT productname, productweight FROM productlist WHERE productcode = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $productCode);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    return $row;
}

$productData = retrieveProductData($productCode);

// Set the response content type to JSON
header('Content-Type: application/json');

// Output the JSON response
echo json_encode($productData);
?>