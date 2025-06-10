<!-- add.inventorybatchdetails.process -->
<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

$ibdids = $_POST['ibdid']; 
$batchids = $_POST['batchID'];
$productCodes = $_POST['productCode'];
$quantities = $_POST['quantity'] ;
$weights = $_POST['weight'];
$prices = $_POST['price'];

// Insert batchid into inventoryhistory if it does not already exist
$stmt = $conn->prepare("INSERT IGNORE INTO inventoryhistory (batchid) VALUES (?)");

// Execute the insert statement for each batchid
foreach ($batchids as $batchid) {
    $stmt->bind_param("s", $batchid);
    if ($stmt->execute()) {
        echo "Batchid $batchid inserted into inventoryhistory successfully\n";
    } else {
        echo "Error inserting batchid $batchid into inventoryhistory: " . $stmt->error . "\n";
    }
}

// Insert into inventorybatchdetails
$stmt = $conn->prepare("INSERT INTO inventorybatchdetails (ibdid, batchid, productcode, quantity, weight, price) VALUES (?, ?, ?, ?, ?, ?)");

foreach ($batchids as $key => $batchid) {
    $productCode = $productCodes[$key];
    $quantity = $quantities[$key];
    $weight = $weights[$key];
    $price = $prices[$key];

    $stmt->bind_param("isiiid", $ibdids[$key], $batchid, $productCode, $quantity, $weight, $price);

    if ($stmt->execute()) {
        echo "Batchid $batchid inserted into inventorybatchdetails successfully\n";
    } else {
        echo "Error inserting batchid $batchid into inventorybatchdetails: " . $stmt->error . "\n";
    }
}

$stmt->close();
$conn->close();

header("Location: ../inventory.php?success=Inventory Batch Details added successfully");
