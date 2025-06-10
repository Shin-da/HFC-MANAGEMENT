/*************  ✨ Codeium Command 🌟  *************/
<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

$ibdids = $_POST['ibdid'] ?? [];
$batchids = $_POST['batchid'] ?? [];
$encoders = $_POST['encoder'] ?? [];
$productcodes = $_POST['productcode'] ?? [];
$quantities = $_POST['quantity'] ?? [];
$weights = $_POST['weight'] ?? [];
$prices = $_POST['price'] ?? [];

$stmt = $conn->prepare("INSERT INTO stockmovement (ibdid, batchid, encoder, productcode, quantity, weight, price) VALUES (?, ?, ?, ?, ?, ?, ?)");

foreach ($ibdids as $key => $ibdid) {
    $encoder = $encoders[$key] ?? '';
    $productcode = $productcodes[$key] ?? '';
    $quantity = $quantities[$key] ?? 0;
    $weight = $weights[$key] ?? 0;
    $price = $prices[$key] ?? 0;
    $batchid = $batchids[$key] ?? '';

    // Bind the parameters
    $stmt->bind_param("ssssisi", $ibdid, $batchid, $encoder, $productcode, $quantity, $weight, $price);
    
    if (!$stmt->execute()) {
        echo "Error: " . $stmt->error;
    }
}

echo "Data inserted successfully into stock movement table!";


/******  ee828962-39cc-48b0-80f7-d5dab62167fe  *******/