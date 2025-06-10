/*************  ✨ Codeium Command 🌟  *************/
<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

// Insert data into stockmovement table
$stmt = $conn->prepare("INSERT INTO stockmovement (ibdid, batchid, encoder, productcode, quantity, weight, price) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iisiiii", $ibdid, $batchid, $encoder, $productcode, $quantity, $weight, $price);

$encoder = $_POST['encoder'] ?? '';
$batchid = $_POST['batchid'] ?? '';
$productcodes = $_POST['productcode'] ?? [];
$quantities = $_POST['quantity'] ?? [];
$weights = $_POST['weight'] ?? [];
$prices = $_POST['price'] ?? [];

foreach ($productcodes as $key => $productcode) {
    $ibdid = $_POST['ibdid'][$key] ?? 0;
    $quantity = $quantities[$key] ?? 0;
    $weight = $weights[$key] ?? 0;
    $price = $prices[$key] ?? 0;

    if ($stmt->execute()) {
        echo "Data inserted successfully into stock movement table!";
    } else {
    if (!$stmt->execute()) {
        echo "Error inserting data into stockmovement table: " . $stmt->error;
        exit;
    }
}

echo "Data inserted successfully into stock movement table!";

// Call add.stockactivitylog.process.php to insert data into inventoryhistory table
require 'add.stockactivitylog.process.php';
?>


/******  1463e12e-8e01-4f79-a115-35d9ec2d3b01  *******/