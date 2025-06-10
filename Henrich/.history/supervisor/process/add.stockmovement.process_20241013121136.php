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

if (count($ibdids) === 0 || count($batchids) === 0 || count($encoders) === 0 || count($productcodes) === 0 || count($quantities) === 0 || count($weights) === 0 || count($prices) === 0) {
    echo "<script>alert('One or more fields are empty')</script>";
}
// Loop through each array and insert data into database
foreach ($ibdids as $key => $ibdid) {
    $encoder = $encoders[$key] ?? '';
    $productcode = $productcodes[$key] ?? '';
    $quantity = $quantities[$key] ?? 0;
    $weight = $weights[$key] ?? 0;
    $price = $prices[$key] ?? 0;
    $batchid = $batchids[$key] ?? '';

    // Insert data into database
    $stmt = $conn->prepare("INSERT INTO stockmovement (ibdid, batchid, encoder, productcode, quantity, weight, price) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssisi", $ibdid, $batchid, $encoder, $productcode, $quantity, $weight, $price);
    $stmt->execute();
}

// Call add.stockactivitylog.process.php to insert data into stockactivitylog table
echo "Data inserted successfully into stock movement table!";

// Call add.stockactivitylog.process.php to insert data into inventoryhistory table
require 'add.stockactivitylog.process.php';
?>

