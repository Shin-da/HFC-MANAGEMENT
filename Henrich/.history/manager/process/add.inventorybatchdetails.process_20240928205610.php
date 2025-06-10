<!-- add.inventorybatchdetails.process -->
<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

// Retrieve Product Details
if (isset($_POST['productcode'])) {
    $productcodes = $_POST['productcode'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE productcode IN (?)");
    $stmt->bind_param("s", implode(',', $productcodes));
    $stmt->execute();
    $productdetails = $stmt->get_result();
} else {
    echo "Error: Product code not set.";
    exit;
}


// Step 2: Calculate the Total Cost
$batchid = $_POST['batchid'];
$productcodes = $_POST['productcode'];
$quantities = $_POST['quantity'];
$weights = $_POST['weight'];
$prices = $_POST['price'];

$totalCost = 0;
foreach ($productcodes as $key => $productcode) {
    $totalCost += ($quantities[$key] ?? 0) * ($prices[$key] ?? 0);
}

// Step 3: Insert Data into inventorybatch table
$stmt = $conn->prepare("INSERT INTO inventorybatch (batchid, totalcost) VALUES (?, ?)");
$stmt->bind_param("is", $batchid, $totalCost);
$stmt->execute();

// Step 4: Insert Data into inventorybatchdetails table
$stmt = $conn->prepare("INSERT INTO inventorybatchdetails (batchid, productcode, quantity, weight, price) VALUES (?, ?, ?, ?, ?)");
foreach ($productcodes as $key => $productcode) {
    $stmt->bind_param("isiii", $batchid, $productcode, $quantities[$key] ?? 0, $weights[$key] ?? 0, $prices[$key] ?? 0);
    $stmt->execute();
}

$stmt->close();
$conn->close();
?>

