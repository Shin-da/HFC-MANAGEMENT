<!-- add.inventorybatchdetails.process -->
<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

echo "Received data:<br>";
print_r($_POST);

// Retrieve Product Details
if (isset($_POST['productcode'])) {
    $productcodes = $_POST['productcode'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE productcode IN (?)");
    $stmt->bind_param("s", implode(',', $productcodes));
    $stmt->execute();
    $productdetails = $stmt->get_result();
    $products = array();
    while ($row = $productdetails->fetch_assoc()) {
        $products[$row['productcode']] = $row;
    }
} else {
    echo "Error: Product code not set.";
    exit;
}

// Calculate Total Cost
$batchid = $_POST['batchid'];
$quantities = $_POST['quantity'];
$prices = $_POST['price'];
$totalCost = 0;
foreach ($productcodes as $key => $productcode) {
    $totalCost += ($quantities[$key] ?? 0) * ($prices[$key] ?? 0);
}

// Insert Data into inventorybatchdetails table
$stmt = $conn->prepare("INSERT INTO inventorybatchdetails (batchid, productcode, quantity, weight, price) VALUES (?, ?, ?, ?, ?)");
foreach ($productcodes as $key => $productcode) {
    $quantity = $quantities[$key];
    $weight = $products[$productcode]['unitweight'] * $quantity;
    $price = $prices[$key];
    $stmt->bind_param("isiii", $batchid, $productcode, $quantity, $weight, $price);
    if (!$stmt->execute()) {
        echo "Error inserting data: " . $stmt->error;
        exit;
    }
}

// Insert Data into inventory table
$stmt = $conn->prepare("INSERT INTO inventory (productcode, quantity, weight) VALUES (?, ?, ?)");
foreach ($productcodes as $key => $productcode) {
    $quantity = $quantities[$key];
    $weight = $products[$productcode]['unitweight'] * $quantity;
    $stmt->bind_param("isi", $productcode, $quantity, $weight);
    if (!$stmt->execute()) {
        echo "Error inserting data: " . $stmt->error;
        exit;
    }
}

// Insert Log into inventoryhistory table
$stmt = $conn->prepare("INSERT INTO inventoryhistory (batchid, productcode, quantity, weight, price, encodedby, encodeddate) VALUES (?, ?, ?, ?, ?, ?, ?)");
foreach ($productcodes as $key => $productcode) {
    $quantity = $quantities[$key];
    $weight = $products[$productcode]['unitweight'] * $quantity;
    $price = $prices[$key];
    $encodedby = $_SESSION['username'];
    $encodeddate = date('Y-m-d H:i:s');
    $stmt->bind_param("isiiiss", $batchid, $productcode, $quantity, $weight, $price, $encodedby, $encodeddate);
    if (!$stmt->execute()) {
        echo "Error inserting data: " . $stmt->error;
        exit;
    }
}

echo "Data inserted successfully!";
?>