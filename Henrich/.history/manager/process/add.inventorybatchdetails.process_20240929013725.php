<!-- add.inventorybatchdetails.process -->
<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

echo "Received data:<br>";
print_r($_POST);

$ibdid = $_POST['ibdid'];
$batchid = $_POST['batchid'][0];
$productcode = $_POST['productcode'][0];
$quantity = $_POST['quantity'][0];
$weight = $_POST['weight'][0];
$price = $_POST['price'][0];
$dateupdated = date('Y-m-d H:i:s');

echo "Batch ID: " . $batchid . "<br>";
echo "Product Code: " . $productcode . "<br>";
echo "Quantity: " . $quantity . "<br>";
echo "Weight: " . $weight . "<br>";
echo "Price: " . $price . "<br>";

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
$stmt = $conn->prepare("INSERT INTO inventorybatchdetails (ibdid, batchid, productcode, quantity, weight, price) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("isiii", $batchid, $productcode, $quantity, $weight, $price);


// Update Data in inventory table
// $stmt = $conn->prepare("INSERT INTO inventory (iid, productcode, productdescription, category, onhand, dateupdated) VALUES (?, ?, ?, ?, ?, ?)");
// $stmt->bind_param("sssssi", $iid, $productcode, $productdescription, $category, $onhand, $dateupdated);
// $stmt->execute();

echo "Data inserted successfully!";
?>