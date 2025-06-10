<!-- add.inventorybatchdetails.process -->
<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

// Retrieve Product Details
if (isset($_POST['productcode'])) {
    $productcodes = $_POST['productcode'];
    $placeholders = implode(',', array_fill(0, count($productcodes), '?'));
    $stmt = $conn->prepare("SELECT * FROM products WHERE productcode IN ($placeholders)");
    $refs = array();
    foreach ($productcodes as $key => $value) {
        $refs[] = &$productcodes[$key];
    }
    $stmt->bind_param(str_repeat('s', count($productcodes)), ...$refs);
    $stmt->execute();
    $productdetails = $stmt->get_result();
} else {
    echo "Error: Product code not set.";
    exit;
}

// Insert Data into products table
$stmt = $conn->prepare("INSERT IGNORE INTO products (productcode, productdescription, category, unitweight, unitprice) VALUES (?, ?, ?, ?, ?)");
foreach ($productcodes as $key => $productcode) {
    $productdescription = $_POST['productdescription'][$key];
    $category = $_POST['category'][$key];
    $unitweight = $_POST['unitweight'][$key];
    $unitprice = $_POST['unitprice'][$key];
    $stmt->bind_param("ssssd", $productcode, $productdescription, $category, $unitweight, $unitprice);
    $stmt->execute();
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

