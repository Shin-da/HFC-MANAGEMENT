<!-- add.inventorybatchdetails.process -->
<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

$ibdids = $_POST['ibdid']; 
$batchids = $_POST['batchid'];
$productCodes = $_POST['productCode'];
$quantities = $_POST['quantity'] ;
$weights = $_POST['weight'];
$prices = $_POST['price'];


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

header("Location: ../inventory.php?success=Inventory Batch Details added successfully");
