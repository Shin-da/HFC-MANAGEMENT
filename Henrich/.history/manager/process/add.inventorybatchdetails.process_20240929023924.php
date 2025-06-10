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

$sql = "INSERT INTO inventorybatchdetails (ibdid, BatchID, ProductCode, Quantity, weight, Price) VALUES ('$ibdid', '$batchid', '$productcode', '$quantity', '$weight', '$price')";

if ($conn->query($sql) === TRUE) {
    echo "Data inserted successfully!";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
echo "Data inserted successfully!";

?>