<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

$ibdids = isset($_POST['ibdid']) ? $_POST['ibdid'] : array();
$batchids = isset($_POST['batchid']) ? $_POST['batchid'] : array();
$encoders = isset($_POST['encoder']) ? $_POST['encoder'] : array();
$productcodes = isset($_POST['productcode']) ? $_POST['productcode'] : array();
$quantities = isset($_POST['quantity']) ? $_POST['quantity'] : array();
$weights = isset($_POST['weight']) ? $_POST['weight'] : array();
$prices = isset($_POST['price']) ? $_POST['price'] : array();

foreach ($ibdids as $key => $ibdid) {
    $encoder = $encoders[$key] ?? '';
    $productcode = $productcodes[$key] ?? '';
    $quantity = $quantities[$key] ?? 0;
    $weight = $weights[$key] ?? 0;
    $price = $prices[$key] ?? 0;
    $batchid = $batchids[$key] ?? '';

    // Bind the parameters
    $stmt->bind_param("ssssisi", $ibdid, $batchid, $encoder, $productcode, $quantity, $weight, $price);
    $stmt = $conn->prepare("INSERT INTO stockmovement (ibdid, batchid, encoder, productcode, quantity, weight, price) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    if (!$stmt->execute()) {
        echo "Error: " . $stmt->error;
    }
}

echo "Data inserted successfully into stock movement table!";

/*************  ✨ Codeium Command 🌟  *************/
$description = "Encoded " . implode(", ", array_map(function($productcode, $quantity) {
    return "$productcode ($quantity)";
}, $productcodes, $quantities));
$description = ;
/******  a5116bd5-4656-4d8a-8d89-d62e79304001  *******/

require 'add.stockactivitylog.process.php';
?>

