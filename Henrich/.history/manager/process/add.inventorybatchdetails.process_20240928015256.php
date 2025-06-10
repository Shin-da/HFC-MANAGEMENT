<!-- add.inventorybatchdetails.process -->
<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

$batchids = $_POST['batchid'];
$productCodes = $_POST['productCode'];
$quantities = $_POST['quantity'];
$weights = $_POST['weight'];
$prices = $_POST['price'];

$stmt = $conn->prepare( "INSERT INTO inventorybatchdetails where batchid=? and productcode=? (batchid, productcode, quantity, weight, price) VALUES (?, ?, ?, ?, ?)");

foreach ($batchids as $key => $batchid) {
    $productCode = $productCodes[$key];
    $quantity = $quantities[$key];
    $weight = $weights[$key];
    $price = $prices[$key];

    $stmt->bind_param("ssssss", $batchid, $productCode, $quantity, $weight, $price);
    if (!$stmt->execute()) {
}



$stmt->close();
$conn->close();
