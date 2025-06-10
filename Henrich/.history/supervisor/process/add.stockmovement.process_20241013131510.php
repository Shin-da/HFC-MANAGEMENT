<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

// indicator that add.stockmovement.process.php is running
echo "Running add.stockmovement.process.php";
// Retrieve the data from the form
$ibdids = $_POST['ibdid'] ?? [];
$batchids = $_POST['batchid'] ?? [];
$encoders = $_POST['encoder'] ?? [];
$productcodes = $_POST['productcode'] ?? [];
$quantities = $_POST['quantity'] ?? [];
$weights = $_POST['weight'] ?? [];
$prices = $_POST['price'] ?? [];
$dateencoded = $_POST['dateencoded'] ?? [];

// Insert the data into the database
foreach ($ibdids as $key => $ibdid) {
    $batchid = $batchids[$key] ?? '';
    $encoder = $encoders[$key] ?? '';
    $productcode = $productcodes[$key] ?? '';
    $quantity = $quantities[$key] ?? 0;
    $weight = $weights[$key] ?? 0;
    $price = $prices[$key] ?? 0;
    $dateEncoded = $dateencoded[$key] ?? '';

    // Bind the parameters
    $stmt->bind_param("ssssisis", $ibdid, $batchid, $encoder, $productcode, $quantity, $weight, $price, $dateEncoded);
    $stmt = $conn->prepare("INSERT INTO stockmovement (ibdid, batchid, encoder, productcode, quantity, weight, price, dateEncoded) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    if (!$stmt->execute()) {
        echo "Error: " . $stmt->error;
    }
}
echo "Data inserted successfully into stock movement table!";

$description = "Encoded " . implode(", ", array_map(function($productcode, $quantity) {
    return "$productcode ($quantity)";
}, $productcodes, $quantities));


require 'add.stockactivitylog.process.php';
?>

