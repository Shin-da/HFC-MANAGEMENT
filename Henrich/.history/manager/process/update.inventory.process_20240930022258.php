<?php
// Update the stock levels in the inventory table
$stmt = $conn->prepare("UPDATE stock SET onhand = onhand + ? WHERE productcode = ?");
$stmt->bind_param("is", $quantity, $productcode);

$stmt2 = $conn->prepare("INSERT INTO inventory (productcode, onhand) VALUES (?, ?)");
$stmt2->bind_param("si", $productcode, $quantity);

$productcodes = $_POST['productcode'];
$quantities = $_POST['quantity'];

foreach ($productcodes as $key => $productcode) {
    $quantity = $quantities[$key];

    // Check if productcode exists in stock table
    $stmt3 = $conn->prepare("SELECT * FROM  WHERE productcode = ?");
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
?>