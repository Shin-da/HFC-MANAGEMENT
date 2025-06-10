<!-- add.inventorybatchdetails.process -->
<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

$batchids = $_POST['batchid'];
$productCodes = $_POST['productCode'];
$quantities = $_POST['quantity'];
$weights = $_POST['weight'];
$prices = $_POST['price'];

$stmt = $conn->prepare( "INSERT INTO inventorybatchdetails where batchid=? and productcode=? (batchid, productcode, quantity, weight, price) VALUES (?, ?, ?, ?, ?)");

}



$stmt->close();
$conn->close();
