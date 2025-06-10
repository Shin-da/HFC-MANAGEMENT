/*************  ✨ Codeium Command 🌟  *************/
<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

// Insert data into inventorybatchdetails table
$ibdids = isset($_POST['ibdid']) ? $_POST['ibdid'] : array();
$batchids = isset($_POST['batchid']) ? $_POST['batchid'] : array();
$productcodes = isset($_POST['productcode']) ? $_POST['productcode'] : array();
$quantities = isset($_POST['quantity']) ? $_POST['quantity'] : array();
$weights = isset($_POST['weight']) ? $_POST['weight'] : array();
$prices = isset($_POST['price']) ? $_POST['price'] : array();
$ibdids = $_POST['ibdid'];
$batchids = $_POST['batchid'];
$productcodes = $_POST['productcode'];
$quantities = $_POST['quantity'];
$weights = $_POST['weight'];
$prices = $_POST['price'];


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
    $batchid = $batchids[$key];
    $productcode = $productcodes[$key];
    $quantity = $quantities[$key];
    $weight = $weights[$key];
    $price = $prices[$key];

    if (!$stmt->execute()) {
        echo "Error inserting data into stock movement table: " . $stmt->error;
        echo "Error inserting data into inventorybatchdetails table: " . $stmt->error;
        exit;
    }
}

echo "Data inserted successfully into stock movement table!";

// Call add.stockactivitylog.process.php to insert data into inventoryhistory table
require 'add.stockactivitylog.process.php';
?>
/******  eb6b108b-9d48-409f-b9e0-ccbe3c8e2c0c  *******/