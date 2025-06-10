<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

$encoder = $_POST['encoder'] ?? '';
$batchid = $_POST['batchid'] ?? '';
$productcodes = $_POST['productcode'] ?? [];
$quantities = $_POST['quantity'] ?? [];
$weights = $_POST['weight'] ?? [];
$prices = $_POST['price'] ?? [];

$stmt = $conn->prepare("INSERT INTO stockmovement (ibdid, batchid, encoder, productcode, quantity, weight, price) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iisiiii", $ibdid, $batchid, $encoder, $productcode, $quantity, $weight, $price);

foreach ($productcodes as $key => $productcode) {
    $ibdid = (int)$_POST['ibdid'][$key];
    $quantity = (int)$quantities[$key];
    $weight = (int)$weights[$key];
    $price = (int)$prices[$key];

    $stmt->execute();
    if ($stmt->errno) {
        echo "Error inserting data into stockmovement table: " . $stmt->error;
        exit;
    }
}

// Call add.stockactivitylog.process.php to insert data into inventoryhistory table
require 'add.stockactivitylog.process.php';
?>

