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
$dateupdated = date('Y-m-d H:i:s');

echo "IBD ID: " . $ibdid . "<br>";
echo "Batch ID: " . $batchid . "<br>";
echo "Product Code: " . $productcode . "<br>";
echo "Quantity: " . $quantity . "<br>";
echo "Weight: " . $weight . "<br>";
echo "Price: " . $price . "<br>";
// Insert Data into inventorybatchdetails table
$stmt = $conn->prepare("INSERT INTO inventorybatchdetails (ibdid, batchid, productcode, quantity, weight, price) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iiiiii", $ibdid, $batchid, $productcode, $quantity, $weight, $price);
if (!$stmt->execute()) {
    echo "Error inserting data: " . $stmt->error;
    exit;
} else {
    echo "Data inserted successfully into inventorybatchdetails table!";
}


echo "Data inserted successfully!";

?>