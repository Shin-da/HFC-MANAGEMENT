<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

$batchid = $_POST['batchid'];
$quantity = $_POST['quantity'];
$weight = $_POST['weight'];
$price = $_POST['price'];
$dateencoded = $_POST['dateencoded'];

$totalquantity = 
$totalweight = $weight;
$totalprice = $price;

$stmt = $conn->prepare("INSERT INTO inventoryhistory (batchid, totalquantity, totalweight, totalprice, dateencoded) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("iiiii", $batchid, $totalquantity, $totalweight, $totalprice, $dateencoded);
if (!$stmt->execute()) {
    echo "Error inserting data into inventoryhistory table: " . $stmt->error;
    exit;
} else {
    echo "Data inserted successfully into inventoryhistory table!";
}
?>