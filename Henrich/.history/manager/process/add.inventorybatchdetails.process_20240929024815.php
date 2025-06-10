<!-- add.inventorybatchdetails.process -->
<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

echo "Received data:<br>";
print_r($_POST);

$ibdid = $_POST['ibdid'][0];
$batchid = $_POST['batchid'][0];
$productcode = $_POST['productcode'][0];
$quantity = $_POST['quantity'][0];
$weight = $_POST['weight'][0];
$price = $_POST['price'][0];

// Retrieve Product Details
$stmt = $conn->prepare("SELECT * FROM products WHERE productcode = ?");
$stmt->bind_param("s", $productcode);
$stmt->execute();
$productdetails = $stmt->get_result();
$product = $productdetails->fetch_assoc();

// Insert Data into inventorybatchdetails table
$stmt = $conn->prepare("INSERT INTO inventorybatchdetails (batchid, productcode, quantity, weight, price) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("isiii", $batchid, $productcode, $quantity, $weight, $price);
if (!$stmt->execute()) {
    echo "Error inserting data: " . $stmt->error;
    exit;
}

// Insert Data into inventory table
$stmt = $conn->prepare("INSERT INTO inventory  (productcode, onhand ) VALUES (?, ?)");
$stmt->bind_param("si", $productcode, $onhand);
if (!$stmt->execute()) {
    echo "Error inserting data: " . $stmt->error;
    exit;
}

// Insert Log into inventoryhistory table
$stmt = $conn->prepare("INSERT INTO inventoryhistory (batchid, productcode, quantity, weight, price, encodedby, encodeddate) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isiiiss", $batchid, $productcode, $quantity, $weight, $price, $_SESSION['username'], date('Y-m-d H:i:s'));
if (!$stmt->execute()) {
    echo "Error inserting data: " . $stmt->error;
    exit;
}

echo "Data inserted successfully!";
?>