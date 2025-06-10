<?php

//fetch data from form add.stockactivitylog.process.php

$productcodes = $_POST['productcode'] ?? [];
$quantities = $_POST['totalpieces'] ?? [];
$productnames = $_POST['productname'] ?? [];

foreach ($productcodes as $key => $productcode) {
    $quantity = $quantities[$key] ?? 0;
    $productname = $productnames[$key] ?? '';
    
    // Get productcategory from productlist table
    $stmt = $conn->prepare("SELECT productcategory FROM productlist WHERE productcode = ?");
    $stmt->bind_param("s", $productcode);
    $stmt->execute();
    $result = $stmt->get_result();
    $productcategory = $result->fetch_assoc()['productcategory'] ?? '';

    // Check if productcode exists in stock table
    $stmt2 = $conn->prepare("SELECT * FROM inventory  WHERE productcode = ?");
    $stmt2->bind_param("s", $productcode);
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    if ($result2->num_rows > 0) {
        // Productcode exists, update record
        $stmt3 = $conn->prepare("UPDATE inventory SET onhand = ?, productname = ?, productcategory = ?, dateupdated = NOW() WHERE productcode = ?");
        $stmt3->bind_param("issi", $quantity, $productname, $productcategory, $productcode);
        $stmt3->execute();
    } else {
        // Productcode does not exist, insert new record
        $stmt4 = $conn->prepare("INSERT INTO inventory (productcode, onhand, productname, productcategory, dateupdated) VALUES (?, ?, ?, ?, NOW())");
        $stmt4->bind_param("isss", $productcode, $quantity, $productname, $productcategory);
        $stmt4->execute();
    }
}

echo "Stock levels updated successfully!";

