<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

echo "Received data:<br>";
print_r($_POST);

$ibdid = $_POST['ibdid'][0];
$batchid = $_POST['batchid'][0];
$productid = $_POST['productcode'][0];
$quantity = $_POST['quantity'][0];
$weight = $_POST['weight'][0];
$price = $_POST['price'][0];
$dateEncoded = date('Y-m-d H:i:s');

echo "IBD ID: " . $ibdid . "<br>";
echo "Batch ID: " . $batchid . "<br>";
echo "Product ID: " . $productid . "<br>";
echo "Quantity: " . $quantity . "<br>";
echo "Weight: " . $weight . "<br>";
echo "Price: " . $price . "<br>";

// Insert Data into inventoryhistory table
$stmt = $conn->prepare("INSERT INTO inventoryhistory (batchid, productid, quantity, weight, price, encodedby, dateencoded) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iiiiiss", $batchid, $productid, $quantity, $weight, $price, $_SESSION['username'], $dateEncoded);
if (!$stmt->execute()) {
    echo "Error inserting data: " . $stmt->error;
    exit;
}

// Insert Data into inventorybatchdetails table
$stmt = $conn->prepare("INSERT INTO inventorybatchdetails (ibdid, batchid, productid, quantity, weight, price) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iiiiii", $ibdid, $batchid, $productid, $quantity, $weight, $price);
if (!$stmt->execute()) {
    echo "Error inserting data: " . $stmt->error;
    exit;
} else {
    echo "Data inserted successfully into inventorybatchdetails table!";
}

echo "Data inserted successfully!";

?>