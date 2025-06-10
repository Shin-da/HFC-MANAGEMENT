<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

$batchid = $_POST['batchid'];
$quantity = $_POST['quantity'];
$weight = $_POST['weight'];
$price = $_POST['price'];
$dateencoded = date('Y-m-d');

// sum the quantity, weight, and price of each product in the batch
$totalboxes = 0;
$totalweight = 0;
$totalprice = 0;


print "$totalquantity, $totalweight, $totalprice";

$stmt = $conn->prepare("INSERT INTO inventoryhistory (batchid, totalboxes, totalweight, totalprice, dateencoded) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("iiiii", $batchid, $totalquantity, $totalweight, $totalprice, $dateencoded);
if (!$stmt->execute()) {
    echo "Error inserting data into inventoryhistory table: " . $stmt->error;
    exit;
} else {
    echo "Data inserted successfully into inventoryhistory table!";
}
?>