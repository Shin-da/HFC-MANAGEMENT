<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

// indicator that add.stockmovement.process.php is running
echo "Running add.stockmovement.process.php";

// Retrieve the data from the form

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

$description = "Encoded " . implode(", ", array_map(function($productcode, $quantity) {
    return "$productcode ($quantity)";
}, $productcodes, $quantities));


require 'add.stockactivitylog.process.php';
?>

