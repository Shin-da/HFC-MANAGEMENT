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


// Insert Data into inventorybatchdetails table
$stmt = $conn->prepare("INSERT INTO inventorybatchdetails (ibdid, batchid, productcode, quantity, weight, price) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isiiii", $ibdid, $batchid, $productcode, $quantity, $weight, $price); 
$productcode = mysqli_real_escape_string($conn, $productcode);
if (!$stmt->execute()) {
    echo "Error inserting data: " . $stmt->error;
    exit;
} 
print_r($_POST);
// Insert Data into inventoryhistory table
$dateEncoded = date('Y-m-d H:i:s');

$stmt = $conn->prepare("INSERT INTO inventoryhistory (batchid, productcode, quantity, weight, price, encodedby, dateencoded) VALUES (?, ?, ?, ?, ?, ?, ?)");

if (!$stmt->execute()) {
    echo "Error inserting data: " . $stmt->error;
    exit;
}

// Update Data into inventory table
$stmt = $conn->prepare( "UPDATE inventory SET onhand = onhand + ? WHERE productcode = ?");
$stmt->bind_param("is", $onhand, $productcode);
if (!$stmt->execute()) {
    echo "Error updating data: " . $stmt->error;
    exit;
}


echo "Data inserted successfully!";
?>