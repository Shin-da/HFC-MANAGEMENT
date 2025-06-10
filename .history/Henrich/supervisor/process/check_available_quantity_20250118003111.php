<?php
require '/xampp/htdocs/HFC/Henrich/database/dbconnect.php';

$productCode = $_GET['productcode'];

$sql = "SELECT availablequantity FROM inventory WHERE productcode = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $productCode);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $availableQuantity = $row['availablequantity'];
    $stockStatus = "Low stock!";
    if ($availableQuantity > 5) {
        $stockStatus = "In stock!";
    }
    echo json_encode(array('availablequantity' => $availableQuantity, 'stockstatus' => $stockStatus));
} else {
    echo json_encode(array('error' => 'Product not found'));
}
?>