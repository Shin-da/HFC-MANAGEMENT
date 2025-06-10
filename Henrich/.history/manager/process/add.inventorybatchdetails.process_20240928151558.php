<!-- add.inventorybatchdetails.process -->
<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';
$product_codes = $_POST['product_code'];
$stmt = $conn->prepare("SELECT * FROM Products WHERE ProductCode IN (".implode(',', $product_codes).")");
$stmt->execute();
$product_details = $stmt->get_result();

// Step 1: Insert Data into inventorybatchdetails
$batch_id = $_POST['batch_id'];
$product_codes = $_POST['product_code'];
$quantities = $_POST['quantity'];
$weights = $_POST['weight'];
$prices = $_POST['price'];

$stmt = $conn->prepare("INSERT INTO inventorybatchdetails (batch_id, product_code, quantity, weight, price) VALUES (?, ?, ?, ?, ?)");
foreach ($product_codes as $key => $product_code) {
    $stmt->bind_param("isiii", $batch_id, $product_code, $quantities[$key], $weights[$key], $prices[$key]);
    $stmt->execute();
}

// Step 2: Calculate the Total Cost
$total_cost = 0;
$stmt = $conn->prepare("SELECT quantity, price FROM inventorybatchdetails WHERE batch_id = ?");
$stmt->bind_param("i", $batch_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $total_cost += $row['quantity'] * $row['price'];
}

// Step 3: Insert Data into inventoryhistory
$date_of_arrival = $_POST['date_of_arrival'];
$encoder = $_POST['encoder'];
$description = $_POST['description'];
$date_stock_in = $_POST['date_stock_in'];
$date_stock_out = $_POST['date_stock_out'];

$stmt = $conn->prepare("INSERT INTO inventoryhistory (batch_id, date_of_arrival, encoder, description, date_stock_in, date_stock_out, total_cost) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssssd", $batch_id, $date_of_arrival, $encoder, $description, $date_stock_in, $date_stock_out, $total_cost);
$stmt->execute();

// Step 4: Update the inventory (Main Stock) Table
$stmt = $conn->prepare("UPDATE inventory SET on_hand = on_hand + ? WHERE product_code = ?");
foreach ($product_codes as $key => $product_code) {
    $stmt->bind_param("is", $quantities[$key], $product_code);
    $stmt->execute();
}