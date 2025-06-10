<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

$batchid = $_POST['batchid'][0];
$productcode = $_POST['productcode'][0];
$quantity = $_POST['quantity'][0];
$weight = $_POST['weight'][0];
$price = $_POST['price'][0];
$dateEncoded = date('Y-m-d H:i:s');

// sum the quantity array 
$stmt = $conn->prepare("INSERT INTO inventoryhistory (batchid, productcode, quantity, weight, price, encodedby, dateencoded) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iiiiiss", $batchid, $productcode, $quantity, $weight, $price, $_SESSION['username'], $dateEncoded);
if (!$stmt->execute()) {
    echo "Error inserting data into inventoryhistory table: " . $stmt->error;
    exit;
} else {
    echo "Data inserted successfully into inventoryhistory table!";
}
?>