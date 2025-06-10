/*************  ✨ Codeium Command 🌟  *************/
<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

// Insert data into stockmovement table
$stmt = $conn->prepare("INSERT INTO stockmovement (ibdid, batchid, encoder, productcode, quantity, weight, price) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iisiiii", $ibdid, $batchid, $encoder, $productcode, $quantity, $weight, $price);
// Insert data into inventorybatchdetails table
$encoder = isset($_POST['encoder']) ? $_POST['encoder'] : '';
$batchid = isset($_POST['batchid'][0]) ? $_POST['batchid'][0] : '';
$productcode = isset($_POST['productcode'][0]) ? $_POST['productcode'][0] : '';
$quantity = isset($_POST['quantity'][0]) ? (int)$_POST['quantity'][0] : 0;
$weight = isset($_POST['weight'][0]) ? (int)$_POST['weight'][0] : 0;
$price = isset($_POST['price'][0]) ? (int)$_POST['price'][0] : 0;

$encoder = $_POST['encoder'] ?? '';
$batchid = $_POST['batchid'][0] ?? '';
$ibdids = $_POST['ibdid'] ?? [];
$batchids = $_POST['batchid'] ?? [];
$productcodes = $_POST['productcode'] ?? [];
$quantities = $_POST['quantity'] ?? [];
$weights = $_POST['weight'] ?? [];
$prices = $_POST['price'] ?? [];

foreach ($productcodes as $key => $productcode) {
    $ibdid = $_POST['ibdid'][$key] ?? 0;
$stmt = $conn->prepare("INSERT INTO stockmovement (ibdid, batchid, encoder,  productcode, quantity, weight, price) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iissiii", $ibdid, $batchid, $encoder, $productcode, $quantity, $weight, $price);

foreach ($ibdids as $key => $ibdid) {
    $batchid = $batchids[$key] ?? '';
    $productcode = $productcodes[$key] ?? '';
    $quantity = $quantities[$key] ?? 0;
    $weight = $weights[$key] ?? 0;
    $price = $prices[$key] ?? 0;

    if (!$stmt->execute()) {
        echo "Error inserting data into stockmovement table: " . $stmt->error;
        echo "Error inserting data into inventorybatchdetails table: " . $stmt->error;
        exit;
    }
}

echo "Data inserted successfully into stock movement table!";

// Call add.stockactivitylog.process.php to insert data into inventoryhistory table
require 'add.stockactivitylog.process.php';
?>


/******  b813a99c-d398-4001-9760-5e28d1e5b9a7  *******/