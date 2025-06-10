<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

// Insert data into inventorybatchdetails table
$ibdids = $_POST['ibdid'];
$batchids = $_POST['batchid'];
$productcodes = $_POST['productcode'];
$quantities = $_POST['quantity'];
$weights = $_POST['weight'];
$prices = $_POST['price'];


$stmt = $conn->prepare("INSERT INTO stockmovement (ibdid, batchid, productcode, quantity, weight, price) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iiisii", $ibdid, $batchid, $productcode, $quantity, $weight, $price);

foreach ($ibdids as $key => $ibdid) {
    $batchid = $batchids[$key];
    $productcode = $productcodes[$key];
    $quantity = $quantities[$key];
    $weight = $weights[$key];
    $price = $prices[$key];

    if (!$stmt->execute()) {
        echo "Error inserting data into inventorybatchdetails table: " . $stmt->error;
        exit;
    }
}

echo "Data inserted successfully into stock movement table!";

// Call add.stoc.process.php to insert data into inventoryhistory table
require 'add.inventoryhistory.process.php';
?>