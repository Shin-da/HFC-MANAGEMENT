<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

$batchid = $_POST['batchid'][0];
$productcode = $_POST['productcode'][0];
$totalquantity = $_POST[
$totalprice = $_POST['price'][0];
$dateEncoded = date('Y-m-d H:i:s');

// sum the quantity array and insert into inventoryhistory
$sumQuantity = array_sum($_POST['quantity']);
// sum the weight array and insert into inventoryhistory
$sumWeight = array_sum($_POST['weight']);
// sum the price array and insert into inventoryhistory
$sumPrice = array_sum($_POST['price']);

// Call add.inventoryhistory.process.php to insert data into inventoryhistory table
$stmt = $conn->prepare("INSERT INTO inventoryhistory (batchid, totalquantity, totalweight, totalprice, dateencoded) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("iiiiii", $batchid    , $sumQuantity, $sumWeight, $sumPrice, $dateEncoded);
if (!$stmt->execute()) {
    echo "Error inserting data into inventoryhistory table: " . $stmt->error;
    exit;
} else {
    echo "Data inserted successfully into inventoryhistory table!";
}
?>