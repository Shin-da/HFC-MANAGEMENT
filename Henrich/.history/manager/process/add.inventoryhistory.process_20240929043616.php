<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

$batchid = $_POST['batchid'][0];
$quantity = $_POST['quantity'][0];
$weight = $_POST['weight'][0];
$price = $_POST['price'][0];
$dateEncoded = date('Y-m-d H:i:s');

// sum the quantity array and insert into inventoryhistory
$totalquantity = $quantity;
// sum the weight array and insert into inventoryhistory
$totalweight = $weight;
// sum the price array and insert into inventoryhistory
$totalprice = $price;

$stmt = $conn->prepare("INSERT INTO inventoryhistory (batchid,totalquantity, totalweight, totalprice, dateencoded) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("iiiiiis", $batchid, $totalquantity, $totalweight, $totalprice, $dateEncoded);
if (!$stmt->execute()) {
    echo "Error inserting data into inventoryhistory table: " . $stmt->error;
    exit;
} else {
    echo "Data inserted successfully into inventoryhistory table!";
}
?>