/*************  ✨ Codeium Command 🌟  *************/
<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

$encoder = $_POST['encoder'] ?? '';
$batchid = $_POST['batchid'] ?? '';
$productcodes = $_POST['productcode'] ?? [];
$quantities = $_POST['quantity'] ?? [];
$weights = $_POST['weight'] ?? [];
$prices = $_POST['price'] ?? [];
$ibdids = $_POST['ibdid'] ?? []; // Initialize $ibdids to an empty array if it doesn't exist

$stmt = $conn->prepare("INSERT INTO stockmovement (ibdid, batchid, encoder, productcode, quantity, weight, price) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iisiiii", $ibdid, $batchid, $encoder, $productcode, $quantity, $weight, $price);

foreach ($productcodes as $key => $productcode) {
    $ibdid = isset($ibdids[$key]) ? (int)$ibdids[$key] : 0;
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


/******  2dc82775-4bd1-4457-ac12-c01680925299  *******/