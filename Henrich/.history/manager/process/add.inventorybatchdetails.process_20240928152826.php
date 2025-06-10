<!-- add.inventorybatchdetails.process -->
<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

$productcodes = $_POST['productcode'];
$stmt = $conn->prepare("SELECT * FROM Products WHERE ProductCode IN (".implode(',', $productcodes).")");
$stmt->execute();
$product_details = $stmt->get_result();

// Step 1: Insert Data into inventorybatchdetails
$batchid = $_POST['batchid'];
$productcodes = $_POST['productcode'];
$quantities = $_POST['quantity'];
$weights = $_POST['weight'];
$prices = $_POST['price'];

$stmt = $conn->prepare("INSERT INTO inventorybatchdetails (batchid, productcode, quantity, weight, price) VALUES (?, ?, ?, ?, ?)");
foreach ($productcodes as $key => $productcode) {
    $stmt->bind_param("isiii", $batchid, $productcode, $quantities[$key], $weights[$key], $prices[$key]);
    $stmt->execute();
}

// Step 2: Calculate the Total Cost
$totalcost = 0;
$stmt = $conn->prepare("SELECT quantity, price FROM inventorybatchdetails WHERE batchid = ?");
$stmt->bind_param("i", $batchid);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $totalcost += $row['quantity'] * $row['price'];
}

// Step 3: Insert Data into inventoryhistory
$dateofarrival = $_POST['dateofarrival'];
$encoder = $_POST['encoder'];
$description = $_POST['description'];
$datestockin = $_POST['datestockin'];
$datestockout = $_POST['datestockout'];

$stmt = $conn->prepare("INSERT INTO inventoryhistory (batchid, dateofarrival, encoder, description, datestockin, datestockout, totalcost) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssssd", $batchid, $dateofarrival, $encoder, $description, $datestockin, $datestockout, $totalcost);
$stmt->execute();

// Step 4: Update the inventory (Main Stock) Table
$stmt = $conn->prepare("UPDATE inventory SET on_hand = on_hand + ? WHERE productcode = ?");
foreach ($productcodes as $key => $productcode) {
    $stmt->bind_param("is", $quantities[$key], $productcode);
    $stmt->execute();
}

$stmt->close();
$conn->close();
?>