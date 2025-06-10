<?php
require '/xampp/htdocs/Henrich/database/dbconnect.php';

$productCode = $_GET['productcode'];

$sql = "SELECT availablequantity FROM inventory WHERE productcode = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $productCode);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $availableQuantity = $row['availablequantity'];
    echo json_encode(array('availablequantity' => $availableQuantity));
} else {
    echo json_encode(array('error' => 'Product not found'));
}