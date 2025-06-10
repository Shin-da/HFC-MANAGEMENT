<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

echo "Received data:<br>";
print_r($_POST);

$ibdid = $_POST['ibdid'];
$batchid = $_POST['batchid'];
$productcode = $_POST['productcode'];
$quantity = $_POST['quantity'];
$weight = $_POST['weight'];
$price = $_POST['price'];

echo "IBD ID: " . $ibdid . "<br>";
echo "Batch ID: " . $batchid . "<br>";
echo "Product Code: " . $productcode . "<br>";
echo "Quantity: " . $quantity . "<br>";
echo "Weight: " . $weight . "<br>";
echo "Price: " . $price . "<br>";

$sql = "INSERT INTO inventorybatchdetails (ibdid, batchid, productcode, quantity, weight, price) VALUES ('$ibdid', '$batchid', '$productcode', '$quantity', '$weight', '$price')";

if ($conn->query($sql) === TRUE) {
    echo "Data inserted successfully!";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
echo "Data inserted successfully!";

?>