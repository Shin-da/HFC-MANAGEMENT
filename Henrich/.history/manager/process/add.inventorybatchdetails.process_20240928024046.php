<!-- add.inventorybatchdetails.process -->
<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

$ibdids = $_POST['ibdid']; 
$batchids = $_POST['batchId'];
$productCodes = $_POST['productCode'];
$quantities = $_POST['quantity'] ;
$weights = $_POST['weight'];
$prices = $_POST['price'];

// Execute the insert statement for each batchid
foreach ($batchids as $batchid) {
    $stmt->execute();
}

// Insert into inventorybatchdetails
$stmt = $conn->prepare("INSERT INTO inventorybatchdetails (ibdid, batchid, productcode, quantity, weight, price) VALUES (?, ?, ?, ?, ?, ?)");

foreach ($batchids as $key => $batchid) {
    $productCode = $productCodes[$key];
    $quantity = $quantities[$key];
    $weight = $weights[$key];
    $price = $prices[$key];

    $stmt->bind_param("isiiid", $ibdids[$key], $batchid, $productCode, $quantity, $weight, $price);

    if (!$stmt->execute()) {
        echo "Error inserting data: " . $stmt->error;
        exit();
    }
}

$stmt->close();
$conn->close();