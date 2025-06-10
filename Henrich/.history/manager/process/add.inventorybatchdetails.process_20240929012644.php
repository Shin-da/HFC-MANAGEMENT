<!-- add.inventorybatchdetails.process -->
<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

echo "Received data:<br>";
print_r($_POST);

$batchid = $_POST['batchid'];
$productcode = $_POST['productcode'];
$quantity = $_POST['quantity'];
$weight = $_POST['weight'];
$price = $_POST['price'];
$dateupdated = date('Y-m-d H:i:s');

// Retrieve Product Details
// $stmt = $conn->prepare("SELECT * FROM products WHERE productcode = ?");
// $stmt->bind_param("s", $productcode);
// if (!$stmt->execute()) {
//     echo "Error executing query: " . $stmt->error;
//     exit;
// }
// $productdetails = $stmt->get_result();
// $product = $productdetails->fetch_assoc();

// Insert Log into inventoryhistory table
// $stmt = $conn->prepare("INSERT INTO inventoryhistory (batchid, productcode, quantity, weight, price, encodedby, dateencoded) VALUES (?, ?, ?, ?, ?, ?, ?)");
// $stmt->bind_param("isiiiss", $batchid, $productcode, $quantity, $weight, $price, $_SESSION['username'], date('Y-m-d H:i:s'));
// if (!$stmt->execute()) {
//     echo "Error inserting data: " . $stmt->error;
//     exit;
// }

// Insert Data into inventorybatchdetails table
$stmt = $conn->prepare("INSERT INTO inventorybatchdetails (batchid, productcode, quantity, weight, price) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("isiii", $batchid, $productcode, $quantity, $weight, $price);
if (!$stmt->execute()) {
    echo "Error inserting data: " . $stmt->error;
    exit;
}

// Update Data in inventory table
// $stmt = $conn->prepare("INSERT INTO inventory (iid, productcode, productdescription, category, onhand, dateupdated) VALUES (?, ?, ?, ?, ?, ?)");
// $stmt->bind_param("sssssi", $iid, $productcode, $productdescription, $category, $onhand, $dateupdated);
// $stmt->execute();

echo "Data inserted successfully!";
?>