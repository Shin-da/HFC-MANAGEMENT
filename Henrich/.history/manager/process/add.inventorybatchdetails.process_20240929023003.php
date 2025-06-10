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

echo "IBD ID: " . $ibdid . "<br>";
echo "Batch ID: " . $batchid . "<br>";
echo "Product Code: " . $productcode . "<br>";
echo "Quantity: " . $quantity . "<br>";
echo "Weight: " . $weight . "<br>";
echo "Price: " . $price . "<br>";

// Insert Data into inventoryhistory table
// $stmt = $conn->prepare("INSERT INTO inventoryhistory (batchid, productcode, quantity, weight, price, encodedby, dateencoded) VALUES (?, ?, ?, ?, ?, ?, ?)");
// $stmt->bind_param("isiiiss", $batchid, $productcode, $quantity, $weight, $price, $_SESSION['username'], date('Y-m-d H:i:s'));
// if (!$stmt->execute()) {
//     echo "Error inserting data: " . $stmt->error;
//     exit;
// }

// Insert Data into inventorybatchdetails table
$sql = $conn->prepare(" INSERT INTO inventorybatchdetails (ibdid, batchid, productcode, quantity, weight, price ) VALUES (?, ?, ?, ?, ?, ?)");
$sql->bind_param("iisiiis", $ibdid, $batchid, $productcode, $quantity, $weight, $price);
if (!$sql->execute()) {
    echo "Error inserting data: " . $sql->error;
    exit;
}

echo "Data inserted successfully!";

?>