<!-- add.inventorybatchdetails.process -->
<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';
$product_codes = $_POST['product_code'];
$stmt = $conn->prepare("SELECT * FROM Products WHERE ProductCode IN (".implode(',', $product_codes).")");
$stmt->execute();
$product_details = $stmt->get_result();

// Step 1: Insert Data into inventorybatchdetails
$batchid = $_POST['batchid'];
$product_codes = $_POST['product_code'];
$quantities = $_POST['quantity'];
$weights = $_POST['weight'];
$prices = $_POST['price'];

$stmt = $conn->prepare("INSERT INTO inventorybatchdetails (batchid, product_code, quantity, weight, price) VALUES (?, ?, ?, ?, ?)");
foreach ($product_codes as $key => $product_code) {
    $stmt->bind_param("isiii", $batchid, $product_code, $quantities[$key], $weights[$key], $prices[$key]);
    $stmt->execute();
}

// Step 2: Calculate the Total Cost
$total_cost = 0;
$stmt = $conn->prepare("SELECT quantity, price FROM inventorybatchdetails WHERE batchid = ?");
$stmt->bind_param("i", $batchid);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $total_cost += $row['quantity'] * $row['price'];
}

// Step 3: Insert Data into inventoryhistory
$dateofarrival = $_POST['dateofarrival'];
$encoder = $_POST['encoder'];
$description = $_POST['description'];
$datestockin = $_POST['datestockin'];
$date_stock_out = $_POST['date_stock_out'];

$stmt = $conn->prepare("INSERT INTO inventoryhistory (batchid, dateofarrival, encoder, description, datestockin, date_stock_out, total_cost) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssssd", $batchid, $dateofarrival, $encoder, $description, $datestockin, $date_stock_out, $total_cost);
$stmt->execute();

// Step 4: Update the inventory (Main Stock) Table
$stmt = $conn->prepare("UPDATE inventory SET on_hand = on_hand + ? WHERE product_code = ?");
foreach ($product_codes as $key => $product_code) {
    $stmt->bind_param("is", $quantities[$key], $product_code);
    $stmt->execute();
}

$stmt->close();
$conn->close();
?>