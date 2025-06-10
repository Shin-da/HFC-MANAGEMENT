<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

$batchid = $_POST['batchid'][0];
$totalquantity = $_POST['quantity'][0];
$totalweight = $_POST['weight'][0];
$totalprice = $_POST['price'][0];
$dateencoded = date('Y-m-d H:i:s');

$stmt = $conn->prepare("INSERT INTO inventoryhistory (batchid, totalquantity, totalweight, totalprice, dateencoded) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("iiiii", $batchid, $totalquantity, $totalweight, $totalprice, $dateencoded);
if (!$stmt->execute()) {
    echo "Error inserting data into inventoryhistory table: " . $stmt->error;
    exit;
} else {
    echo "Data inserted successfully into inventoryhistory table!";
}
?>