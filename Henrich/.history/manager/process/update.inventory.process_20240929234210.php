<?php

// Update the inventory table
$stmt = $conn->prepare("UPDATE inventory SET onhand = onhand + ? WHERE productcode = ?");
$stmt->bind_param("is", $quantity, $productcode);

$stmt2 = $conn->prepare("INSERT INTO inventory (productcode, onhand) VALUES (?, ?)");
$stmt2->bind_param("si", $productcode, $quantity);

foreach ($productcodes as $key => $productcode) {
    $quantity = $quantities[$key];

    // Check if productcode exists in inventory table
    $stmt3 = $conn->prepare("SELECT * FROM inventory WHERE productcode = ?");
    $stmt3->bind_param("s", $productcode);
    $stmt3->execute();
    $result = $stmt3->get_result();

    if ($result->num_rows > 0) {
        // Productcode exists, update record
        if (!$stmt->execute()) {
            echo "Error updating data in inventory table: " . $stmt->error;
            exit;
        }
    } else {
        // Productcode does not exist, insert new record
        if (!$stmt2->execute()) {
            echo "Error inserting data into inventory table: " . $stmt2->error;
            exit;
        }
    }
}

echo "Stock levels updated successfully!";
?>