<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

// Insert data into inventorybatchdetails table
$batchid = isset($_POST['batchid'][0]) ? $_POST['batchid

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
    }
}

echo "Data inserted successfully into stock movement table!";

// Call add.stockactivitylog.process.php to insert data into inventoryhistory table
require 'add.stockactivitylog.process.php';
?>
