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

if (isset($_POST['productname'])) {
    $productname = $_POST['productname'];
} else {
    $productname = '';
}

// print out the values of the form fields

echo "totalboxes: ";
echo $totalboxes;
echo "<br>";
echo "onhand: ";
echo $onhand;
echo "<br>";
echo "productname: ";
echo $productname;
echo "<br>";

// Update the stock levels in the inventory table
$stmt = $conn->prepare("UPDATE inventory SET onhand = ?, productname = ?, productcategory = ?, dateupdated = NOW() WHERE productcode = ?");
$stmt->bind_param("issi", $onhand, $productname, $productcategory, $productcode);

$stmt2 = $conn->prepare("INSERT INTO inventory (productcode, onhand, productname, productcategory, dateupdated) VALUES (?, ?, ?, ?, NOW())");
$stmt2->bind_param("isss", $productcode, $quantity, $productname, $productcategory);

$productcodes = $_POST['productcode'] ?? [];
$quantities = $_POST['totalpieces'] ?? [];
foreach ($productcodes as $key => $productcode) {
    $quantity = $quantities[$key] ?? 0;

    // Get productname and productcategory from productlist table
    $stmt4 = $conn->prepare("SELECT productname, productcategory FROM productlist WHERE productcode = ?");
    $stmt4->bind_param("s", $productcode);
    $stmt4->execute();
    $result4 = $stmt4->get_result();
    $row = $result4->fetch_assoc();
    $productname = $row['productname'] ?? '';
    $productcategory = $row['productcategory'] ?? '';

    // Check if productcode exists in stock table
    $stmt3 = $conn->prepare("SELECT * FROM inventory WHERE productcode = ?");
    $stmt3->bind_param("s", $productcode);
    $stmt3->execute();
    $result = $stmt3->get_result();

    if ($result->num_rows > 0) {
        // Productcode exists, update record
        $stmt->bind_param("issi", $quantity, $productname, $productcategory, $productcode);
        $stmt->execute();
    } else {
        // Productcode does not exist, insert new record
        $stmt2->bind_param("isss", $productcode, $quantity, $productname, $productcategory);
        $stmt2->execute();
    }
}

echo "Stock levels updated successfully!";