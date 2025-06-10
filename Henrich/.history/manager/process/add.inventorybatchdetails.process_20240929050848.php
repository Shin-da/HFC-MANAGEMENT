<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

$ibdid = $_POST['ibdid'][0];
$batchid = $_POST['batchid'][0];
$productcode = $_POST['productcode'][0];
$quantity = $_POST['quantity'][0];
$weight = $_POST['weight'][0];
$price = $_POST['price'][0];

$stmt = $conn->prepare("INSERT INTO inventorybatchdetails (ibdid, batchid, productcode, quantity, weight, price) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iiiiii", $ibdid, $batchid, $productcode, $quantity, $weight, $price);
if (!$stmt->execute()) {
    echo "Error inserting data into inventorybatchdetails table: " . $stmt->error;
    exit;
} else {
    echo "Data inserted successfully into inventorybatchdetails table!";
}
// pass the productcode to add.inventoryhistory.process.php
$productcode = $_POST['productcode'][0];
echo $productcode;
// Call add.inventoryhistory.process.php to insert data into inventoryhistory table

// 
?>