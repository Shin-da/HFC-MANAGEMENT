<!-- add.inventorybatchdetails.process -->
<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';
// Example data
$productcode = 'p009';
$productdescription = 'New Product';
$category = 'electronics';
$unitweight = 0.5;
$unitprice = 99.99;
// Retrieve Product Details
$productcodes = $_POST['productcode'];
$stmt = $conn->prepare("SELECT * FROM products WHERE productcode IN (".implode(',', $productcodes).")");
$stmt->execute();
$productdetails = $stmt->get_result();

// Insert Data into products table
$stmt = $conn->prepare("INSERT INTO products (productcode, productdescription, category, unitweight, unitprice) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssssd", $productcode, $productdescription, $category, $unitweight, $unitprice);



$stmt->execute();

$stmt = $conn->prepare("INSERT INTO inventorybatchdetails (batchid, productcode, quantity, weight, price) VALUES (?, ?, ?, ?, ?)");
foreach ($productcodes as $key => $productcode) {
    $stmt->bind_param("isiii", $batchid, $productcode, $quantities[$key] ?? 0, $weights[$key] ?? 0, $prices[$key] ?? 0);
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
$dateofarrival = $_POST['dateofarrival'] ?? '';
$encoder = $_POST['encoder'] ?? '';
$description = $_POST['description'] ?? '';
$datestockin = $_POST['datestockin'] ?? '';
$datestockout = $_POST['datestockout'] ?? '';

$stmt = $conn->prepare("INSERT INTO inventoryhistory (batchid, dateofarrival, encoder, description, datestockin, datestockout, totalcost) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("issssss", $batchid, $dateofarrival, $encoder, $description, $datestockin, $datestockout, $totalcost);
$stmt->execute();

// Step 4: Update the inventory (Main Stock) Table
$stmt = $conn->prepare("UPDATE inventory SET onhand = onhand + ? WHERE productcode = ?");
foreach ($productcodes as $key => $productcode) {
    $stmt->bind_param("is", $quantities[$key] ?? 0, $productcode);
    $stmt->execute();
}

$stmt->close();
$conn->close();
?>

