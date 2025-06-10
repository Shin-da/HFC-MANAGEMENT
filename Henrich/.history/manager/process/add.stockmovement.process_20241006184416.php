<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

// Insert data into inventorybatchdetails table
$ibdids = isset($_POST['ibdid']) ? $_POST['ibdid'] : array();
$batchids = isset($_POST['batchid']) ? $_POST['batchid'] : array();
$productcodes = isset($_POST['productcode']) ? $_POST['productcode'] : array();
$quantities = isset($_POST['quantity']) ? $_POST['quantity'] : array();
$weights = isset($_POST['weight']) ? $_POST['weight'] : array();
$prices = isset($_POST['price']) ? $_POST['price'] : array();

$stmt = $conn->prepare("INSERT INTO stockmovement (ibdid, batchid, productcode, quantity, weight, price) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iiisii", $ibdid, $batchid, $productcode, $quantity, $weight, $price);

foreach ($ibdids as $key => $ibdid) {
    $batchid = $batchids[$key] ?? null;
    $productcode = $productcodes[$key] ?? null;
    $quantity = $quantities[$key] ?? null;
    $weight = $weights[$key] ?? null;
    $price = $prices[$key] ?? null;

    if ($ibdid === null || $batchid === null || $productcode === null || $quantity === null || $weight === null || $price === null) {
        echo "Error: Missing data in POST request";
        exit;
    }

    if (!$stmt->execute()) {
        echo "Error inserting data into stock movement table: " . $stmt->error;
        exit;
    }
}

echo "Data inserted successfully into stock movement table!";

// Call add.stockactivitylog.process.php to insert data into inventoryhistory table
require 'add.stockactivitylog.process.php';
?>
