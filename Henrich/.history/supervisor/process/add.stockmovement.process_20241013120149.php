<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

// Insert data into inventorybatchdetails table
$ibdids = isset($_POST['ibdid']) ? $_POST['ibdid'] : array();
$batchids = isset($_POST['batchid']) ? $_POST['batchid'] : array();
$encoders = isset($_POST['encoder']) ? $_POST['encoder'] : array();
$productcodes = isset($_POST['productcode']) ? $_POST['productcode'] : array();
$quantities = isset($_POST['quantity']) ? $_POST['quantity'] : array();
$weights = isset($_POST['weight']) ? $_POST['weight'] : array();
$prices = isset($_POST['price']) ? $_POST['price'] : array();


if (empty($ibdids) || empty($batchids) || empty($encoders) || empty($productcodes) || empty($quantities) || empty($weights) || empty($prices)) {
    echo "Error: One or more arrays are empty.";
    exit;
}

$stmt = $conn->prepare("INSERT INTO stockmovement (ibdid, batchid, encoder,  productcode, quantity, weight, price) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssisi", $ibdid, $batchid, $encoder, $productcode, $quantity, $weight, $price);

foreach ($ibdids as $key => $ibdid) {
    $batchid = $batchids[$key] ?? '';
    $encoder = $encoders[$key] ?? '';
    $productcode = $productcodes[$key] ?? '';
    $quantity = $quantities[$key] ?? 0;
    $weight = $weights[$key] ?? 0;
    $price = $prices[$key] ?? 0;

    if (!$stmt->execute()) {
        echo "Error inserting data into inventorybatchdetails table: " . $stmt->error;
        exit;
    } else {
        echo "Data inserted successfully into inventorybatchdetails table!";
    }
}

echo "Data inserted successfully into stock movement table!";

// Call add.stockactivitylog.process.php to insert data into inventoryhistory table
require 'add.stockactivitylog.process.php';
?>

