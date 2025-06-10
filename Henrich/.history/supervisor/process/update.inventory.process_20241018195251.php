<?php

//fetch data from form add.stockactivitylog.process.php
if (isset($_POST['totalboxes'])) {
    $totalboxes = $_POST['totalboxes'];
} else {
    $totalboxes = '';
}

if (isset($_POST['totalpieces'])) {
    $onhand = $_POST['totalpieces'];
} else {
    $onhand = '';
}

// print out the values of the form fields
echo "totalboxes: ";
echo $totalboxes;
echo "<br>";
echo "onhand: ";
echo $onhand;
echo "<br>";

// Update the stock levels in the inventory table
$stmt = $conn->prepare("UPDATE inventory SET onhand = ?, dateupdated = NOW() WHERE productcode = ?");
$stmt->bind_param("is", $onhand, $productcode);

$stmt2 = $conn->prepare("INSERT INTO inventory (productcode, onhand, dateupdated) VALUES (?, ?, NOW())");
$stmt2->bind_param("si", $productcode, $quantity);

$productcodes = $_POST['productcode'] ?? [];
$quantities = $_POST['totalpieces'] ?? [];
foreach ($productcodes as $key => $productcode) {
    $quantity = $quantities[$key] ?? 0;

    // Check if productcode exists in stock table
    $stmt3 = $conn->prepare("SELECT * FROM inventory  WHERE productcode = ?");
    $stmt3->bind_param("s", $productcode);
    $stmt3->execute();
    $result = $stmt3->get_result();

    if ($result->num_rows > 0) {
        // Productcode exists, update record
        $stmt->execute();
    } else {
        // Productcode does not exist, insert new record
        $stmt2->execute();
    }
}

echo "Stock levels updated successfully!";